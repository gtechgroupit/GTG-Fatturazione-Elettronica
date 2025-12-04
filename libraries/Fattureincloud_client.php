<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client per l'integrazione con FattureInCloud.it
 *
 * FattureInCloud è un servizio di fatturazione elettronica che offre:
 * - Invio fatture al Sistema di Interscambio
 * - Conservazione sostitutiva
 * - Ricezione fatture passive
 * - Gestione completa della contabilità
 *
 * API Documentation: https://developers.fattureincloud.it/
 *
 * @author GTech Group IT
 * @version 1.0.0
 */
class Fattureincloud_client
{
    /**
     * @var object CodeIgniter instance
     */
    protected $CI;

    /**
     * @var string Ambiente (test/produzione)
     */
    protected $ambiente;

    /**
     * @var array Configurazione
     */
    protected $config;

    /**
     * @var string Ultimo errore
     */
    protected $last_error;

    /**
     * @var mixed Ultima risposta
     */
    protected $last_response;

    /**
     * @var string Access Token
     */
    protected $access_token;

    /**
     * @var array Rate limit info dall'ultima richiesta
     */
    protected $rate_limit_info = [];

    /**
     * @var int Numero massimo di retry per rate limiting
     */
    protected $max_retries = 3;

    /**
     * API Base URL
     */
    const API_BASE_URL = 'https://api-v2.fattureincloud.it';

    /**
     * OAuth URLs
     */
    const OAUTH_AUTHORIZE_URL = 'https://api-v2.fattureincloud.it/oauth/authorize';
    const OAUTH_TOKEN_URL = 'https://api-v2.fattureincloud.it/oauth/token';

    /**
     * Costruttore
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->loadConfig();
    }

    /**
     * Carica la configurazione
     */
    protected function loadConfig()
    {
        $this->ambiente = get_option('fe_ambiente') ?: 'test';

        $this->config = [
            'client_id'     => get_option('fe_fic_client_id') ?: '',
            'client_secret' => get_option('fe_fic_client_secret') ?: '',
            'access_token'  => get_option('fe_fic_access_token') ?: '',
            'refresh_token' => get_option('fe_fic_refresh_token') ?: '',
            'company_id'    => get_option('fe_fic_company_id') ?: '',
            'token_expires' => get_option('fe_fic_token_expires') ?: 0,
        ];

        $this->access_token = $this->config['access_token'];

        // Refresh token se scaduto
        if ($this->config['token_expires'] && $this->config['token_expires'] < time()) {
            $this->refreshAccessToken();
        }
    }

    /**
     * Genera l'URL per l'autorizzazione OAuth
     *
     * @param string $redirect_uri URL di callback
     * @param string $state Stato per CSRF protection
     * @return string
     */
    public function getAuthorizationUrl($redirect_uri, $state = '')
    {
        $params = [
            'response_type' => 'code',
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $redirect_uri,
            'scope'         => 'entity.clients:a entity.suppliers:a products:a issued_documents:a received_documents:a settings:a situation:r',
        ];

        if ($state) {
            $params['state'] = $state;
        }

        return self::OAUTH_AUTHORIZE_URL . '?' . http_build_query($params);
    }

    /**
     * Ottiene l'access token dal codice di autorizzazione
     *
     * @param string $code Codice di autorizzazione
     * @param string $redirect_uri URL di callback
     * @return bool
     */
    public function getAccessToken($code, $redirect_uri)
    {
        $data = [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'code'          => $code,
            'redirect_uri'  => $redirect_uri,
        ];

        $response = $this->makeRequest(self::OAUTH_TOKEN_URL, $data, 'POST', false);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['access_token'])) {
            $this->access_token = $result['access_token'];

            // Salva i token
            update_option('fe_fic_access_token', $result['access_token']);
            update_option('fe_fic_refresh_token', $result['refresh_token'] ?? '');
            update_option('fe_fic_token_expires', time() + ($result['expires_in'] ?? 3600));

            return true;
        }

        $this->last_error = $result['error_description'] ?? 'Errore durante l\'autenticazione';
        return false;
    }

    /**
     * Aggiorna l'access token
     *
     * @return bool
     */
    protected function refreshAccessToken()
    {
        if (empty($this->config['refresh_token'])) {
            return false;
        }

        $data = [
            'grant_type'    => 'refresh_token',
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'refresh_token' => $this->config['refresh_token'],
        ];

        $response = $this->makeRequest(self::OAUTH_TOKEN_URL, $data, 'POST', false);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['access_token'])) {
            $this->access_token = $result['access_token'];

            update_option('fe_fic_access_token', $result['access_token']);
            if (isset($result['refresh_token'])) {
                update_option('fe_fic_refresh_token', $result['refresh_token']);
            }
            update_option('fe_fic_token_expires', time() + ($result['expires_in'] ?? 3600));

            return true;
        }

        return false;
    }

    /**
     * Ottiene la lista delle aziende dell'utente
     *
     * @return array|false
     */
    public function getCompanies()
    {
        $endpoint = self::API_BASE_URL . '/user/companies';

        $response = $this->makeRequest($endpoint, null, 'GET');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        return $result['data']['companies'] ?? [];
    }

    /**
     * Invia una fattura tramite FattureInCloud
     *
     * NOTA: FattureInCloud API v2 NON supporta l'invio di XML esterni.
     * I documenti devono essere creati tramite le API di FIC prima di poter essere inviati al SDI.
     * Usare createAndSendInvoice() oppure createIssuedDocument() + sendDocumentToSDI()
     *
     * @deprecated Questo metodo non funziona con FIC API v2. Usare createAndSendInvoice()
     * @param string $xml_content Contenuto XML della fattura (non supportato)
     * @param string $filename Nome del file
     * @param bool $signed Se l'XML è già firmato
     * @return array|false Sempre false con messaggio di errore
     */
    public function sendInvoice($xml_content, $filename, $signed = false)
    {
        $this->last_error = 'FattureInCloud non supporta l\'invio di XML esterni. ' .
            'I documenti devono essere creati tramite le API di FIC. ' .
            'Utilizzare createAndSendInvoice() con i dati strutturati della fattura, ' .
            'oppure createIssuedDocument() seguito da sendDocumentToSDI().';

        fe_log('errore_fic', $this->last_error . ' - Tentativo di invio XML: ' . $filename);

        return false;
    }

    /**
     * Crea prima un documento su FIC e poi lo invia al SDI
     *
     * @param array $invoice_data Dati della fattura nel formato FIC
     * @param bool $dry_run Se true, valida senza inviare realmente al SDI
     * @return array|false
     */
    public function createAndSendInvoice($invoice_data, $dry_run = false)
    {
        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return false;
        }

        // Prima crea il documento
        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents';

        $data = ['data' => $invoice_data];

        $response = $this->makeRequest($endpoint, json_encode($data), 'POST');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $this->last_error = $result['error']['message'] ?? 'Errore creazione documento';
            return false;
        }

        $document_id = $result['data']['id'] ?? null;

        if (!$document_id) {
            $this->last_error = 'ID documento non ricevuto';
            return false;
        }

        // Ora invia al SDI
        return $this->sendDocumentToSDI($document_id);
    }

    /**
     * Invia un documento esistente al SDI
     *
     * NOTA: Richiede un piano a pagamento su FattureInCloud.
     * Il documento deve essere stato creato tramite le API di FIC.
     *
     * @param int $document_id ID del documento FIC
     * @param bool $dry_run Se true, esegue tutti i controlli senza inviare realmente al SDI
     * @return array|false
     */
    public function sendDocumentToSDI($document_id, $dry_run = false)
    {
        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return false;
        }

        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents/' . $document_id . '/e_invoice/send';

        // Prepara i dati con opzione dry_run
        $data = [];
        if ($dry_run) {
            $data['options'] = ['dry_run' => true];
        }

        $response = $this->makeRequest($endpoint, json_encode($data), 'POST');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $this->last_error = $result['error']['message'] ?? 'Errore invio SDI';
            if (isset($result['error']['validation_result'])) {
                $this->last_error .= ' - Validazione: ' . implode(', ', $result['error']['validation_result']);
            }
            return false;
        }

        return [
            'success'            => true,
            'identificativo_sdi' => $result['data']['ei_raw_response']['IdentificativoSdI'] ?? $document_id,
            'id_fic'             => $document_id,
            'message'            => $dry_run ? 'Validazione completata (dry-run)' : 'Fattura inviata al SDI',
            'dry_run'            => $dry_run,
            'raw_response'       => $result,
        ];
    }

    /**
     * Controlla lo stato di una fattura elettronica
     *
     * @param int $document_id ID del documento FIC
     * @return array Stato della fattura con chiavi: stato, ei_status, message, raw_response
     */
    public function checkInvoiceStatus($document_id)
    {
        if (empty($this->config['company_id'])) {
            return [
                'stato'     => FE_STATO_INVIATA,
                'ei_status' => 'unknown',
                'message'   => 'Company ID non configurato',
            ];
        }

        // Usa fieldset=detailed per ottenere ei_status
        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents/' . $document_id . '?fieldset=detailed';

        $response = $this->makeRequest($endpoint, null, 'GET');

        if ($response === false) {
            return [
                'stato'     => FE_STATO_INVIATA,
                'ei_status' => 'unknown',
                'message'   => 'Impossibile verificare lo stato: ' . ($this->last_error ?? 'errore sconosciuto'),
            ];
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            return [
                'stato'     => FE_STATO_INVIATA,
                'ei_status' => 'error',
                'message'   => $result['error']['message'] ?? '',
            ];
        }

        $ei_status = $result['data']['ei_status'] ?? 'not_sent';

        // Mappa completa degli stati FIC ai nostri stati interni
        // Riferimento: https://developers.fattureincloud.it/docs/guides/e-invoice-management
        $stato_mapping = [
            // Stati iniziali e di elaborazione
            'attempt'           => FE_STATO_INVIATA,        // In corso di invio
            'missing'           => FE_STATO_DA_INVIARE,     // Fattura mancante
            'not_sent'          => FE_STATO_DA_INVIARE,     // Non ancora inviata
            'sent'              => FE_STATO_INVIATA,        // Inviata al SDI
            'pending'           => FE_STATO_INVIATA,        // In attesa di risposta
            'processing'        => FE_STATO_INVIATA,        // SDI sta consegnando

            // Stati finali positivi
            'delivered'         => FE_STATO_CONSEGNATA,     // Consegnata al destinatario
            'accepted'          => FE_STATO_ACCETTATA,      // Accettata dal cliente
            'manual_accepted'   => FE_STATO_ACCETTATA,      // Accettata manualmente

            // Stati finali negativi
            'not_delivered'     => FE_STATO_NON_CONSEGNATA, // Non consegnata
            'rejected'          => FE_STATO_RIFIUTATA,      // Rifiutata dal cliente
            'manual_rejected'   => FE_STATO_RIFIUTATA,      // Rifiutata manualmente
            'discarded'         => FE_STATO_SCARTATA,       // Scartata dal SDI
            'error'             => FE_STATO_SCARTATA,       // Errore durante l'invio

            // Stati speciali
            'no_response'       => FE_STATO_DECORRENZA_TERMINI, // Nessuna risposta entro i termini
        ];

        // Gestione stato sconosciuto
        $stato_interno = $stato_mapping[$ei_status] ?? FE_STATO_INVIATA;

        return [
            'stato'        => $stato_interno,
            'ei_status'    => $ei_status,
            'message'      => $result['data']['ei_status_extended'] ?? '',
            'e_invoice'    => $result['data']['e_invoice'] ?? false,
            'raw_response' => $result,
        ];
    }

    /**
     * Crea un documento emesso su FattureInCloud
     *
     * NOTA: Questo metodo crea solo il documento, non lo invia al SDI.
     * Per inviare al SDI usare sendDocumentToSDI() dopo la creazione.
     *
     * @param array $invoice_data Dati della fattura nel formato FIC
     * @param array $options Opzioni aggiuntive (es: fix_payments => true)
     * @return array|false Documento creato con ID o false
     */
    public function createIssuedDocument($invoice_data, $options = [])
    {
        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return false;
        }

        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents';

        $request_data = ['data' => $invoice_data];

        // Aggiungi opzioni se presenti (es: fix_payments per aggiustare automaticamente i pagamenti)
        if (!empty($options)) {
            $request_data['options'] = $options;
        }

        $response = $this->makeRequest($endpoint, json_encode($request_data), 'POST');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $this->last_error = $result['error']['message'] ?? 'Errore creazione documento';
            if (isset($result['error']['validation_result'])) {
                $this->last_error .= ' - ' . implode(', ', $result['error']['validation_result']);
            }
            return false;
        }

        return [
            'success'  => true,
            'id'       => $result['data']['id'] ?? null,
            'type'     => $result['data']['type'] ?? null,
            'number'   => $result['data']['number'] ?? null,
            'e_invoice' => $result['data']['e_invoice'] ?? false,
            'raw_response' => $result,
        ];
    }

    /**
     * Verifica l'XML di una e-invoice prima dell'invio al SDI
     *
     * @param int $document_id ID del documento FIC
     * @return array Risultato verifica con chiavi: valid, errors
     */
    public function verifyEInvoiceXml($document_id)
    {
        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return ['valid' => false, 'errors' => [$this->last_error]];
        }

        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents/' . $document_id . '/e_invoice/xml/verify';

        $response = $this->makeRequest($endpoint, null, 'GET');

        if ($response === false) {
            return [
                'valid'  => false,
                'errors' => [$this->last_error ?? 'Errore durante la verifica'],
            ];
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            return [
                'valid'  => false,
                'errors' => $result['error']['validation_result'] ?? [$result['error']['message'] ?? 'Errore validazione'],
            ];
        }

        return [
            'valid'        => true,
            'errors'       => [],
            'raw_response' => $result,
        ];
    }

    /**
     * Scarica l'XML generato di una e-invoice emessa
     *
     * @param int $document_id ID del documento FIC
     * @param bool $include_attachment Include allegato nell'XML
     * @return string|false Contenuto XML o false
     */
    public function getEInvoiceXml($document_id, $include_attachment = false)
    {
        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return false;
        }

        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents/' . $document_id . '/e_invoice/xml';

        if ($include_attachment) {
            $endpoint .= '?include_attachment=true';
        }

        $response = $this->makeRequest($endpoint, null, 'GET');

        if ($response === false) {
            return false;
        }

        // La risposta dovrebbe essere direttamente XML
        if (strpos($response, '<?xml') === 0 || strpos($response, '<p:FatturaElettronica') !== false) {
            return $response;
        }

        // Se invece è JSON con errore
        $result = json_decode($response, true);
        if (isset($result['error'])) {
            $this->last_error = $result['error']['message'] ?? 'Errore download XML';
            return false;
        }

        return $response;
    }

    /**
     * Ottiene il motivo di rifiuto/scarto di una e-invoice
     *
     * @param int $document_id ID del documento FIC
     * @return array|false Motivo rifiuto con chiavi: reason, code, date
     */
    public function getEInvoiceRejectionReason($document_id)
    {
        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return false;
        }

        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents/' . $document_id . '/e_invoice/rejection_reason';

        $response = $this->makeRequest($endpoint, null, 'GET');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $this->last_error = $result['error']['message'] ?? 'Errore recupero motivo rifiuto';
            return false;
        }

        return [
            'reason' => $result['data']['reason'] ?? '',
            'code'   => $result['data']['code'] ?? '',
            'date'   => $result['data']['date'] ?? '',
            'raw_response' => $result,
        ];
    }

    /**
     * Calcola i totali di un nuovo documento (preview senza creare)
     *
     * @param array $invoice_data Dati della fattura
     * @return array|false Totali calcolati
     */
    public function getNewDocumentTotals($invoice_data)
    {
        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return false;
        }

        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents/totals';

        $response = $this->makeRequest($endpoint, json_encode(['data' => $invoice_data]), 'POST');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $this->last_error = $result['error']['message'] ?? 'Errore calcolo totali';
            return false;
        }

        return $result['data'] ?? false;
    }

    /**
     * Scarica le fatture passive (acquisto) con paginazione completa
     *
     * @param string|null $from_date Data di inizio (formato Y-m-d o strtotime compatibile)
     * @param string|null $to_date Data di fine (formato Y-m-d o strtotime compatibile)
     * @param bool $download_xml Se scaricare anche il contenuto XML (più lento)
     * @param int $max_pages Numero massimo di pagine da scaricare (0 = tutte)
     * @return array Lista di fatture passive
     */
    public function downloadPassiveInvoices($from_date = null, $to_date = null, $download_xml = true, $max_pages = 0)
    {
        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return [];
        }

        $base_endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/received_documents';

        $params = [
            'type'     => 'expense',
            'per_page' => 100, // Massimo consentito dall'API
            'fieldset' => 'detailed', // Per avere ei_raw con IdentificativoSdI
        ];

        // Costruisce la query filtro con sintassi SQL-like
        // Le date devono essere racchiuse tra apici singoli
        $filters = [];

        if ($from_date) {
            $filters[] = "date >= '" . date('Y-m-d', strtotime($from_date)) . "'";
        }

        if ($to_date) {
            $filters[] = "date <= '" . date('Y-m-d', strtotime($to_date)) . "'";
        }

        if (!empty($filters)) {
            $params['q'] = implode(' and ', $filters);
        }

        $invoices = [];
        $current_page = 1;
        $last_page = 1;

        do {
            $params['page'] = $current_page;
            $endpoint = $base_endpoint . '?' . http_build_query($params);

            $response = $this->makeRequest($endpoint, null, 'GET');

            if ($response === false) {
                fe_log('errore_fic', 'Errore scaricamento fatture passive pagina ' . $current_page . ': ' . $this->last_error);
                break;
            }

            $result = json_decode($response, true);

            if (isset($result['error'])) {
                $this->last_error = $result['error']['message'] ?? 'Errore sconosciuto';
                break;
            }

            // Aggiorna info paginazione dalla prima risposta
            if ($current_page === 1) {
                $last_page = $result['last_page'] ?? 1;
                $total = $result['total'] ?? 0;

                if ($total === 0) {
                    return [];
                }

                fe_log('fic_download', "Trovate {$total} fatture passive su {$last_page} pagine");
            }

            // Processa i documenti della pagina corrente
            if (!empty($result['data'])) {
                foreach ($result['data'] as $doc) {
                    $invoice_data = [
                        'id_fic'             => $doc['id'],
                        'identificativo_sdi' => $doc['ei_raw']['IdentificativoSdI'] ?? $doc['id'],
                        'nome_file'          => !empty($doc['attachment_url']) ? basename($doc['attachment_url']) : 'fattura_' . $doc['id'] . '.xml',
                        'data_ricezione'     => $doc['date'] ?? date('Y-m-d'),
                        'description'        => $doc['description'] ?? '',
                        'amount_gross'       => $doc['amount_gross'] ?? 0,
                        'entity_name'        => $doc['entity']['name'] ?? '',
                        'entity_vat'         => $doc['entity']['vat_number'] ?? '',
                    ];

                    // Scarica XML solo se richiesto (operazione più lenta)
                    if ($download_xml) {
                        $xml_content = $this->downloadReceivedDocumentXml($doc['id']);
                        if ($xml_content) {
                            $invoice_data['xml_content'] = $xml_content;
                        }
                    }

                    $invoices[] = $invoice_data;
                }
            }

            $current_page++;

            // Rispetta il limite massimo di pagine se specificato
            if ($max_pages > 0 && $current_page > $max_pages) {
                fe_log('fic_download', "Raggiunto limite massimo di {$max_pages} pagine");
                break;
            }

        } while ($current_page <= $last_page);

        return $invoices;
    }

    /**
     * Lista i documenti ricevuti (senza scaricare XML)
     *
     * @param array $filters Filtri opzionali (type, q, sort, etc.)
     * @param int $page Pagina da richiedere
     * @param int $per_page Elementi per pagina (max 100)
     * @return array|false
     */
    public function listReceivedDocuments($filters = [], $page = 1, $per_page = 50)
    {
        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return false;
        }

        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/received_documents';

        $params = array_merge([
            'type'     => 'expense',
            'per_page' => min($per_page, 100),
            'page'     => $page,
        ], $filters);

        $endpoint .= '?' . http_build_query($params);

        $response = $this->makeRequest($endpoint, null, 'GET');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $this->last_error = $result['error']['message'] ?? 'Errore lista documenti';
            return false;
        }

        return [
            'data'         => $result['data'] ?? [],
            'current_page' => $result['current_page'] ?? 1,
            'last_page'    => $result['last_page'] ?? 1,
            'total'        => $result['total'] ?? 0,
            'per_page'     => $result['per_page'] ?? $per_page,
        ];
    }

    /**
     * Scarica l'XML di un documento ricevuto
     *
     * @param int $document_id ID del documento
     * @return string|false
     */
    protected function downloadReceivedDocumentXml($document_id)
    {
        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/received_documents/' . $document_id . '/e_invoice/xml';

        $response = $this->makeRequest($endpoint, null, 'GET');

        if ($response === false) {
            return false;
        }

        // La risposta potrebbe essere direttamente XML o JSON con XML encodato
        if (strpos($response, '<?xml') === 0) {
            return $response;
        }

        $result = json_decode($response, true);
        if (isset($result['data']['xml'])) {
            return base64_decode($result['data']['xml']);
        }

        return false;
    }

    /**
     * Effettua una richiesta all'API con gestione errori e retry
     *
     * @param string $endpoint URL endpoint
     * @param mixed $data Dati da inviare
     * @param string $method Metodo HTTP
     * @param bool $use_auth Se usare l'autenticazione Bearer
     * @param int $retry_count Contatore retry interno
     * @return string|false
     */
    protected function makeRequest($endpoint, $data = null, $method = 'GET', $use_auth = true, $retry_count = 0)
    {
        $ch = curl_init();

        $headers = [
            'Accept: application/json',
        ];

        if ($use_auth && $this->access_token) {
            $headers[] = 'Authorization: Bearer ' . $this->access_token;
        }

        if ($method === 'POST' || $method === 'PUT') {
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HEADER         => true, // Per ottenere gli header di risposta
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $full_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $error = curl_error($ch);

        curl_close($ch);

        // Separa header e body
        $response_headers = substr($full_response, 0, $header_size);
        $response = substr($full_response, $header_size);

        // Estrai informazioni rate limit dagli header
        $this->parseRateLimitHeaders($response_headers);

        if ($error) {
            $this->last_error = 'Errore cURL: ' . $error;
            fe_log('errore_fic', $this->last_error);
            return false;
        }

        // Gestione 401 - Token scaduto
        if ($http_code === 401) {
            if ($this->refreshAccessToken()) {
                return $this->makeRequest($endpoint, $data, $method, $use_auth, $retry_count);
            }
            $this->last_error = 'Autenticazione fallita. Rieffettua l\'autorizzazione OAuth.';
            return false;
        }

        // Gestione 403 - Quota superata o permessi insufficienti
        if ($http_code === 403) {
            $result = json_decode($response, true);
            $error_msg = $result['error']['message'] ?? 'Accesso negato';

            // Verifica se è un errore di quota
            if (stripos($error_msg, 'quota') !== false || stripos($error_msg, 'limit') !== false) {
                $retry_after = $this->getRetryAfterFromHeaders($response_headers);
                $this->last_error = 'Quota API superata. ' .
                    ($retry_after ? "Riprova tra {$retry_after} secondi." : 'Riprova più tardi.');
            } else {
                $this->last_error = 'Accesso negato: ' . $error_msg;
            }

            fe_log('errore_fic', $this->last_error);
            return false;
        }

        // Gestione 429 - Rate limiting con retry automatico
        if ($http_code === 429) {
            if ($retry_count < $this->max_retries) {
                $retry_after = $this->getRetryAfterFromHeaders($response_headers);
                $wait_time = $retry_after ?: pow(2, $retry_count + 1); // Exponential backoff: 2, 4, 8 secondi

                fe_log('fic_rate_limit', "Rate limit raggiunto. Attesa di {$wait_time} secondi (tentativo " . ($retry_count + 1) . "/{$this->max_retries})");

                sleep($wait_time);
                return $this->makeRequest($endpoint, $data, $method, $use_auth, $retry_count + 1);
            }

            $this->last_error = 'Troppe richieste. Rate limit superato dopo ' . $this->max_retries . ' tentativi.';
            fe_log('errore_fic', $this->last_error);
            return false;
        }

        // Gestione errori generici
        if ($http_code < 200 || $http_code >= 300) {
            $result = json_decode($response, true);
            $error_msg = $result['error']['message'] ?? $response;
            $this->last_error = 'Errore HTTP ' . $http_code . ': ' . $error_msg;
            return false;
        }

        $this->last_response = $response;
        return $response;
    }

    /**
     * Estrae le informazioni di rate limit dagli header HTTP
     *
     * @param string $headers Header HTTP della risposta
     */
    protected function parseRateLimitHeaders($headers)
    {
        $this->rate_limit_info = [];

        // Pattern per estrarre gli header di rate limit
        $patterns = [
            'hourly_remaining' => '/RateLimit-HourlyRemaining:\s*(\d+)/i',
            'hourly_limit'     => '/RateLimit-HourlyLimit:\s*(\d+)/i',
            'monthly_remaining' => '/RateLimit-MonthlyRemaining:\s*(\d+)/i',
            'monthly_limit'    => '/RateLimit-MonthlyLimit:\s*(\d+)/i',
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $headers, $matches)) {
                $this->rate_limit_info[$key] = (int) $matches[1];
            }
        }
    }

    /**
     * Estrae il valore Retry-After dagli header
     *
     * @param string $headers Header HTTP della risposta
     * @return int|null Secondi da attendere o null
     */
    protected function getRetryAfterFromHeaders($headers)
    {
        if (preg_match('/Retry-After:\s*(\d+)/i', $headers, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Ottiene le informazioni correnti sul rate limit
     *
     * @return array
     */
    public function getRateLimitInfo()
    {
        return $this->rate_limit_info;
    }

    /**
     * Ottiene l'ultimo errore
     *
     * @return string|null
     */
    public function getLastError()
    {
        return $this->last_error;
    }

    /**
     * Ottiene l'ultima risposta
     *
     * @return mixed
     */
    public function getLastResponse()
    {
        return $this->last_response;
    }

    /**
     * Test della connessione
     *
     * @return bool
     */
    public function testConnection()
    {
        if (empty($this->access_token)) {
            $this->last_error = 'Access Token non configurato';
            return false;
        }

        $companies = $this->getCompanies();

        if ($companies === false) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se l'autorizzazione OAuth è completa
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return !empty($this->access_token);
    }

    /**
     * Ottiene le informazioni del provider
     *
     * @return array
     */
    public static function getProviderInfo()
    {
        return [
            'id'          => 'fattureincloud',
            'name'        => 'FattureInCloud.it',
            'description' => 'Gestionale di fatturazione completo con fatturazione elettronica, conservazione e molto altro.',
            'logo'        => 'assets/images/providers/fattureincloud.png',
            'website'     => 'https://www.fattureincloud.it/',
            'features'    => [
                'Invio fatture al SDI',
                'Conservazione sostitutiva a norma',
                'Ricezione fatture passive',
                'Gestionale completo integrato',
                'App mobile iOS/Android',
                'Reportistica avanzata',
                'Multi-azienda',
            ],
            'pricing' => [
                'Prova gratuita 30 giorni',
                'Piani a partire da €5/mese',
                'Piano Forfettari gratuito',
            ],
            'requirements' => [
                'Account su FattureInCloud.it',
                'App registrata nel Developer Portal',
                'OAuth 2.0 autorizzato',
            ],
            'oauth' => true,
            'fields' => [
                [
                    'name'        => 'fe_fic_client_id',
                    'label'       => 'Client ID',
                    'type'        => 'text',
                    'required'    => true,
                    'placeholder' => 'Il tuo Client ID OAuth',
                    'help'        => 'Ottienilo dal Developer Portal di FattureInCloud',
                ],
                [
                    'name'        => 'fe_fic_client_secret',
                    'label'       => 'Client Secret',
                    'type'        => 'password',
                    'required'    => true,
                    'placeholder' => 'Il tuo Client Secret OAuth',
                ],
                [
                    'name'        => 'fe_fic_company_id',
                    'label'       => 'Company ID',
                    'type'        => 'text',
                    'required'    => true,
                    'placeholder' => 'ID dell\'azienda su FattureInCloud',
                    'help'        => 'Verrà popolato automaticamente dopo l\'autorizzazione OAuth',
                ],
            ],
        ];
    }
}

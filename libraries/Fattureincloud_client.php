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
     * @param string $xml_content Contenuto XML della fattura
     * @param string $filename Nome del file
     * @param bool $signed Se l'XML è già firmato
     * @return array|false Risultato dell'invio o false
     */
    public function sendInvoice($xml_content, $filename, $signed = false)
    {
        $this->last_error = null;
        $this->last_response = null;

        if (empty($this->access_token)) {
            $this->last_error = 'Access Token non configurato. Effettua l\'autorizzazione OAuth.';
            return false;
        }

        if (empty($this->config['company_id'])) {
            $this->last_error = 'Company ID non configurato';
            return false;
        }

        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents/e_invoice/send';

        // Prepara i dati per l'invio
        $data = [
            'data' => [
                'xml' => base64_encode($xml_content),
            ],
        ];

        $response = $this->makeRequest($endpoint, json_encode($data), 'POST');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $this->last_error = $result['error']['message'] ?? 'Errore sconosciuto';
            return false;
        }

        return [
            'success'            => true,
            'identificativo_sdi' => $result['data']['id'] ?? '',
            'id_fic'             => $result['data']['id'] ?? '',
            'message'            => 'Fattura inviata con successo tramite FattureInCloud',
            'raw_response'       => $result,
        ];
    }

    /**
     * Crea prima un documento su FIC e poi lo invia al SDI
     *
     * @param array $invoice_data Dati della fattura
     * @return array|false
     */
    public function createAndSendInvoice($invoice_data)
    {
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
     * @param int $document_id ID del documento
     * @return array|false
     */
    public function sendDocumentToSDI($document_id)
    {
        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents/' . $document_id . '/e_invoice/send';

        $response = $this->makeRequest($endpoint, '{}', 'POST');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $this->last_error = $result['error']['message'] ?? 'Errore invio SDI';
            return false;
        }

        return [
            'success'            => true,
            'identificativo_sdi' => $result['data']['ei_raw_response']['IdentificativoSdI'] ?? $document_id,
            'id_fic'             => $document_id,
            'message'            => 'Fattura inviata al SDI',
            'raw_response'       => $result,
        ];
    }

    /**
     * Controlla lo stato di una fattura
     *
     * @param string $identificativo_sdi ID SDI o ID FIC
     * @return array|false
     */
    public function checkInvoiceStatus($identificativo_sdi)
    {
        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/issued_documents/' . $identificativo_sdi;

        $response = $this->makeRequest($endpoint, null, 'GET');

        if ($response === false) {
            return [
                'stato'   => FE_STATO_INVIATA,
                'message' => 'Impossibile verificare lo stato',
            ];
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            return [
                'stato'   => FE_STATO_INVIATA,
                'message' => $result['error']['message'] ?? '',
            ];
        }

        $ei_status = $result['data']['ei_status'] ?? 'pending';

        // Mappa gli stati di FIC ai nostri stati
        $stato_mapping = [
            'pending'            => FE_STATO_INVIATA,
            'sent'               => FE_STATO_INVIATA,
            'delivered'          => FE_STATO_CONSEGNATA,
            'not_delivered'      => FE_STATO_NON_CONSEGNATA,
            'accepted'           => FE_STATO_ACCETTATA,
            'rejected'           => FE_STATO_RIFIUTATA,
            'discarded'          => FE_STATO_SCARTATA,
            'terms_expired'      => FE_STATO_DECORRENZA_TERMINI,
            'delivery_impossible' => FE_STATO_IMPOSSIBILITA_RECAPITO,
        ];

        return [
            'stato'       => $stato_mapping[$ei_status] ?? FE_STATO_INVIATA,
            'message'     => $result['data']['ei_status_extended'] ?? '',
            'raw_response' => $result,
        ];
    }

    /**
     * Scarica le fatture passive (acquisto)
     *
     * @param string|null $from_date Data di inizio
     * @param string|null $to_date Data di fine
     * @return array
     */
    public function downloadPassiveInvoices($from_date = null, $to_date = null)
    {
        if (empty($this->config['company_id'])) {
            return [];
        }

        $endpoint = self::API_BASE_URL . '/c/' . $this->config['company_id'] . '/received_documents';

        $params = [
            'type'     => 'expense',
            'per_page' => 100,
        ];

        if ($from_date) {
            $params['q'] = 'date >= ' . date('Y-m-d', strtotime($from_date));
        }

        $endpoint .= '?' . http_build_query($params);

        $response = $this->makeRequest($endpoint, null, 'GET');

        if ($response === false) {
            return [];
        }

        $result = json_decode($response, true);

        if (isset($result['error']) || empty($result['data'])) {
            return [];
        }

        $invoices = [];
        foreach ($result['data'] as $doc) {
            // Scarica l'XML se disponibile
            $xml_content = $this->downloadReceivedDocumentXml($doc['id']);

            if ($xml_content) {
                $invoices[] = [
                    'identificativo_sdi' => $doc['ei_raw']['IdentificativoSdI'] ?? $doc['id'],
                    'nome_file'          => $doc['attachment_url'] ? basename($doc['attachment_url']) : 'fattura_' . $doc['id'] . '.xml',
                    'xml_content'        => $xml_content,
                    'data_ricezione'     => $doc['date'] ?? date('Y-m-d H:i:s'),
                ];
            }
        }

        return $invoices;
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
     * Effettua una richiesta all'API
     *
     * @param string $endpoint URL endpoint
     * @param mixed $data Dati da inviare
     * @param string $method Metodo HTTP
     * @param bool $use_auth Se usare l'autenticazione Bearer
     * @return string|false
     */
    protected function makeRequest($endpoint, $data = null, $method = 'GET', $use_auth = true)
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
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            $this->last_error = 'Errore cURL: ' . $error;
            fe_log('errore_fic', $this->last_error);
            return false;
        }

        if ($http_code === 401) {
            // Token scaduto, prova a rinnovarlo
            if ($this->refreshAccessToken()) {
                return $this->makeRequest($endpoint, $data, $method, $use_auth);
            }
            $this->last_error = 'Autenticazione fallita. Rieffettua l\'autorizzazione OAuth.';
            return false;
        }

        if ($http_code < 200 || $http_code >= 300) {
            $this->last_error = 'Errore HTTP ' . $http_code . ': ' . $response;
            return false;
        }

        $this->last_response = $response;
        return $response;
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

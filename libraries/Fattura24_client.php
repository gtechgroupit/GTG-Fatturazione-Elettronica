<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client per l'integrazione con Fattura24.it
 *
 * Fattura24 è un servizio di fatturazione elettronica che offre:
 * - Invio fatture al Sistema di Interscambio
 * - Conservazione sostitutiva a norma
 * - Ricezione fatture passive
 * - Dashboard di monitoraggio
 *
 * API Documentation: https://www.fattura24.com/api-fatturazione-elettronica/
 *
 * @author GTech Group IT
 * @version 1.0.0
 */
class Fattura24_client
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
     * API Endpoints
     */
    const API_BASE_URL = 'https://www.app.fattura24.com/api/v0.3';

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
            'api_key' => get_option('fe_fattura24_api_key') ?: '',
        ];
    }

    /**
     * Invia una fattura tramite Fattura24
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

        if (empty($this->config['api_key'])) {
            $this->last_error = 'API Key Fattura24 non configurata';
            return false;
        }

        // Fattura24 accetta direttamente l'XML FatturaPA
        $endpoint = self::API_BASE_URL . '/SaveDocument';

        $data = [
            'apiKey'       => $this->config['api_key'],
            'documentXml'  => base64_encode($xml_content),
            'outputType'   => 'xml', // Risposta in XML
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response === false) {
            return false;
        }

        // Parsa la risposta XML di Fattura24
        $result = $this->parseResponse($response);

        if (!$result['success']) {
            $this->last_error = $result['error'] ?? 'Errore sconosciuto';
            return false;
        }

        // Ora invia al SDI
        $send_result = $this->sendToSDI($result['id']);

        if ($send_result === false) {
            return false;
        }

        return [
            'success'            => true,
            'identificativo_sdi' => $send_result['idSdi'] ?? $result['id'],
            'id_fattura24'       => $result['id'],
            'message'            => 'Fattura inviata con successo tramite Fattura24',
            'raw_response'       => $send_result,
        ];
    }

    /**
     * Invia il documento salvato al SDI
     *
     * @param string $document_id ID del documento su Fattura24
     * @return array|false
     */
    protected function sendToSDI($document_id)
    {
        $endpoint = self::API_BASE_URL . '/SendDocumentToSdi';

        $data = [
            'apiKey'     => $this->config['api_key'],
            'documentId' => $document_id,
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response === false) {
            return false;
        }

        $result = $this->parseResponse($response);

        if (!$result['success']) {
            $this->last_error = $result['error'] ?? 'Errore invio SDI';
            return false;
        }

        return $result;
    }

    /**
     * Controlla lo stato di una fattura
     *
     * @param string $identificativo_sdi ID SDI o ID Fattura24
     * @return array|false
     */
    public function checkInvoiceStatus($identificativo_sdi)
    {
        $endpoint = self::API_BASE_URL . '/GetSdiStatus';

        $data = [
            'apiKey'     => $this->config['api_key'],
            'documentId' => $identificativo_sdi,
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response === false) {
            return [
                'stato'   => FE_STATO_INVIATA,
                'message' => 'Impossibile verificare lo stato',
            ];
        }

        $result = $this->parseResponse($response);

        // Mappa gli stati di Fattura24 ai nostri stati
        $stato_mapping = [
            'INVIATO'           => FE_STATO_INVIATA,
            'CONSEGNATO'        => FE_STATO_CONSEGNATA,
            'NON_CONSEGNATO'    => FE_STATO_NON_CONSEGNATA,
            'ACCETTATO'         => FE_STATO_ACCETTATA,
            'RIFIUTATO'         => FE_STATO_RIFIUTATA,
            'SCARTATO'          => FE_STATO_SCARTATA,
            'DECORRENZA_TERMINI' => FE_STATO_DECORRENZA_TERMINI,
            'IMPOSSIBILITA_RECAPITO' => FE_STATO_IMPOSSIBILITA_RECAPITO,
            'IN_ELABORAZIONE'   => FE_STATO_INVIATA,
        ];

        $stato = $result['status'] ?? 'INVIATO';

        return [
            'stato'       => $stato_mapping[$stato] ?? FE_STATO_INVIATA,
            'message'     => $result['statusDescription'] ?? '',
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
        $endpoint = self::API_BASE_URL . '/GetReceivedDocuments';

        $data = [
            'apiKey' => $this->config['api_key'],
        ];

        if ($from_date) {
            $data['dateFrom'] = date('d/m/Y', strtotime($from_date));
        }
        if ($to_date) {
            $data['dateTo'] = date('d/m/Y', strtotime($to_date));
        }

        $response = $this->makeRequest($endpoint, $data);

        if ($response === false) {
            return [];
        }

        $result = $this->parseResponse($response);

        if (!$result['success'] || empty($result['documents'])) {
            return [];
        }

        $invoices = [];
        foreach ($result['documents'] as $doc) {
            // Scarica il contenuto XML
            $xml_content = $this->downloadDocument($doc['id']);

            if ($xml_content) {
                $invoices[] = [
                    'identificativo_sdi' => $doc['idSdi'] ?? $doc['id'],
                    'nome_file'          => $doc['fileName'] ?? 'fattura_' . $doc['id'] . '.xml',
                    'xml_content'        => $xml_content,
                    'data_ricezione'     => $doc['receivedDate'] ?? date('Y-m-d H:i:s'),
                ];
            }
        }

        return $invoices;
    }

    /**
     * Scarica il contenuto di un documento
     *
     * @param string $document_id ID del documento
     * @return string|false
     */
    protected function downloadDocument($document_id)
    {
        $endpoint = self::API_BASE_URL . '/GetDocument';

        $data = [
            'apiKey'     => $this->config['api_key'],
            'documentId' => $document_id,
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response === false) {
            return false;
        }

        $result = $this->parseResponse($response);

        if (!$result['success'] || empty($result['documentXml'])) {
            return false;
        }

        return base64_decode($result['documentXml']);
    }

    /**
     * Scarica le notifiche SDI
     *
     * @param string|null $from_date Data di inizio
     * @return array
     */
    public function downloadNotifications($from_date = null)
    {
        $endpoint = self::API_BASE_URL . '/GetNotifications';

        $data = [
            'apiKey' => $this->config['api_key'],
        ];

        if ($from_date) {
            $data['dateFrom'] = date('d/m/Y', strtotime($from_date));
        }

        $response = $this->makeRequest($endpoint, $data);

        if ($response === false) {
            return [];
        }

        $result = $this->parseResponse($response);

        if (!$result['success'] || empty($result['notifications'])) {
            return [];
        }

        $notifications = [];
        foreach ($result['notifications'] as $notif) {
            $notifications[] = [
                'identificativo_sdi' => $notif['idSdi'] ?? '',
                'tipo_notifica'      => $notif['type'] ?? '',
                'nome_file'          => $notif['fileName'] ?? '',
                'xml_content'        => base64_decode($notif['content'] ?? ''),
                'data_ricezione'     => $notif['date'] ?? date('Y-m-d H:i:s'),
            ];
        }

        return $notifications;
    }

    /**
     * Effettua una richiesta all'API
     *
     * @param string $endpoint URL endpoint
     * @param array $data Dati da inviare
     * @return string|false
     */
    protected function makeRequest($endpoint, $data)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/xml',
            ],
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if ($errno) {
            $this->last_error = 'Errore cURL (' . $errno . '): ' . $error;
            fe_log('errore_fattura24', $this->last_error . ' - Endpoint: ' . $endpoint);
            return false;
        }

        if ($http_code < 200 || $http_code >= 300) {
            $this->last_error = 'Errore HTTP ' . $http_code . ' - Endpoint: ' . $endpoint;
            if ($response) {
                $this->last_error .= ' - Risposta: ' . substr($response, 0, 500);
            }
            fe_log('errore_fattura24', $this->last_error);
            return false;
        }

        $this->last_response = $response;
        return $response;
    }

    /**
     * Parsa la risposta XML di Fattura24
     *
     * @param string $response Risposta XML
     * @return array
     */
    protected function parseResponse($response)
    {
        $doc = new DOMDocument();
        @$doc->loadXML($response);

        if (!$doc->documentElement) {
            return ['success' => false, 'error' => 'Risposta XML non valida: ' . substr($response, 0, 200)];
        }

        $xpath = new DOMXPath($doc);

        // Fattura24 usa returnCode (1 = successo, -1 o altri = errore) e description
        $returnCode = $xpath->evaluate('string(//returnCode)');
        $description = $xpath->evaluate('string(//description)');
        $docId = $xpath->evaluate('string(//docId)');
        $docNumber = $xpath->evaluate('string(//docNumber)');

        // Campi legacy/alternativi
        $success = $xpath->evaluate('string(//success)');
        $error = $xpath->evaluate('string(//error)');
        $id = $xpath->evaluate('string(//id)');
        $idSdi = $xpath->evaluate('string(//idSdi)');
        $status = $xpath->evaluate('string(//status)');
        $statusDescription = $xpath->evaluate('string(//statusDescription)');

        // Determina successo: returnCode=1 oppure success=true
        $isSuccess = ($returnCode === '1' || $returnCode === '0') || ($success === 'true' || $success === '1');

        $result = [
            'success'           => $isSuccess,
            'returnCode'        => $returnCode ?: null,
            'description'       => $description ?: null,
            'error'             => $error ?: ($returnCode === '-1' ? $description : null),
            'id'                => $id ?: $docId ?: null,
            'docId'             => $docId ?: null,
            'docNumber'         => $docNumber ?: null,
            'idSdi'             => $idSdi ?: null,
            'status'            => $status ?: null,
            'statusDescription' => $statusDescription ?: null,
        ];

        // Parsa documenti se presenti
        $documents = $xpath->query('//document');
        if ($documents->length > 0) {
            $result['documents'] = [];
            foreach ($documents as $doc) {
                $result['documents'][] = [
                    'id'           => $xpath->evaluate('string(id)', $doc),
                    'idSdi'        => $xpath->evaluate('string(idSdi)', $doc),
                    'fileName'     => $xpath->evaluate('string(fileName)', $doc),
                    'receivedDate' => $xpath->evaluate('string(receivedDate)', $doc),
                ];
            }
        }

        // Parsa notifiche se presenti
        $notifications = $xpath->query('//notification');
        if ($notifications->length > 0) {
            $result['notifications'] = [];
            foreach ($notifications as $notif) {
                $result['notifications'][] = [
                    'idSdi'    => $xpath->evaluate('string(idSdi)', $notif),
                    'type'     => $xpath->evaluate('string(type)', $notif),
                    'fileName' => $xpath->evaluate('string(fileName)', $notif),
                    'content'  => $xpath->evaluate('string(content)', $notif),
                    'date'     => $xpath->evaluate('string(date)', $notif),
                ];
            }
        }

        // Parsa documentXml se presente
        $documentXml = $xpath->evaluate('string(//documentXml)');
        if ($documentXml) {
            $result['documentXml'] = $documentXml;
        }

        return $result;
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
        if (empty($this->config['api_key'])) {
            $this->last_error = 'API Key non configurata';
            return false;
        }

        // Fa una richiesta di test per verificare l'API Key
        $endpoint = self::API_BASE_URL . '/TestKey';

        $data = [
            'apiKey' => $this->config['api_key'],
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response === false) {
            return false;
        }

        $result = $this->parseResponse($response);

        // Fattura24 usa returnCode: 1 = successo, -1 = errore
        if (isset($result['returnCode'])) {
            if ($result['returnCode'] == 1 || $result['returnCode'] == '1') {
                return true;
            } else {
                $this->last_error = $result['description'] ?? 'Errore sconosciuto da Fattura24';
                return false;
            }
        }

        return $result['success'] ?? false;
    }

    /**
     * Gestisce il webhook per la ricezione delle notifiche
     *
     * @param string $payload Payload ricevuto
     * @param string|null $signature Firma (opzionale)
     * @return array|false
     */
    public function handleWebhook($payload, $signature = null)
    {
        // Fattura24 può inviare notifiche via webhook
        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Prova come XML
            $doc = new DOMDocument();
            if ($doc->loadXML($payload)) {
                $data = $this->parseResponse($payload);
            } else {
                $this->last_error = 'Payload non valido';
                return false;
            }
        }

        return $data;
    }

    /**
     * Ottiene le informazioni del provider
     *
     * @return array
     */
    public static function getProviderInfo()
    {
        return [
            'id'          => 'fattura24',
            'name'        => 'Fattura24.it',
            'description' => 'Servizio di fatturazione elettronica completo con conservazione sostitutiva inclusa.',
            'logo'        => 'assets/images/providers/fattura24.png',
            'website'     => 'https://www.fattura24.com/',
            'features'    => [
                'Invio fatture al SDI',
                'Conservazione sostitutiva a norma (inclusa)',
                'Ricezione fatture passive',
                'Dashboard di monitoraggio',
                'Notifiche email automatiche',
                'Supporto tecnico italiano',
            ],
            'pricing' => [
                'Piano gratuito disponibile',
                'Piani a partire da €4.90/mese',
                'Nessun costo per fattura',
            ],
            'requirements' => [
                'Account su Fattura24.com',
                'API Key (generabile dal pannello)',
            ],
            'fields' => [
                [
                    'name'        => 'fe_fattura24_api_key',
                    'label'       => 'API Key',
                    'type'        => 'text',
                    'required'    => true,
                    'placeholder' => 'La tua API Key di Fattura24',
                    'help'        => 'Puoi trovare la tua API Key nel pannello di Fattura24 sotto Impostazioni > API',
                ],
            ],
        ];
    }
}

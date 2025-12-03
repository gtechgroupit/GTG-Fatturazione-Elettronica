<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client per l'invio e la ricezione di fatture elettroniche tramite SDI
 *
 * Supporta diversi provider:
 * - Aruba (FatturaPA)
 * - InfoCert (Legalinvoice)
 * - Fatture in Cloud
 * - API personalizzate
 * - Modalità test
 *
 * @author GTech Group IT
 * @version 1.0.0
 */
class Sdi_client
{
    /**
     * @var object CodeIgniter instance
     */
    protected $CI;

    /**
     * @var string Provider attivo
     */
    protected $provider;

    /**
     * @var string Ambiente (test/produzione)
     */
    protected $ambiente;

    /**
     * @var array Configurazione API
     */
    protected $config;

    /**
     * @var string Ultimo errore
     */
    protected $last_error;

    /**
     * @var array Risposta dell'ultima chiamata
     */
    protected $last_response;

    /**
     * Endpoint per i vari provider
     */
    const ENDPOINTS = [
        'aruba' => [
            'test' => 'https://ws.fatturazioneelettronica.aruba.it/services/invoice/upload',
            'prod' => 'https://ws.fatturazioneelettronica.aruba.it/services/invoice/upload',
            'notifications' => 'https://ws.fatturazioneelettronica.aruba.it/services/invoice/getByFilename',
            'passive' => 'https://ws.fatturazioneelettronica.aruba.it/services/invoice/listInvoices',
        ],
        'infocert' => [
            'test' => 'https://fattura-pa-test.infocert.it/api/v1/invoices',
            'prod' => 'https://fattura-pa.infocert.it/api/v1/invoices',
        ],
        'fattureincloud' => [
            'test' => 'https://api-v2.fattureincloud.it/c/{company_id}/issued_e_invoices',
            'prod' => 'https://api-v2.fattureincloud.it/c/{company_id}/issued_e_invoices',
        ],
    ];

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
        $this->provider = get_option('fe_provider') ?: 'test';
        $this->ambiente = get_option('fe_ambiente') ?: 'test';

        $this->config = [
            'endpoint'   => get_option('fe_api_endpoint') ?: '',
            'username'   => get_option('fe_api_username') ?: '',
            'password'   => get_option('fe_api_password') ?: '',
            'api_key'    => get_option('fe_api_key') ?: '',
            'api_secret' => get_option('fe_api_secret') ?: '',
        ];
    }

    /**
     * Invia una fattura elettronica allo SDI
     *
     * @param string $xml_content Contenuto XML della fattura
     * @param string $filename Nome del file
     * @param bool $signed Se l'XML è già firmato
     * @return array|false Risultato dell'invio o false in caso di errore
     */
    public function sendInvoice($xml_content, $filename, $signed = false)
    {
        $this->last_error = null;
        $this->last_response = null;

        // Log dell'invio
        fe_log('invio_fattura', "Invio fattura: {$filename}");

        switch ($this->provider) {
            case 'aruba':
                return $this->sendViaAruba($xml_content, $filename, $signed);

            case 'infocert':
                return $this->sendViaInfoCert($xml_content, $filename, $signed);

            case 'fattureincloud':
                return $this->sendViaFattureInCloud($xml_content, $filename);

            case 'custom':
                return $this->sendViaCustomApi($xml_content, $filename, $signed);

            case 'test':
            default:
                return $this->sendViaTest($xml_content, $filename);
        }
    }

    /**
     * Invio tramite Aruba
     */
    protected function sendViaAruba($xml_content, $filename, $signed)
    {
        $endpoint = self::ENDPOINTS['aruba'][$this->ambiente == 'produzione' ? 'prod' : 'test'];

        // Prepara i dati per Aruba
        // Aruba richiede il file in base64
        $data = [
            'dataFile'          => base64_encode($xml_content),
            'credential'        => [
                'username' => $this->config['username'],
                'password' => $this->config['password'],
            ],
            'domain'            => 'fatturapa',
            'filename'          => $filename,
            'signed'            => $signed ? 'true' : 'false',
        ];

        $response = $this->makeRequest($endpoint, $data, [
            'Content-Type: application/json',
        ]);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['uploadFileName'])) {
            return [
                'success'           => true,
                'identificativo_sdi' => $result['uploadFileName'] ?? '',
                'message'           => 'Fattura inviata con successo',
                'raw_response'      => $result,
            ];
        }

        $this->last_error = $result['errorDescription'] ?? 'Errore sconosciuto';
        return false;
    }

    /**
     * Invio tramite InfoCert
     */
    protected function sendViaInfoCert($xml_content, $filename, $signed)
    {
        $endpoint = self::ENDPOINTS['infocert'][$this->ambiente == 'produzione' ? 'prod' : 'test'];

        $data = [
            'xml_file'   => base64_encode($xml_content),
            'filename'   => $filename,
            'is_signed'  => $signed,
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config['api_key'],
        ];

        $response = $this->makeRequest($endpoint, $data, $headers);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['id'])) {
            return [
                'success'           => true,
                'identificativo_sdi' => $result['id'],
                'message'           => 'Fattura inviata con successo',
                'raw_response'      => $result,
            ];
        }

        $this->last_error = $result['error'] ?? 'Errore sconosciuto';
        return false;
    }

    /**
     * Invio tramite Fatture in Cloud
     */
    protected function sendViaFattureInCloud($xml_content, $filename)
    {
        $endpoint = str_replace(
            '{company_id}',
            $this->config['api_key'],
            self::ENDPOINTS['fattureincloud'][$this->ambiente == 'produzione' ? 'prod' : 'test']
        );

        $data = [
            'data' => [
                'type'     => 'issued_e_invoice',
                'xml_file' => base64_encode($xml_content),
            ]
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config['api_secret'],
        ];

        $response = $this->makeRequest($endpoint, $data, $headers);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['data']['id'])) {
            return [
                'success'           => true,
                'identificativo_sdi' => $result['data']['id'],
                'message'           => 'Fattura inviata con successo',
                'raw_response'      => $result,
            ];
        }

        $this->last_error = $result['error']['message'] ?? 'Errore sconosciuto';
        return false;
    }

    /**
     * Invio tramite API personalizzata
     */
    protected function sendViaCustomApi($xml_content, $filename, $signed)
    {
        $endpoint = $this->config['endpoint'];

        if (empty($endpoint)) {
            $this->last_error = 'Endpoint API non configurato';
            return false;
        }

        $data = [
            'xml_content' => base64_encode($xml_content),
            'filename'    => $filename,
            'signed'      => $signed,
        ];

        $headers = [
            'Content-Type: application/json',
        ];

        // Aggiungi autenticazione se configurata
        if (!empty($this->config['api_key'])) {
            $headers[] = 'X-API-Key: ' . $this->config['api_key'];
        }

        if (!empty($this->config['username']) && !empty($this->config['password'])) {
            $headers[] = 'Authorization: Basic ' . base64_encode($this->config['username'] . ':' . $this->config['password']);
        }

        $response = $this->makeRequest($endpoint, $data, $headers);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        // Cerca campi comuni nella risposta
        $id_field = $result['identificativo_sdi'] ?? $result['id'] ?? $result['sdi_id'] ?? null;

        if ($id_field !== null) {
            return [
                'success'           => true,
                'identificativo_sdi' => $id_field,
                'message'           => 'Fattura inviata con successo',
                'raw_response'      => $result,
            ];
        }

        $this->last_error = $result['error'] ?? $result['message'] ?? 'Errore sconosciuto';
        return false;
    }

    /**
     * Modalità test - simula l'invio
     */
    protected function sendViaTest($xml_content, $filename)
    {
        // Simula un invio di successo
        $fake_sdi_id = 'TEST_' . date('YmdHis') . '_' . substr(md5($filename), 0, 8);

        // Salva il file XML in locale per verifica
        $path = fe_get_upload_path('attive');
        file_put_contents($path . $filename, $xml_content);

        fe_log('invio_test', "Fattura inviata in modalità test: {$filename}", null, null);

        return [
            'success'           => true,
            'identificativo_sdi' => $fake_sdi_id,
            'message'           => '[TEST] Fattura inviata con successo (modalità test)',
            'raw_response'      => [
                'test_mode'  => true,
                'saved_path' => $path . $filename,
            ],
        ];
    }

    /**
     * Controlla lo stato di una fattura inviata
     *
     * @param string $identificativo_sdi Identificativo SDI
     * @return array|false Stato della fattura o false in caso di errore
     */
    public function checkInvoiceStatus($identificativo_sdi)
    {
        $this->last_error = null;

        switch ($this->provider) {
            case 'aruba':
                return $this->checkStatusAruba($identificativo_sdi);

            case 'infocert':
                return $this->checkStatusInfoCert($identificativo_sdi);

            case 'fattureincloud':
                return $this->checkStatusFattureInCloud($identificativo_sdi);

            case 'custom':
                return $this->checkStatusCustomApi($identificativo_sdi);

            case 'test':
            default:
                return $this->checkStatusTest($identificativo_sdi);
        }
    }

    /**
     * Controlla lo stato su Aruba
     */
    protected function checkStatusAruba($identificativo_sdi)
    {
        $endpoint = self::ENDPOINTS['aruba']['notifications'];

        $data = [
            'credential' => [
                'username' => $this->config['username'],
                'password' => $this->config['password'],
            ],
            'filename'   => $identificativo_sdi,
        ];

        $response = $this->makeRequest($endpoint, $data, [
            'Content-Type: application/json',
        ]);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        return $this->parseArubaStatus($result);
    }

    /**
     * Parsa lo stato da Aruba
     */
    protected function parseArubaStatus($result)
    {
        if (!isset($result['invoices']) || empty($result['invoices'])) {
            return [
                'stato' => FE_STATO_INVIATA,
                'message' => 'In attesa di elaborazione',
            ];
        }

        $invoice = $result['invoices'][0];
        $status = $invoice['status'] ?? '';

        $mapping = [
            'RECEIVED'    => FE_STATO_CONSEGNATA,
            'DELIVERED'   => FE_STATO_CONSEGNATA,
            'ACCEPTED'    => FE_STATO_ACCETTATA,
            'REJECTED'    => FE_STATO_RIFIUTATA,
            'FAILED'      => FE_STATO_SCARTATA,
            'NOT_DELIVERED' => FE_STATO_NON_CONSEGNATA,
        ];

        return [
            'stato'       => $mapping[$status] ?? FE_STATO_INVIATA,
            'message'     => $invoice['statusDescription'] ?? '',
            'raw_response' => $result,
        ];
    }

    /**
     * Controlla lo stato su InfoCert
     */
    protected function checkStatusInfoCert($identificativo_sdi)
    {
        $endpoint = self::ENDPOINTS['infocert'][$this->ambiente == 'produzione' ? 'prod' : 'test'];
        $endpoint .= '/' . $identificativo_sdi;

        $headers = [
            'Authorization: Bearer ' . $this->config['api_key'],
        ];

        $response = $this->makeRequest($endpoint, null, $headers, 'GET');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        $status = $result['status'] ?? 'PENDING';

        $mapping = [
            'PENDING'    => FE_STATO_INVIATA,
            'DELIVERED'  => FE_STATO_CONSEGNATA,
            'ACCEPTED'   => FE_STATO_ACCETTATA,
            'REJECTED'   => FE_STATO_RIFIUTATA,
            'ERROR'      => FE_STATO_SCARTATA,
        ];

        return [
            'stato'       => $mapping[$status] ?? FE_STATO_INVIATA,
            'message'     => $result['status_message'] ?? '',
            'raw_response' => $result,
        ];
    }

    /**
     * Controlla lo stato su Fatture in Cloud
     */
    protected function checkStatusFattureInCloud($identificativo_sdi)
    {
        $endpoint = str_replace(
            '{company_id}',
            $this->config['api_key'],
            self::ENDPOINTS['fattureincloud'][$this->ambiente == 'produzione' ? 'prod' : 'test']
        );
        $endpoint .= '/' . $identificativo_sdi;

        $headers = [
            'Authorization: Bearer ' . $this->config['api_secret'],
        ];

        $response = $this->makeRequest($endpoint, null, $headers, 'GET');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        $status = $result['data']['ei_status'] ?? 'pending';

        $mapping = [
            'pending'     => FE_STATO_INVIATA,
            'sent'        => FE_STATO_INVIATA,
            'delivered'   => FE_STATO_CONSEGNATA,
            'accepted'    => FE_STATO_ACCETTATA,
            'rejected'    => FE_STATO_RIFIUTATA,
            'error'       => FE_STATO_SCARTATA,
        ];

        return [
            'stato'       => $mapping[$status] ?? FE_STATO_INVIATA,
            'message'     => $result['data']['ei_status_description'] ?? '',
            'raw_response' => $result,
        ];
    }

    /**
     * Controlla lo stato tramite API personalizzata
     */
    protected function checkStatusCustomApi($identificativo_sdi)
    {
        $endpoint = $this->config['endpoint'] . '/status/' . $identificativo_sdi;

        $headers = [];
        if (!empty($this->config['api_key'])) {
            $headers[] = 'X-API-Key: ' . $this->config['api_key'];
        }

        $response = $this->makeRequest($endpoint, null, $headers, 'GET');

        if ($response === false) {
            return [
                'stato'   => FE_STATO_INVIATA,
                'message' => 'Impossibile verificare lo stato',
            ];
        }

        $result = json_decode($response, true);

        return [
            'stato'       => $result['stato'] ?? FE_STATO_INVIATA,
            'message'     => $result['message'] ?? '',
            'raw_response' => $result,
        ];
    }

    /**
     * Controlla lo stato in modalità test
     */
    protected function checkStatusTest($identificativo_sdi)
    {
        // Simula una progressione degli stati basata sul tempo
        // In produzione questo verrebbe sostituito con chiamate reali

        return [
            'stato'       => FE_STATO_CONSEGNATA,
            'message'     => '[TEST] Fattura consegnata (simulazione)',
            'raw_response' => ['test_mode' => true],
        ];
    }

    /**
     * Scarica le fatture passive (acquisto)
     *
     * @param string|null $from_date Data di inizio (opzionale)
     * @param string|null $to_date Data di fine (opzionale)
     * @return array|false Lista di fatture o false in caso di errore
     */
    public function downloadPassiveInvoices($from_date = null, $to_date = null)
    {
        $this->last_error = null;

        switch ($this->provider) {
            case 'aruba':
                return $this->downloadPassiveAruba($from_date, $to_date);

            case 'infocert':
                return $this->downloadPassiveInfoCert($from_date, $to_date);

            case 'fattureincloud':
                return $this->downloadPassiveFattureInCloud($from_date, $to_date);

            case 'custom':
                return $this->downloadPassiveCustomApi($from_date, $to_date);

            case 'test':
            default:
                return [];
        }
    }

    /**
     * Scarica le fatture passive da Aruba
     */
    protected function downloadPassiveAruba($from_date, $to_date)
    {
        $endpoint = self::ENDPOINTS['aruba']['passive'];

        $data = [
            'credential' => [
                'username' => $this->config['username'],
                'password' => $this->config['password'],
            ],
            'invoiceType' => 'IN', // Incoming
        ];

        if ($from_date) {
            $data['startDate'] = $from_date;
        }
        if ($to_date) {
            $data['endDate'] = $to_date;
        }

        $response = $this->makeRequest($endpoint, $data, [
            'Content-Type: application/json',
        ]);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        $invoices = [];
        foreach ($result['invoices'] ?? [] as $inv) {
            $invoices[] = [
                'identificativo_sdi' => $inv['sdiIdentifier'] ?? '',
                'nome_file'          => $inv['filename'] ?? '',
                'xml_content'        => base64_decode($inv['file'] ?? ''),
                'data_ricezione'     => $inv['receivedDate'] ?? date('Y-m-d H:i:s'),
            ];
        }

        return $invoices;
    }

    /**
     * Scarica le fatture passive da InfoCert
     */
    protected function downloadPassiveInfoCert($from_date, $to_date)
    {
        $endpoint = self::ENDPOINTS['infocert'][$this->ambiente == 'produzione' ? 'prod' : 'test'];
        $endpoint = str_replace('/invoices', '/received_invoices', $endpoint);

        $params = [];
        if ($from_date) {
            $params['from'] = $from_date;
        }
        if ($to_date) {
            $params['to'] = $to_date;
        }

        if (!empty($params)) {
            $endpoint .= '?' . http_build_query($params);
        }

        $headers = [
            'Authorization: Bearer ' . $this->config['api_key'],
        ];

        $response = $this->makeRequest($endpoint, null, $headers, 'GET');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        $invoices = [];
        foreach ($result['data'] ?? [] as $inv) {
            $invoices[] = [
                'identificativo_sdi' => $inv['sdi_id'] ?? '',
                'nome_file'          => $inv['filename'] ?? '',
                'xml_content'        => $inv['xml'] ?? '',
                'data_ricezione'     => $inv['received_at'] ?? date('Y-m-d H:i:s'),
            ];
        }

        return $invoices;
    }

    /**
     * Scarica le fatture passive da Fatture in Cloud
     */
    protected function downloadPassiveFattureInCloud($from_date, $to_date)
    {
        $endpoint = str_replace(
            '{company_id}',
            $this->config['api_key'],
            self::ENDPOINTS['fattureincloud'][$this->ambiente == 'produzione' ? 'prod' : 'test']
        );
        $endpoint = str_replace('/issued_e_invoices', '/received_e_invoices', $endpoint);

        $headers = [
            'Authorization: Bearer ' . $this->config['api_secret'],
        ];

        $response = $this->makeRequest($endpoint, null, $headers, 'GET');

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        $invoices = [];
        foreach ($result['data'] ?? [] as $inv) {
            $invoices[] = [
                'identificativo_sdi' => $inv['id'] ?? '',
                'nome_file'          => $inv['ei_filename'] ?? '',
                'xml_content'        => $inv['xml'] ?? '',
                'data_ricezione'     => $inv['created_at'] ?? date('Y-m-d H:i:s'),
            ];
        }

        return $invoices;
    }

    /**
     * Scarica le fatture passive da API personalizzata
     */
    protected function downloadPassiveCustomApi($from_date, $to_date)
    {
        $endpoint = $this->config['endpoint'] . '/passive';

        $params = [];
        if ($from_date) {
            $params['from_date'] = $from_date;
        }
        if ($to_date) {
            $params['to_date'] = $to_date;
        }

        if (!empty($params)) {
            $endpoint .= '?' . http_build_query($params);
        }

        $headers = [];
        if (!empty($this->config['api_key'])) {
            $headers[] = 'X-API-Key: ' . $this->config['api_key'];
        }

        $response = $this->makeRequest($endpoint, null, $headers, 'GET');

        if ($response === false) {
            return [];
        }

        return json_decode($response, true) ?: [];
    }

    /**
     * Scarica le notifiche SDI
     *
     * @param string|null $from_date Data di inizio
     * @return array|false Lista di notifiche o false in caso di errore
     */
    public function downloadNotifications($from_date = null)
    {
        $this->last_error = null;

        switch ($this->provider) {
            case 'aruba':
                return $this->downloadNotificationsAruba($from_date);

            case 'test':
            default:
                return [];
        }
    }

    /**
     * Scarica le notifiche da Aruba
     */
    protected function downloadNotificationsAruba($from_date)
    {
        // Aruba invia le notifiche come callback o via polling
        // Questa è un'implementazione di esempio

        $endpoint = 'https://ws.fatturazioneelettronica.aruba.it/services/invoice/notifications';

        $data = [
            'credential' => [
                'username' => $this->config['username'],
                'password' => $this->config['password'],
            ],
        ];

        if ($from_date) {
            $data['fromDate'] = $from_date;
        }

        $response = $this->makeRequest($endpoint, $data, [
            'Content-Type: application/json',
        ]);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        $notifications = [];
        foreach ($result['notifications'] ?? [] as $notif) {
            $notifications[] = [
                'identificativo_sdi' => $notif['sdiIdentifier'] ?? '',
                'tipo_notifica'      => $notif['type'] ?? '',
                'nome_file'          => $notif['filename'] ?? '',
                'xml_content'        => base64_decode($notif['content'] ?? ''),
                'data_ricezione'     => $notif['receivedDate'] ?? date('Y-m-d H:i:s'),
            ];
        }

        return $notifications;
    }

    /**
     * Effettua una richiesta HTTP
     *
     * @param string $url URL
     * @param array|null $data Dati da inviare
     * @param array $headers Headers aggiuntivi
     * @param string $method Metodo HTTP
     * @return string|false Risposta o false in caso di errore
     */
    protected function makeRequest($url, $data = null, $headers = [], $method = 'POST')
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            $this->last_error = 'Errore cURL: ' . $error;
            fe_log('errore_curl', $this->last_error);
            return false;
        }

        if ($http_code < 200 || $http_code >= 300) {
            $this->last_error = 'Errore HTTP ' . $http_code . ': ' . $response;
            fe_log('errore_http', $this->last_error);
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
     * @return array|null
     */
    public function getLastResponse()
    {
        return $this->last_response;
    }

    /**
     * Verifica se la connessione al provider è funzionante
     *
     * @return bool
     */
    public function testConnection()
    {
        if ($this->provider == 'test') {
            return true;
        }

        // Prova a fare una chiamata leggera per verificare le credenziali
        $status = $this->checkInvoiceStatus('TEST_CONNECTION');

        return $status !== false || $this->last_error === null;
    }

    /**
     * Webhook handler per la ricezione di notifiche SDI
     *
     * @param string $payload Payload ricevuto
     * @param string $signature Firma per validazione (opzionale)
     * @return array|false Dati della notifica o false
     */
    public function handleWebhook($payload, $signature = null)
    {
        // Verifica la firma se configurata
        $secret = get_option('fe_webhook_secret');

        if (!empty($signature) && !empty($secret)) {
            $expected = hash_hmac('sha256', $payload, $secret);
            if (!hash_equals($expected, $signature)) {
                $this->last_error = 'Firma webhook non valida';
                return false;
            }
        }

        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->last_error = 'Payload JSON non valido';
            return false;
        }

        return $data;
    }
}

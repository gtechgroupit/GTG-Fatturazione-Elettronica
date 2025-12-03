<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client principale per l'invio e la ricezione di fatture elettroniche tramite SDI
 *
 * Questa classe funge da factory/router per i vari provider:
 * - agenzia_entrate: Integrazione diretta con Agenzia delle Entrate
 * - fattura24: Integrazione con Fattura24.it
 * - fattureincloud: Integrazione con FattureInCloud.it
 * - test: Modalità test/sviluppo
 *
 * @author GTech Group IT
 * @version 2.0.0
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
     * @var object Istanza del client provider specifico
     */
    protected $provider_client;

    /**
     * @var string Ambiente (test/produzione)
     */
    protected $ambiente;

    /**
     * @var string Ultimo errore
     */
    protected $last_error;

    /**
     * @var array Risposta dell'ultima chiamata
     */
    protected $last_response;

    /**
     * Provider supportati con le loro classi
     */
    const PROVIDERS = [
        'agenzia_entrate'  => 'Agenzia_entrate_client',
        'fattura24'        => 'Fattura24_client',
        'fattureincloud'   => 'Fattureincloud_client',
        'test'             => null, // Gestito internamente
    ];

    /**
     * Costruttore
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->loadConfig();
        $this->loadProviderClient();
    }

    /**
     * Carica la configurazione
     */
    protected function loadConfig()
    {
        $this->provider = get_option('fe_provider') ?: 'test';
        $this->ambiente = get_option('fe_ambiente') ?: 'test';
    }

    /**
     * Carica il client del provider specifico
     */
    protected function loadProviderClient()
    {
        if ($this->provider === 'test' || !isset(self::PROVIDERS[$this->provider])) {
            $this->provider_client = null;
            return;
        }

        $client_class = self::PROVIDERS[$this->provider];

        if ($client_class) {
            $this->CI->load->library('fatturazione_elettronica/' . $client_class);
            $client_name = strtolower($client_class);
            $this->provider_client = $this->CI->$client_name;
        }
    }

    /**
     * Ottiene l'elenco di tutti i provider disponibili con le loro informazioni
     *
     * @return array
     */
    public static function getAvailableProviders()
    {
        $providers = [];
        $CI = &get_instance();

        // Agenzia delle Entrate
        $CI->load->library('fatturazione_elettronica/Agenzia_entrate_client');
        $providers['agenzia_entrate'] = Agenzia_entrate_client::getProviderInfo();

        // Fattura24
        $CI->load->library('fatturazione_elettronica/Fattura24_client');
        $providers['fattura24'] = Fattura24_client::getProviderInfo();

        // FattureInCloud
        $CI->load->library('fatturazione_elettronica/Fattureincloud_client');
        $providers['fattureincloud'] = Fattureincloud_client::getProviderInfo();

        // Modalità Test
        $providers['test'] = [
            'id'          => 'test',
            'name'        => 'Modalità Test',
            'description' => 'Modalità di sviluppo e test senza invio reale allo SDI',
            'icon'        => 'fa-flask',
            'color'       => '#6c757d',
            'features'    => [
                'Simulazione invio fatture',
                'Generazione XML valido',
                'Test senza costi',
                'Ideale per sviluppo',
            ],
            'pricing'     => 'Gratuito',
            'fields'      => [],
            'requirements' => [],
        ];

        return $providers;
    }

    /**
     * Ottiene le informazioni del provider attivo
     *
     * @return array
     */
    public function getActiveProviderInfo()
    {
        $providers = self::getAvailableProviders();
        return $providers[$this->provider] ?? $providers['test'];
    }

    /**
     * Verifica se il provider è configurato correttamente
     *
     * @return array ['configured' => bool, 'missing_fields' => array]
     */
    public function isProviderConfigured()
    {
        $provider_info = $this->getActiveProviderInfo();
        $missing = [];

        foreach ($provider_info['fields'] ?? [] as $field) {
            if (isset($field['required']) && $field['required']) {
                $value = get_option($field['name']);
                if (empty($value)) {
                    $missing[] = $field['label'];
                }
            }
        }

        return [
            'configured'     => empty($missing),
            'missing_fields' => $missing,
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
        fe_log('invio_fattura', "Invio fattura: {$filename} via {$this->provider}");

        // Usa il provider client se disponibile
        if ($this->provider_client && method_exists($this->provider_client, 'sendInvoice')) {
            $result = $this->provider_client->sendInvoice($xml_content, $filename, $signed);

            if ($result === false) {
                $this->last_error = $this->provider_client->getLastError();
            }

            return $result;
        }

        // Fallback modalità test
        return $this->sendViaTest($xml_content, $filename);
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
            'success'            => true,
            'identificativo_sdi' => $fake_sdi_id,
            'message'            => '[TEST] Fattura inviata con successo (modalità test)',
            'raw_response'       => [
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

        // Usa il provider client se disponibile
        if ($this->provider_client && method_exists($this->provider_client, 'checkInvoiceStatus')) {
            $result = $this->provider_client->checkInvoiceStatus($identificativo_sdi);

            if ($result === false) {
                $this->last_error = $this->provider_client->getLastError();
            }

            return $result;
        }

        // Fallback modalità test
        return $this->checkStatusTest($identificativo_sdi);
    }

    /**
     * Controlla lo stato in modalità test
     */
    protected function checkStatusTest($identificativo_sdi)
    {
        return [
            'stato'        => FE_STATO_CONSEGNATA,
            'message'      => '[TEST] Fattura consegnata (simulazione)',
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

        // Usa il provider client se disponibile
        if ($this->provider_client && method_exists($this->provider_client, 'downloadPassiveInvoices')) {
            $result = $this->provider_client->downloadPassiveInvoices($from_date, $to_date);

            if ($result === false) {
                $this->last_error = $this->provider_client->getLastError();
            }

            return $result;
        }

        // Fallback modalità test - nessuna fattura passiva
        return [];
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

        // Usa il provider client se disponibile
        if ($this->provider_client && method_exists($this->provider_client, 'downloadNotifications')) {
            $result = $this->provider_client->downloadNotifications($from_date);

            if ($result === false) {
                $this->last_error = $this->provider_client->getLastError();
            }

            return $result;
        }

        return [];
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
        if ($this->provider === 'test') {
            return true;
        }

        // Verifica configurazione
        $config_check = $this->isProviderConfigured();
        if (!$config_check['configured']) {
            $this->last_error = 'Configurazione incompleta: ' . implode(', ', $config_check['missing_fields']);
            return false;
        }

        // Usa il provider client se disponibile
        if ($this->provider_client && method_exists($this->provider_client, 'testConnection')) {
            $result = $this->provider_client->testConnection();

            if (!$result) {
                $this->last_error = $this->provider_client->getLastError();
            }

            return $result;
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
        // Usa il provider client se disponibile
        if ($this->provider_client && method_exists($this->provider_client, 'handleWebhook')) {
            return $this->provider_client->handleWebhook($payload, $signature);
        }

        // Gestione generica
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

    /**
     * Ottiene il provider attivo
     *
     * @return string
     */
    public function getActiveProvider()
    {
        return $this->provider;
    }

    /**
     * Ottiene l'ambiente attivo
     *
     * @return string
     */
    public function getAmbiente()
    {
        return $this->ambiente;
    }

    // =========================================================================
    // METODI SPECIFICI PER FATTUREINCLOUD OAUTH
    // =========================================================================

    /**
     * Ottiene l'URL di autorizzazione OAuth per FattureInCloud
     *
     * @return string|false
     */
    public function getOAuthAuthorizationUrl()
    {
        if ($this->provider !== 'fattureincloud') {
            $this->last_error = 'OAuth disponibile solo per FattureInCloud';
            return false;
        }

        $this->CI->load->library('fatturazione_elettronica/Fattureincloud_client');
        return $this->CI->fattureincloud_client->getAuthorizationUrl();
    }

    /**
     * Gestisce il callback OAuth per FattureInCloud
     *
     * @param string $code Codice di autorizzazione
     * @return bool
     */
    public function handleOAuthCallback($code)
    {
        if ($this->provider !== 'fattureincloud') {
            $this->last_error = 'OAuth disponibile solo per FattureInCloud';
            return false;
        }

        $this->CI->load->library('fatturazione_elettronica/Fattureincloud_client');
        return $this->CI->fattureincloud_client->handleCallback($code);
    }

    /**
     * Disconnette l'account OAuth
     *
     * @return bool
     */
    public function disconnectOAuth()
    {
        if ($this->provider === 'fattureincloud') {
            $this->CI->load->library('fatturazione_elettronica/Fattureincloud_client');
            return $this->CI->fattureincloud_client->disconnect();
        }

        return true;
    }
}

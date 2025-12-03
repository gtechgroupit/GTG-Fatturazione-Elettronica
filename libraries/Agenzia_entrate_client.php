<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client per l'integrazione diretta con l'Agenzia delle Entrate
 *
 * Utilizza i Web Services SDI per l'invio e la ricezione delle fatture elettroniche
 * direttamente al Sistema di Interscambio dell'Agenzia delle Entrate.
 *
 * NOTA: Per l'utilizzo diretto è necessario:
 * - Essere accreditati presso l'Agenzia delle Entrate
 * - Possedere un certificato di firma digitale
 * - Configurare un endpoint per la ricezione delle notifiche
 *
 * @author GTech Group IT
 * @version 1.0.0
 */
class Agenzia_entrate_client
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
     * Endpoint SDI
     */
    const ENDPOINTS = [
        'test' => [
            'trasmissione'  => 'https://testservizi.fatturapa.it/ricevi_file',
            'notifiche'     => 'https://testservizi.fatturapa.it/ricevi_notifica',
            'stato'         => 'https://testservizi.fatturapa.it/stato_fattura',
        ],
        'produzione' => [
            'trasmissione'  => 'https://servizi.fatturapa.it/ricevi_file',
            'notifiche'     => 'https://servizi.fatturapa.it/ricevi_notifica',
            'stato'         => 'https://servizi.fatturapa.it/stato_fattura',
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
        $this->ambiente = get_option('fe_ambiente') ?: 'test';

        $this->config = [
            'certificato_path'     => get_option('fe_ade_certificato_path') ?: '',
            'certificato_password' => get_option('fe_ade_certificato_password') ?: '',
            'codice_fiscale_trasmittente' => get_option('fe_codice_fiscale') ?: '',
            'partita_iva'          => get_option('fe_partita_iva') ?: '',
            'codice_accreditamento' => get_option('fe_ade_codice_accreditamento') ?: '',
            'endpoint_ricezione'   => get_option('fe_ade_endpoint_ricezione') ?: '',
        ];
    }

    /**
     * Invia una fattura al Sistema di Interscambio
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

        // Verifica configurazione
        if (!$this->validateConfig()) {
            return false;
        }

        // Se non è firmato, firma il documento
        if (!$signed) {
            $xml_content = $this->signDocument($xml_content);
            if ($xml_content === false) {
                return false;
            }
        }

        // Prepara il messaggio SOAP per l'invio
        $endpoint = self::ENDPOINTS[$this->ambiente == 'produzione' ? 'produzione' : 'test']['trasmissione'];

        try {
            $response = $this->sendToSDI($endpoint, $xml_content, $filename);

            if ($response === false) {
                return false;
            }

            return [
                'success'            => true,
                'identificativo_sdi' => $response['identificativoSdI'] ?? '',
                'data_ora_ricezione' => $response['dataOraRicezione'] ?? date('Y-m-d H:i:s'),
                'message'            => 'Fattura inviata con successo al Sistema di Interscambio',
                'raw_response'       => $response,
            ];

        } catch (Exception $e) {
            $this->last_error = 'Errore durante l\'invio: ' . $e->getMessage();
            fe_log('errore_invio_ade', $this->last_error);
            return false;
        }
    }

    /**
     * Verifica la configurazione
     *
     * @return bool
     */
    protected function validateConfig()
    {
        if (empty($this->config['certificato_path'])) {
            $this->last_error = 'Certificato di firma non configurato';
            return false;
        }

        if (!file_exists($this->config['certificato_path'])) {
            $this->last_error = 'File certificato non trovato: ' . $this->config['certificato_path'];
            return false;
        }

        if (empty($this->config['partita_iva'])) {
            $this->last_error = 'Partita IVA trasmittente non configurata';
            return false;
        }

        return true;
    }

    /**
     * Firma digitalmente il documento XML
     *
     * @param string $xml_content Contenuto XML
     * @return string|false XML firmato o false
     */
    protected function signDocument($xml_content)
    {
        // Verifica che OpenSSL sia disponibile
        if (!extension_loaded('openssl')) {
            $this->last_error = 'Estensione OpenSSL non disponibile';
            return false;
        }

        try {
            // Carica il certificato PKCS#12
            $cert_content = file_get_contents($this->config['certificato_path']);
            $cert_data = [];

            if (!openssl_pkcs12_read($cert_content, $cert_data, $this->config['certificato_password'])) {
                $this->last_error = 'Impossibile leggere il certificato. Verifica la password.';
                return false;
            }

            $private_key = $cert_data['pkey'];
            $certificate = $cert_data['cert'];

            // Crea la firma XAdES-BES
            $xml_signed = $this->createXAdESSignature($xml_content, $private_key, $certificate);

            return $xml_signed;

        } catch (Exception $e) {
            $this->last_error = 'Errore durante la firma: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Crea una firma XAdES-BES
     *
     * @param string $xml_content Contenuto XML
     * @param string $private_key Chiave privata
     * @param string $certificate Certificato
     * @return string XML firmato
     */
    protected function createXAdESSignature($xml_content, $private_key, $certificate)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml_content);

        // Calcola il digest del documento
        $canonicalized = $doc->C14N(true, false);
        $digest = base64_encode(hash('sha256', $canonicalized, true));

        // Crea la struttura della firma
        $signedInfo = $this->createSignedInfo($digest);

        // Firma il SignedInfo
        openssl_sign($signedInfo, $signature, $private_key, OPENSSL_ALGO_SHA256);
        $signature_value = base64_encode($signature);

        // Estrai le informazioni del certificato
        $cert_info = openssl_x509_parse($certificate);
        $cert_der = '';
        openssl_x509_export($certificate, $cert_pem);
        $cert_der = base64_encode($this->pemToDer($cert_pem));

        // Aggiungi la firma al documento
        $signed_xml = $this->insertSignature($doc, $signedInfo, $signature_value, $cert_der);

        return $signed_xml;
    }

    /**
     * Crea l'elemento SignedInfo
     */
    protected function createSignedInfo($digest)
    {
        $signedInfo = '<ds:SignedInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
            <ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
            <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/>
            <ds:Reference URI="">
                <ds:Transforms>
                    <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
                </ds:Transforms>
                <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                <ds:DigestValue>' . $digest . '</ds:DigestValue>
            </ds:Reference>
        </ds:SignedInfo>';

        return $signedInfo;
    }

    /**
     * Inserisce la firma nel documento
     */
    protected function insertSignature($doc, $signedInfo, $signatureValue, $certificate)
    {
        $signature = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Signature');

        // SignedInfo
        $signedInfoFragment = $doc->createDocumentFragment();
        $signedInfoFragment->appendXML($signedInfo);
        $signature->appendChild($signedInfoFragment);

        // SignatureValue
        $sigValue = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignatureValue', $signatureValue);
        $signature->appendChild($sigValue);

        // KeyInfo
        $keyInfo = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:KeyInfo');
        $x509Data = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Data');
        $x509Cert = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Certificate', $certificate);
        $x509Data->appendChild($x509Cert);
        $keyInfo->appendChild($x509Data);
        $signature->appendChild($keyInfo);

        // Inserisci la firma come primo elemento dopo il root
        $root = $doc->documentElement;
        $root->insertBefore($signature, $root->firstChild);

        return $doc->saveXML();
    }

    /**
     * Converte PEM in DER
     */
    protected function pemToDer($pem)
    {
        $pem = str_replace('-----BEGIN CERTIFICATE-----', '', $pem);
        $pem = str_replace('-----END CERTIFICATE-----', '', $pem);
        $pem = str_replace(["\r", "\n", ' '], '', $pem);
        return base64_decode($pem);
    }

    /**
     * Invia al Sistema di Interscambio tramite Web Service
     *
     * @param string $endpoint Endpoint del servizio
     * @param string $xml_content XML da inviare
     * @param string $filename Nome file
     * @return array|false
     */
    protected function sendToSDI($endpoint, $xml_content, $filename)
    {
        // Prepara il messaggio SOAP
        $soap_message = $this->createSoapMessage($xml_content, $filename);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $soap_message,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSLCERT        => $this->config['certificato_path'],
            CURLOPT_SSLCERTPASSWD  => $this->config['certificato_password'],
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/soap+xml; charset=utf-8',
                'SOAPAction: http://www.fatturapa.it/TrasmsissioneFatture/v1.0/TrasmettiFattura',
            ],
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            $this->last_error = 'Errore cURL: ' . $error;
            return false;
        }

        if ($http_code < 200 || $http_code >= 300) {
            $this->last_error = 'Errore HTTP ' . $http_code;
            return false;
        }

        // Parsa la risposta SOAP
        return $this->parseSoapResponse($response);
    }

    /**
     * Crea il messaggio SOAP
     */
    protected function createSoapMessage($xml_content, $filename)
    {
        $xml_base64 = base64_encode($xml_content);

        return '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"
               xmlns:tns="http://www.fatturapa.it/TrasmissioneFatture/v1.0">
    <soap:Header/>
    <soap:Body>
        <tns:FileSdI>
            <tns:NomeFile>' . htmlspecialchars($filename) . '</tns:NomeFile>
            <tns:File>' . $xml_base64 . '</tns:File>
        </tns:FileSdI>
    </soap:Body>
</soap:Envelope>';
    }

    /**
     * Parsa la risposta SOAP
     */
    protected function parseSoapResponse($response)
    {
        $doc = new DOMDocument();
        $doc->loadXML($response);

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('soap', 'http://www.w3.org/2003/05/soap-envelope');
        $xpath->registerNamespace('tns', 'http://www.fatturapa.it/TrasmissioneFatture/v1.0');

        // Cerca l'identificativo SDI nella risposta
        $id_sdi = $xpath->evaluate('string(//tns:IdentificativoSdI)');
        $data_ora = $xpath->evaluate('string(//tns:DataOraRicezione)');

        // Cerca eventuali errori
        $errore = $xpath->evaluate('string(//tns:Errore)');

        if (!empty($errore)) {
            $this->last_error = $errore;
            return false;
        }

        return [
            'identificativoSdI' => $id_sdi,
            'dataOraRicezione'  => $data_ora,
        ];
    }

    /**
     * Controlla lo stato di una fattura
     *
     * @param string $identificativo_sdi ID SDI
     * @return array|false
     */
    public function checkInvoiceStatus($identificativo_sdi)
    {
        $endpoint = self::ENDPOINTS[$this->ambiente == 'produzione' ? 'produzione' : 'test']['stato'];

        $soap_message = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"
               xmlns:tns="http://www.fatturapa.it/TrasmissioneFatture/v1.0">
    <soap:Header/>
    <soap:Body>
        <tns:RichiestaStatoFattura>
            <tns:IdentificativoSdI>' . htmlspecialchars($identificativo_sdi) . '</tns:IdentificativoSdI>
        </tns:RichiestaStatoFattura>
    </soap:Body>
</soap:Envelope>';

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $soap_message,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSLCERT        => $this->config['certificato_path'],
            CURLOPT_SSLCERTPASSWD  => $this->config['certificato_password'],
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/soap+xml; charset=utf-8',
            ],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return false;
        }

        return $this->parseStatusResponse($response);
    }

    /**
     * Parsa la risposta dello stato
     */
    protected function parseStatusResponse($response)
    {
        $doc = new DOMDocument();
        $doc->loadXML($response);

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('tns', 'http://www.fatturapa.it/TrasmissioneFatture/v1.0');

        $stato = $xpath->evaluate('string(//tns:StatoFattura)');
        $descrizione = $xpath->evaluate('string(//tns:DescrizioneStato)');

        // Mappa gli stati SDI ai nostri stati
        $stato_mapping = [
            'RC'  => FE_STATO_CONSEGNATA,       // Ricevuta di Consegna
            'NS'  => FE_STATO_SCARTATA,         // Notifica di Scarto
            'MC'  => FE_STATO_MANCATA_CONSEGNA, // Mancata Consegna
            'NE'  => FE_STATO_RIFIUTATA,        // Notifica Esito cedente/prestatore
            'DT'  => FE_STATO_DECORRENZA_TERMINI, // Decorrenza Termini
            'AT'  => FE_STATO_ACCETTATA,        // Attestazione di Avvenuta Trasmissione
        ];

        return [
            'stato'       => $stato_mapping[$stato] ?? FE_STATO_INVIATA,
            'message'     => $descrizione,
            'raw_response' => $response,
        ];
    }

    /**
     * Scarica le fatture passive
     *
     * @param string|null $from_date Data di inizio
     * @param string|null $to_date Data di fine
     * @return array
     */
    public function downloadPassiveInvoices($from_date = null, $to_date = null)
    {
        // L'Agenzia delle Entrate invia le fatture passive tramite il canale configurato
        // Qui implementiamo il polling se necessario
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
        if (!$this->validateConfig()) {
            return false;
        }

        // Verifica che il certificato sia valido
        $cert_content = file_get_contents($this->config['certificato_path']);
        $cert_data = [];

        if (!openssl_pkcs12_read($cert_content, $cert_data, $this->config['certificato_password'])) {
            $this->last_error = 'Certificato non valido o password errata';
            return false;
        }

        // Verifica la scadenza del certificato
        $cert_info = openssl_x509_parse($cert_data['cert']);
        if ($cert_info['validTo_time_t'] < time()) {
            $this->last_error = 'Certificato scaduto';
            return false;
        }

        return true;
    }

    /**
     * Gestisce il webhook per la ricezione delle notifiche SDI
     *
     * @param string $payload Payload ricevuto
     * @return array|false
     */
    public function handleWebhook($payload)
    {
        // Parsa il messaggio SOAP ricevuto
        $doc = new DOMDocument();
        if (!$doc->loadXML($payload)) {
            $this->last_error = 'XML non valido';
            return false;
        }

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('tns', 'http://www.fatturapa.it/TrasmissioneFatture/v1.0');

        $tipo_messaggio = $xpath->evaluate('string(//tns:TipoMessaggio)');
        $id_sdi = $xpath->evaluate('string(//tns:IdentificativoSdI)');
        $nome_file = $xpath->evaluate('string(//tns:NomeFile)');
        $file_content = $xpath->evaluate('string(//tns:File)');

        return [
            'tipo'               => $tipo_messaggio,
            'identificativo_sdi' => $id_sdi,
            'nome_file'          => $nome_file,
            'content'            => base64_decode($file_content),
        ];
    }

    /**
     * Ottiene le informazioni del provider
     *
     * @return array
     */
    public static function getProviderInfo()
    {
        return [
            'id'          => 'agenzia_entrate',
            'name'        => 'Agenzia delle Entrate (Diretto)',
            'description' => 'Integrazione diretta con il Sistema di Interscambio dell\'Agenzia delle Entrate. Richiede accreditamento e certificato di firma digitale.',
            'logo'        => 'assets/images/providers/ade.png',
            'website'     => 'https://www.fatturapa.gov.it/',
            'features'    => [
                'Integrazione diretta senza intermediari',
                'Nessun costo per transazione',
                'Massimo controllo sul processo',
                'Richiede accreditamento presso AdE',
                'Richiede certificato di firma digitale',
            ],
            'requirements' => [
                'Accreditamento presso Agenzia delle Entrate',
                'Certificato di firma digitale (PKCS#12)',
                'Endpoint per ricezione notifiche',
                'Competenze tecniche avanzate',
            ],
            'fields' => [
                [
                    'name'        => 'fe_ade_certificato_path',
                    'label'       => 'Percorso Certificato (P12/PFX)',
                    'type'        => 'text',
                    'required'    => true,
                    'placeholder' => '/path/to/certificate.p12',
                ],
                [
                    'name'        => 'fe_ade_certificato_password',
                    'label'       => 'Password Certificato',
                    'type'        => 'password',
                    'required'    => true,
                ],
                [
                    'name'        => 'fe_ade_codice_accreditamento',
                    'label'       => 'Codice Accreditamento SDI',
                    'type'        => 'text',
                    'required'    => false,
                    'placeholder' => 'Codice rilasciato dall\'AdE',
                ],
                [
                    'name'        => 'fe_ade_endpoint_ricezione',
                    'label'       => 'Endpoint Ricezione Notifiche',
                    'type'        => 'text',
                    'required'    => false,
                    'placeholder' => 'https://tuodominio.com/sdi/callback',
                ],
            ],
        ];
    }
}

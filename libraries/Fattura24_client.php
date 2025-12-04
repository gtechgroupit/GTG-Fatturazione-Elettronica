<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client per l'integrazione con Fattura24.it
 *
 * IMPORTANTE: Fattura24 NON supporta l'invio automatico allo SDI tramite API.
 * Le fatture vengono create nel sistema Fattura24 e l'utente deve
 * completare l'invio manualmente dal pannello web.
 *
 * API Documentation: https://www.fattura24.com/api-fatturazione-elettronica/
 *
 * @author GTech Group IT
 * @version 2.0.0
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
     * API Endpoints - IMPORTANTE: usare app.fattura24.com (NON www.fattura24.com)
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
     * NOTA: Fattura24 NON supporta l'invio automatico allo SDI tramite API.
     * La fattura viene creata nel sistema Fattura24 e l'utente deve
     * completare l'invio manualmente dal pannello web.
     *
     * @param string $xml_content Contenuto XML della fattura (formato FatturaPA)
     * @param string $filename Nome del file
     * @param bool $signed Se l'XML è già firmato (ignorato)
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

        // Parsa l'XML FatturaPA e converti nel formato Fattura24
        $fattura24_xml = $this->convertFatturaPAToFattura24($xml_content);

        if ($fattura24_xml === false) {
            return false;
        }

        // Chiama SaveDocument
        $endpoint = self::API_BASE_URL . '/SaveDocument';

        $data = [
            'apiKey' => $this->config['api_key'],
            'xml'    => $fattura24_xml,
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response === false) {
            return false;
        }

        // Parsa la risposta
        $result = $this->parseResponse($response);

        if (!$result['success']) {
            $this->last_error = $result['error'] ?? $result['description'] ?? 'Errore sconosciuto da Fattura24';
            return false;
        }

        // IMPORTANTE: Fattura24 NON invia automaticamente allo SDI
        // L'utente deve completare l'invio manualmente
        return [
            'success'            => true,
            'identificativo_sdi' => 'F24_' . ($result['docId'] ?? $result['id'] ?? time()),
            'id_fattura24'       => $result['docId'] ?? $result['id'],
            'doc_number'         => $result['docNumber'] ?? null,
            'message'            => 'Fattura creata in Fattura24. ATTENZIONE: Devi completare l\'invio allo SDI manualmente dal pannello Fattura24.',
            'requires_manual_send' => true,
            'raw_response'       => $result,
        ];
    }

    /**
     * Converte XML FatturaPA nel formato XML di Fattura24
     *
     * @param string $fatturapa_xml XML in formato FatturaPA
     * @return string|false XML in formato Fattura24 o false in caso di errore
     */
    protected function convertFatturaPAToFattura24($fatturapa_xml)
    {
        // Log dell'XML raw ricevuto (primi 2000 caratteri)
        fe_log('debug_fattura24', '=== INIZIO CONVERSIONE ===');
        fe_log('debug_fattura24', 'XML FatturaPA ricevuto (primi 2000 char): ' . substr($fatturapa_xml, 0, 2000));

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        @$doc->loadXML($fatturapa_xml);

        if (!$doc->documentElement) {
            $this->last_error = 'XML FatturaPA non valido';
            fe_log('errore_fattura24', 'XML FatturaPA non valido - contenuto vuoto o malformato');
            return false;
        }

        $xpath = new DOMXPath($doc);

        // Registra i namespace FatturaPA - prova entrambi i casi
        $xpath->registerNamespace('p', 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2');

        // Log root element info
        $root = $doc->documentElement;
        fe_log('debug_fattura24', 'Root element: ' . $root->nodeName . ', namespace: ' . ($root->namespaceURI ?: 'none'));

        // Log per debug
        fe_log('debug_fattura24', 'Inizio estrazione dati...');

        // Estrai i dati dal FatturaPA
        $cessionario = $this->extractCessionario($xpath);
        $documento = $this->extractDocumento($xpath);
        $linee = $this->extractLinee($xpath);
        $pagamento = $this->extractPagamento($xpath);

        // Log dei dati estratti
        fe_log('debug_fattura24', 'Cessionario: ' . json_encode($cessionario, JSON_UNESCAPED_UNICODE));
        fe_log('debug_fattura24', 'Documento: ' . json_encode($documento, JSON_UNESCAPED_UNICODE));
        fe_log('debug_fattura24', 'Linee (' . count($linee) . '): ' . json_encode($linee, JSON_UNESCAPED_UNICODE));
        fe_log('debug_fattura24', 'Pagamento: ' . json_encode($pagamento, JSON_UNESCAPED_UNICODE));

        if (empty($cessionario['denominazione']) && empty($documento['numero'])) {
            $this->last_error = 'Impossibile estrarre i dati dalla fattura - dati vuoti';
            fe_log('errore_fattura24', $this->last_error);
            return false;
        }

        // Costruisci l'XML Fattura24
        $f24_xml = $this->buildFattura24Xml($cessionario, $documento, $linee, $pagamento);

        // Log dell'XML generato
        fe_log('debug_fattura24', 'XML Fattura24 generato: ' . $f24_xml);

        return $f24_xml;
    }

    /**
     * Estrae i dati del cessionario/committente dall'XML FatturaPA
     */
    protected function extractCessionario($xpath)
    {
        $result = [
            'denominazione' => '',
            'nome' => '',
            'cognome' => '',
            'partita_iva' => '',
            'codice_fiscale' => '',
            'indirizzo' => '',
            'cap' => '',
            'comune' => '',
            'provincia' => '',
            'nazione' => 'IT',
            'pec' => '',
            'codice_destinatario' => '',
        ];

        // Trova il nodo CessionarioCommittente
        $cessionarioNode = $this->findNode($xpath, 'CessionarioCommittente');

        if ($cessionarioNode) {
            fe_log('debug_fattura24', 'Trovato CessionarioCommittente');

            // Estrai tutti i valori ricorsivamente
            $values = $this->extractAllValues($cessionarioNode);
            fe_log('debug_fattura24', 'Valori CessionarioCommittente: ' . json_encode($values, JSON_UNESCAPED_UNICODE));

            // Mappa i valori ai campi
            $result['denominazione'] = $values['Denominazione'] ?? '';
            $result['nome'] = $values['Nome'] ?? '';
            $result['cognome'] = $values['Cognome'] ?? '';
            $result['partita_iva'] = $values['IdCodice'] ?? '';
            $result['codice_fiscale'] = $values['CodiceFiscale'] ?? '';
            $result['indirizzo'] = $values['Indirizzo'] ?? '';
            $result['cap'] = $values['CAP'] ?? '';
            $result['comune'] = $values['Comune'] ?? '';
            $result['provincia'] = $values['Provincia'] ?? '';
            $result['nazione'] = $values['Nazione'] ?? 'IT';
        } else {
            fe_log('debug_fattura24', 'CessionarioCommittente NON trovato');
        }

        // Trova DatiTrasmissione per PEC e codice destinatario
        $trasmissioneNode = $this->findNode($xpath, 'DatiTrasmissione');
        if ($trasmissioneNode) {
            $values = $this->extractAllValues($trasmissioneNode);
            $result['pec'] = $values['PECDestinatario'] ?? '';
            $result['codice_destinatario'] = $values['CodiceDestinatario'] ?? '';
        }

        // Se non c'è denominazione, prova con nome + cognome
        if (empty($result['denominazione'])) {
            $result['denominazione'] = trim($result['nome'] . ' ' . $result['cognome']);
        }

        return $result;
    }

    /**
     * Trova un nodo per local-name
     */
    protected function findNode($xpath, $localName)
    {
        $patterns = [
            '//' . $localName,
            '//p:' . $localName,
            '//*[local-name()="' . $localName . '"]',
        ];

        foreach ($patterns as $pattern) {
            $nodes = $xpath->query($pattern);
            if ($nodes && $nodes->length > 0) {
                return $nodes->item(0);
            }
        }
        return null;
    }

    /**
     * Estrae tutti i valori da un nodo e i suoi figli (ricorsivo)
     */
    protected function extractAllValues($node, $values = [])
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) continue;

            $localName = $child->localName ?: $child->nodeName;

            // Se ha figli, ricorsione
            if ($child->hasChildNodes()) {
                $hasElementChild = false;
                foreach ($child->childNodes as $grandchild) {
                    if ($grandchild->nodeType === XML_ELEMENT_NODE) {
                        $hasElementChild = true;
                        break;
                    }
                }
                if ($hasElementChild) {
                    $values = $this->extractAllValues($child, $values);
                } else {
                    $values[$localName] = trim($child->textContent);
                }
            } else {
                $values[$localName] = trim($child->textContent);
            }
        }
        return $values;
    }

    /**
     * Estrae i dati del documento dall'XML FatturaPA
     */
    protected function extractDocumento($xpath)
    {
        $result = [
            'tipo_documento' => '',
            'numero' => '',
            'data' => '',
            'causale' => '',
            'importo_totale' => '',
            'bollo_virtuale' => '',
        ];

        // Trova DatiGeneraliDocumento
        $docNode = $this->findNode($xpath, 'DatiGeneraliDocumento');

        if ($docNode) {
            fe_log('debug_fattura24', 'Trovato DatiGeneraliDocumento');

            $values = $this->extractAllValues($docNode);
            fe_log('debug_fattura24', 'Valori DatiGeneraliDocumento: ' . json_encode($values, JSON_UNESCAPED_UNICODE));

            $result['tipo_documento'] = $values['TipoDocumento'] ?? '';
            $result['numero'] = $values['Numero'] ?? '';
            $result['data'] = $values['Data'] ?? '';
            $result['causale'] = $values['Causale'] ?? '';
            $result['importo_totale'] = $values['ImportoTotaleDocumento'] ?? '';
            $result['bollo_virtuale'] = $values['BolloVirtuale'] ?? '';
        } else {
            fe_log('debug_fattura24', 'DatiGeneraliDocumento NON trovato');
        }

        return $result;
    }

    /**
     * Estrae le linee del documento dall'XML FatturaPA
     */
    protected function extractLinee($xpath)
    {
        $linee = [];

        // Prova vari pattern per trovare le linee
        $nodePatterns = [
            '//DettaglioLinee',
            '//p:DettaglioLinee',
            '//*[local-name()="DettaglioLinee"]',
        ];

        $nodes = null;
        $usedPattern = null;
        foreach ($nodePatterns as $pattern) {
            $nodes = $xpath->query($pattern);
            fe_log('debug_fattura24', "Tentativo pattern '{$pattern}': trovati " . ($nodes ? $nodes->length : 0) . " nodi");
            if ($nodes && $nodes->length > 0) {
                $usedPattern = $pattern;
                fe_log('debug_fattura24', "Usando pattern: {$pattern} con {$nodes->length} linee");
                break;
            }
        }

        if (!$nodes || $nodes->length === 0) {
            fe_log('debug_fattura24', 'ATTENZIONE: Nessuna linea DettaglioLinee trovata nell\'XML');

            // Prova a vedere la struttura del documento
            $beniServizi = $xpath->query('//*[local-name()="DatiBeniServizi"]');
            if ($beniServizi && $beniServizi->length > 0) {
                $bsNode = $beniServizi->item(0);
                $childNodes = [];
                foreach ($bsNode->childNodes as $child) {
                    if ($child->nodeType === XML_ELEMENT_NODE) {
                        $childNodes[] = $child->nodeName;
                    }
                }
                fe_log('debug_fattura24', 'Figli di DatiBeniServizi: ' . implode(', ', $childNodes));
            }
            return $linee;
        }

        foreach ($nodes as $index => $node) {
            // Log dettagliato per ogni nodo trovato
            fe_log('debug_fattura24', "Processando linea {$index}: nodeName=" . $node->nodeName);

            // Estrai valori direttamente dai figli del nodo
            $descrizione = '';
            $quantita = '1';
            $prezzoUnitario = '';
            $prezzoTotale = '';
            $aliquotaIVA = '';
            $natura = '';

            foreach ($node->childNodes as $child) {
                if ($child->nodeType !== XML_ELEMENT_NODE) continue;

                $localName = $child->localName ?: $child->nodeName;
                $value = trim($child->textContent);

                switch ($localName) {
                    case 'Descrizione':
                        $descrizione = $value;
                        break;
                    case 'Quantita':
                        $quantita = $value ?: '1';
                        break;
                    case 'PrezzoUnitario':
                        $prezzoUnitario = $value;
                        break;
                    case 'PrezzoTotale':
                        $prezzoTotale = $value;
                        break;
                    case 'AliquotaIVA':
                        $aliquotaIVA = $value;
                        break;
                    case 'Natura':
                        $natura = $value;
                        break;
                }
            }

            $linea = [
                'descrizione'     => $descrizione,
                'quantita'        => $quantita,
                'prezzo_unitario' => $prezzoUnitario,
                'prezzo_totale'   => $prezzoTotale,
                'aliquota_iva'    => $aliquotaIVA,
                'natura'          => $natura,
            ];

            // Se manca prezzo_totale, calcolalo
            if (empty($linea['prezzo_totale']) && !empty($linea['prezzo_unitario'])) {
                $linea['prezzo_totale'] = floatval($linea['prezzo_unitario']) * floatval($linea['quantita'] ?: 1);
            }

            fe_log('debug_fattura24', "Linea {$index} estratta: " . json_encode($linea, JSON_UNESCAPED_UNICODE));

            $linee[] = $linea;
        }

        return $linee;
    }

    /**
     * Estrae i dati di pagamento dall'XML FatturaPA
     */
    protected function extractPagamento($xpath)
    {
        $result = [
            'modalita' => '',
            'importo' => '',
            'iban' => '',
            'istituto' => '',
        ];

        // Trova DettaglioPagamento
        $pagamentoNode = $this->findNode($xpath, 'DettaglioPagamento');

        if ($pagamentoNode) {
            $values = $this->extractAllValues($pagamentoNode);
            fe_log('debug_fattura24', 'Valori DettaglioPagamento: ' . json_encode($values, JSON_UNESCAPED_UNICODE));

            $result['modalita'] = $values['ModalitaPagamento'] ?? '';
            $result['importo'] = $values['ImportoPagamento'] ?? '';
            $result['iban'] = $values['IBAN'] ?? '';
            $result['istituto'] = $values['IstitutoFinanziario'] ?? '';
        }

        return $result;
    }

    /**
     * Costruisce l'XML nel formato Fattura24
     */
    protected function buildFattura24Xml($cliente, $documento, $linee, $pagamento)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        // Root element
        $root = $dom->createElement('Fattura24');
        $dom->appendChild($root);

        // Document element
        $doc = $dom->createElement('Document');
        $root->appendChild($doc);

        // Tipo documento: FE per fattura elettronica, TD04 per nota di credito
        $tipoDoc = 'FE';
        $feDocType = '';
        if (in_array($documento['tipo_documento'], ['TD04', 'TD05'])) {
            $feDocType = 'TD04'; // Nota di credito
        }

        $this->addElement($dom, $doc, 'DocumentType', $tipoDoc);
        if ($feDocType) {
            $this->addElement($dom, $doc, 'FeDocType', $feDocType);
        }

        // Dati cliente
        $this->addElement($dom, $doc, 'CustomerName', $cliente['denominazione'], true);
        $this->addElement($dom, $doc, 'CustomerAddress', $cliente['indirizzo'], true);
        $this->addElement($dom, $doc, 'CustomerPostcode', $cliente['cap']);
        $this->addElement($dom, $doc, 'CustomerCity', $cliente['comune']);
        $this->addElement($dom, $doc, 'CustomerProvince', $cliente['provincia']);
        $this->addElement($dom, $doc, 'CustomerCountry', $cliente['nazione'] ?: 'IT');
        $this->addElement($dom, $doc, 'CustomerFiscalCode', $cliente['codice_fiscale']);
        $this->addElement($dom, $doc, 'CustomerVatCode', $cliente['partita_iva']);

        // Dati FE specifici
        if (!empty($cliente['pec'])) {
            $this->addElement($dom, $doc, 'FeCustomerPec', $cliente['pec']);
        }
        if (!empty($cliente['codice_destinatario'])) {
            $this->addElement($dom, $doc, 'FeDestinationCode', $cliente['codice_destinatario']);
        }

        // Oggetto/causale
        $this->addElement($dom, $doc, 'Object', $documento['causale'] ?: 'Fattura', true);

        // Data e numero (opzionali)
        if (!empty($documento['data'])) {
            $this->addElement($dom, $doc, 'Date', $documento['data']);
        }
        if (!empty($documento['numero'])) {
            $this->addElement($dom, $doc, 'Number', $documento['numero']);
        }

        // Bollo virtuale
        if (!empty($documento['bollo_virtuale']) && $documento['bollo_virtuale'] === 'SI') {
            $this->addElement($dom, $doc, 'FeVirtualStamp', 'V');
        }

        // Calcola totali
        $totale_imponibile = 0;
        $totale_iva = 0;

        foreach ($linee as $linea) {
            $prezzo = floatval($linea['prezzo_totale'] ?: $linea['prezzo_unitario']);
            $aliquota = floatval($linea['aliquota_iva']);
            $totale_imponibile += $prezzo;
            $totale_iva += $prezzo * ($aliquota / 100);
        }

        $totale = $totale_imponibile + $totale_iva;

        // Se abbiamo ImportoTotaleDocumento, usiamo quello
        if (!empty($documento['importo_totale'])) {
            $totale = floatval($documento['importo_totale']);
        }

        $this->addElement($dom, $doc, 'TotalWithoutTax', number_format($totale_imponibile, 2, '.', ''));
        $this->addElement($dom, $doc, 'VatAmount', number_format($totale_iva, 2, '.', ''));
        $this->addElement($dom, $doc, 'Total', number_format($totale, 2, '.', ''));

        // Pagamento
        if (!empty($pagamento['modalita'])) {
            $this->addElement($dom, $doc, 'FePaymentCode', $pagamento['modalita']);
        }
        if (!empty($pagamento['istituto'])) {
            $this->addElement($dom, $doc, 'PaymentMethodName', $pagamento['istituto']);
        }
        if (!empty($pagamento['iban'])) {
            $this->addElement($dom, $doc, 'PaymentMethodDescription', $pagamento['iban']);
        }

        // Non inviare email automaticamente
        $this->addElement($dom, $doc, 'SendEmail', 'false');

        // Righe
        $rows = $dom->createElement('Rows');
        $doc->appendChild($rows);

        foreach ($linee as $linea) {
            $row = $dom->createElement('Row');
            $rows->appendChild($row);

            $this->addElement($dom, $row, 'Description', $linea['descrizione'], true);
            $this->addElement($dom, $row, 'Qty', $linea['quantita'] ?: '1');
            $this->addElement($dom, $row, 'Price', number_format(floatval($linea['prezzo_unitario']), 2, '.', ''));

            $aliquota = floatval($linea['aliquota_iva']);
            $this->addElement($dom, $row, 'VatCode', intval($aliquota));
            $this->addElement($dom, $row, 'VatDescription', $aliquota . '%');

            // Natura IVA per aliquote a 0%
            if ($aliquota == 0 && !empty($linea['natura'])) {
                $this->addElement($dom, $row, 'FeVatNature', $linea['natura']);
            }
        }

        return $dom->saveXML();
    }

    /**
     * Aggiunge un elemento al DOM
     */
    protected function addElement($dom, $parent, $name, $value, $cdata = false)
    {
        if ($value === null || $value === '') {
            return;
        }

        $element = $dom->createElement($name);

        if ($cdata) {
            $element->appendChild($dom->createCDATASection($value));
        } else {
            $element->appendChild($dom->createTextNode($value));
        }

        $parent->appendChild($element);
    }

    /**
     * Controlla lo stato di una fattura
     *
     * NOTA: Fattura24 non ha un'API pubblica per controllare lo stato SDI.
     * Restituisce sempre lo stato "IN_ATTESA" perché l'utente deve verificare
     * manualmente nel pannello Fattura24.
     *
     * @param string $identificativo_sdi ID SDI o ID Fattura24
     * @return array
     */
    public function checkInvoiceStatus($identificativo_sdi)
    {
        // Fattura24 non ha un'API pubblica per controllare lo stato
        // L'utente deve verificare manualmente nel pannello
        return [
            'stato'        => FE_STATO_INVIATA,
            'message'      => 'Verifica lo stato nel pannello Fattura24',
            'raw_response' => ['note' => 'Fattura24 non fornisce API per lo stato SDI'],
        ];
    }

    /**
     * Scarica le fatture passive (acquisto)
     *
     * NOTA: Fattura24 non ha un'API pubblica per scaricare le fatture passive.
     *
     * @param string|null $from_date Data di inizio
     * @param string|null $to_date Data di fine
     * @return array
     */
    public function downloadPassiveInvoices($from_date = null, $to_date = null)
    {
        // Fattura24 non fornisce API per le fatture passive
        $this->last_error = 'Fattura24 non fornisce API per scaricare le fatture passive. Usa il pannello web.';
        return [];
    }

    /**
     * Scarica le notifiche SDI
     *
     * NOTA: Fattura24 non ha un'API pubblica per le notifiche.
     *
     * @param string|null $from_date Data di inizio
     * @return array
     */
    public function downloadNotifications($from_date = null)
    {
        // Fattura24 non fornisce API per le notifiche
        return [];
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

        // Fattura24 usa returnCode (0 o 1 = successo, -1 = errore) e description
        $returnCode = $xpath->evaluate('string(//returnCode)');
        $description = $xpath->evaluate('string(//description)');
        $docId = $xpath->evaluate('string(//docId)');
        $docNumber = $xpath->evaluate('string(//docNumber)');

        // Determina successo: returnCode=0 o 1 = successo, -1 = errore
        $isSuccess = in_array($returnCode, ['0', '1']);

        $result = [
            'success'     => $isSuccess,
            'returnCode'  => $returnCode ?: null,
            'description' => $description ?: null,
            'error'       => $isSuccess ? null : $description,
            'docId'       => $docId ?: null,
            'docNumber'   => $docNumber ?: null,
            'id'          => $docId ?: null,
        ];

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
            if ($result['returnCode'] == '1') {
                return true;
            } else {
                $this->last_error = $result['description'] ?? 'API Key non valida';
                return false;
            }
        }

        return false;
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
            if (@$doc->loadXML($payload)) {
                $data = $this->parseResponse($payload);
            } else {
                $this->last_error = 'Payload non valido';
                return false;
            }
        }

        return $data;
    }

    /**
     * Scarica il PDF di un documento
     *
     * @param string $docId ID del documento
     * @return string|false Contenuto PDF o false
     */
    public function downloadPdf($docId)
    {
        if (empty($this->config['api_key'])) {
            $this->last_error = 'API Key non configurata';
            return false;
        }

        $endpoint = self::API_BASE_URL . '/GetFile';

        $data = [
            'apiKey' => $this->config['api_key'],
            'docId'  => $docId,
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response === false) {
            return false;
        }

        // La risposta dovrebbe essere il PDF diretto
        return $response;
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
            'description' => 'Crea fatture elettroniche in Fattura24. NOTA: L\'invio allo SDI deve essere completato manualmente dal pannello Fattura24.',
            'icon'        => 'fa-file-invoice',
            'color'       => '#ff6b35',
            'website'     => 'https://www.fattura24.com/',
            'features'    => [
                'Creazione fatture tramite API',
                'Conservazione sostitutiva inclusa',
                'Dashboard di monitoraggio',
                'Supporto tecnico italiano',
            ],
            'pricing' => [
                'Piano gratuito disponibile',
                'Piani a partire da €4.90/mese',
            ],
            'requirements' => [
                'Account su Fattura24.com',
                'API Key (da Configurazione > App e servizi esterni > API)',
            ],
            'limitations' => [
                'L\'invio allo SDI deve essere completato manualmente',
                'Non supporta ritenute d\'acconto e casse previdenziali',
            ],
            'fields' => [
                [
                    'name'        => 'fe_fattura24_api_key',
                    'label'       => 'API Key',
                    'type'        => 'text',
                    'required'    => true,
                    'placeholder' => 'La tua API Key di Fattura24',
                    'help'        => 'Trova la tua API Key in Fattura24: Configurazione > App e servizi esterni > API',
                    'col'         => 12,
                ],
            ],
        ];
    }
}

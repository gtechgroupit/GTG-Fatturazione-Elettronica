<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Classe per la generazione del file XML FatturaPA
 *
 * Genera file XML conformi alle specifiche tecniche dell'Agenzia delle Entrate
 * per la fatturazione elettronica (versione 1.2.2)
 *
 * @author GTech Group IT
 * @version 1.0.0
 */
class FatturaPA_Generator
{
    /**
     * @var object CodeIgniter instance
     */
    protected $CI;

    /**
     * @var DOMDocument Documento XML
     */
    protected $xml;

    /**
     * @var DOMElement Elemento radice
     */
    protected $root;

    /**
     * @var array Dati della fattura
     */
    protected $invoice_data;

    /**
     * @var array Dati del cliente
     */
    protected $client_data;

    /**
     * @var array Configurazione azienda
     */
    protected $company_data;

    /**
     * @var string Formato trasmissione (FPA12 o FPR12)
     */
    protected $formato_trasmissione;

    /**
     * @var string Tipo documento
     */
    protected $tipo_documento;

    /**
     * @var array Errori di validazione
     */
    protected $errors = [];

    /**
     * Namespace per FatturaPA
     */
    const NAMESPACE_FPA = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2';
    const NAMESPACE_DS = 'http://www.w3.org/2000/09/xmldsig#';
    const NAMESPACE_XSI = 'http://www.w3.org/2001/XMLSchema-instance';
    const SCHEMA_LOCATION = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2.2/Schema_del_file_xml_FatturaPA_versione_1.2.2.xsd';

    /**
     * Costruttore
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        // Carica l'helper se non già caricato
        if (!function_exists('fe_normalizza_partita_iva')) {
            $this->CI->load->helper('fatturazione_elettronica/fatturazione_elettronica');
        }

        $this->loadCompanyData();
    }

    /**
     * Carica i dati aziendali dalla configurazione
     */
    protected function loadCompanyData()
    {
        $this->company_data = [
            'denominazione'           => get_option('fe_denominazione') ?: get_option('invoice_company_name'),
            'partita_iva'             => fe_normalizza_partita_iva(get_option('fe_partita_iva') ?: get_option('company_vat')),
            'codice_fiscale'          => get_option('fe_codice_fiscale') ?: get_option('fe_partita_iva'),
            'regime_fiscale'          => get_option('fe_regime_fiscale') ?: 'RF01',
            'indirizzo'               => get_option('fe_indirizzo') ?: get_option('invoice_company_address'),
            'cap'                     => get_option('fe_cap') ?: '',
            'comune'                  => get_option('fe_comune') ?: get_option('invoice_company_city'),
            'provincia'               => get_option('fe_provincia') ?: '',
            'nazione'                 => get_option('fe_nazione') ?: 'IT',
            'telefono'                => get_option('fe_telefono') ?: get_option('company_phone'),
            'email'                   => get_option('fe_email') ?: get_option('invoice_company_email'),
            'pec'                     => get_option('fe_pec') ?: '',
            'codice_destinatario'     => get_option('fe_codice_destinatario') ?: '',
            'rea_ufficio'             => get_option('fe_rea_ufficio') ?: '',
            'rea_numero'              => get_option('fe_rea_numero') ?: '',
            'capitale_sociale'        => get_option('fe_capitale_sociale') ?: '',
            'socio_unico'             => get_option('fe_socio_unico') ?: '',
            'stato_liquidazione'      => get_option('fe_stato_liquidazione') ?: 'LN',
        ];
    }

    /**
     * Genera la fattura elettronica XML da una fattura Perfex
     *
     * @param int $invoice_id ID della fattura Perfex
     * @param string $tipo_documento Tipo documento (TD01, TD04, ecc.)
     * @param string $progressivo Progressivo invio
     * @return string|false XML generato o false in caso di errore
     */
    public function generateFromInvoice($invoice_id, $tipo_documento = 'TD01', $progressivo = null)
    {
        $this->CI->load->model('invoices_model');

        // Carica i dati della fattura
        $invoice = $this->CI->invoices_model->get($invoice_id);

        if (!$invoice) {
            $this->errors[] = 'Fattura non trovata';
            return false;
        }

        // Carica i dati del cliente
        $this->CI->load->model('clients_model');
        $client = $this->CI->clients_model->get($invoice->clientid);

        if (!$client) {
            $this->errors[] = 'Cliente non trovato';
            return false;
        }

        $this->invoice_data = $invoice;
        $this->client_data = $client;
        $this->tipo_documento = $tipo_documento;

        // Determina il formato trasmissione
        $this->formato_trasmissione = fe_get_formato_trasmissione($client->userid);

        // Genera il progressivo se non fornito
        if ($progressivo === null) {
            $progressivo = fe_genera_progressivo();
        }

        return $this->generate($progressivo);
    }

    /**
     * Genera la nota di credito elettronica XML
     *
     * @param int $credit_note_id ID della nota di credito Perfex
     * @param string $progressivo Progressivo invio
     * @return string|false XML generato o false in caso di errore
     */
    public function generateFromCreditNote($credit_note_id, $progressivo = null)
    {
        $this->CI->load->model('credit_notes_model');

        $credit_note = $this->CI->credit_notes_model->get($credit_note_id);

        if (!$credit_note) {
            $this->errors[] = 'Nota di credito non trovata';
            return false;
        }

        // Carica i dati del cliente
        $this->CI->load->model('clients_model');
        $client = $this->CI->clients_model->get($credit_note->clientid);

        if (!$client) {
            $this->errors[] = 'Cliente non trovato';
            return false;
        }

        // Converti la nota di credito in formato simile alla fattura
        $this->invoice_data = $this->convertCreditNoteToInvoiceFormat($credit_note);
        $this->client_data = $client;
        $this->tipo_documento = 'TD04';

        $this->formato_trasmissione = fe_get_formato_trasmissione($client->userid);

        if ($progressivo === null) {
            $progressivo = fe_genera_progressivo();
        }

        return $this->generate($progressivo);
    }

    /**
     * Converte una nota di credito nel formato fattura
     *
     * @param object $credit_note Nota di credito
     * @return object
     */
    protected function convertCreditNoteToInvoiceFormat($credit_note)
    {
        $this->CI->load->model('credit_notes_model');

        $data = new stdClass();
        $data->id = $credit_note->id;
        $data->number = $credit_note->number;
        $data->prefix = $credit_note->prefix;
        $data->date = $credit_note->date;
        $data->duedate = $credit_note->date;
        $data->clientid = $credit_note->clientid;
        $data->subtotal = $credit_note->subtotal;
        $data->total_tax = $credit_note->total_tax;
        $data->total = $credit_note->total;
        $data->currency = $credit_note->currency;
        $data->items = $this->CI->credit_notes_model->get_items($credit_note->id);
        $data->reference_no = $credit_note->reference_no;

        // Dati fattura di riferimento se presente
        if (!empty($credit_note->invoice_id)) {
            $this->CI->load->model('invoices_model');
            $original_invoice = $this->CI->invoices_model->get($credit_note->invoice_id);
            if ($original_invoice) {
                $data->riferimento_fattura = [
                    'numero' => $original_invoice->number,
                    'data'   => $original_invoice->date,
                ];
            }
        }

        return $data;
    }

    /**
     * Genera l'XML FatturaPA
     *
     * @param string $progressivo Progressivo invio
     * @return string|false
     */
    protected function generate($progressivo)
    {
        // Valida i dati prima della generazione
        if (!$this->validate()) {
            return false;
        }

        // Crea il documento XML
        $this->xml = new DOMDocument('1.0', 'UTF-8');
        $this->xml->formatOutput = true;

        // Crea l'elemento radice
        $this->root = $this->xml->createElementNS(self::NAMESPACE_FPA, 'p:FatturaElettronica');
        $this->root->setAttribute('versione', $this->formato_trasmissione);
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ds', self::NAMESPACE_DS);
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', self::NAMESPACE_XSI);
        $this->root->setAttributeNS(self::NAMESPACE_XSI, 'xsi:schemaLocation', self::SCHEMA_LOCATION);
        $this->xml->appendChild($this->root);

        // Genera le sezioni
        $this->generateFatturaElettronicaHeader($progressivo);
        $this->generateFatturaElettronicaBody();

        return $this->xml->saveXML();
    }

    /**
     * Valida i dati necessari per la generazione
     *
     * @return bool
     */
    protected function validate()
    {
        $this->errors = [];

        // Valida dati azienda
        if (empty($this->company_data['partita_iva'])) {
            $this->errors[] = 'Partita IVA azienda non configurata';
        }

        if (empty($this->company_data['denominazione'])) {
            $this->errors[] = 'Denominazione azienda non configurata';
        }

        // Valida dati cliente
        $client_piva = $this->client_data->vat ?? '';
        $client_cf = get_client_meta($this->client_data->userid, 'fe_codice_fiscale') ?: '';
        $codice_dest = get_client_meta($this->client_data->userid, 'fe_codice_destinatario') ?: '';
        $client_pec = get_client_meta($this->client_data->userid, 'fe_pec') ?: '';

        // Per clienti italiani serve almeno P.IVA o CF
        if (empty($client_piva) && empty($client_cf)) {
            $this->errors[] = 'Il cliente deve avere Partita IVA o Codice Fiscale';
        }

        // Serve almeno codice destinatario o PEC
        if (empty($codice_dest) && empty($client_pec)) {
            // Se non c'è né codice né PEC, usiamo 0000000 (per persone fisiche senza PEC)
            // Questo è valido secondo le specifiche SDI
        }

        return empty($this->errors);
    }

    /**
     * Genera l'header della fattura elettronica
     *
     * @param string $progressivo Progressivo invio
     */
    protected function generateFatturaElettronicaHeader($progressivo)
    {
        $header = $this->xml->createElement('FatturaElettronicaHeader');
        $this->root->appendChild($header);

        // DatiTrasmissione
        $this->generateDatiTrasmissione($header, $progressivo);

        // CedentePrestatore
        $this->generateCedentePrestatore($header);

        // CessionarioCommittente
        $this->generateCessionarioCommittente($header);
    }

    /**
     * Genera la sezione DatiTrasmissione
     */
    protected function generateDatiTrasmissione($parent, $progressivo)
    {
        $datiTrasmissione = $this->xml->createElement('DatiTrasmissione');
        $parent->appendChild($datiTrasmissione);

        // IdTrasmittente
        $idTrasmittente = $this->xml->createElement('IdTrasmittente');
        $datiTrasmissione->appendChild($idTrasmittente);

        $this->addElement($idTrasmittente, 'IdPaese', $this->company_data['nazione']);
        $this->addElement($idTrasmittente, 'IdCodice', $this->company_data['partita_iva']);

        // ProgressivoInvio
        $this->addElement($datiTrasmissione, 'ProgressivoInvio', $progressivo);

        // FormatoTrasmissione
        $this->addElement($datiTrasmissione, 'FormatoTrasmissione', $this->formato_trasmissione);

        // CodiceDestinatario
        $codice_destinatario = get_client_meta($this->client_data->userid, 'fe_codice_destinatario');
        if (empty($codice_destinatario)) {
            $codice_destinatario = '0000000'; // Default per privati
        }
        $this->addElement($datiTrasmissione, 'CodiceDestinatario', strtoupper($codice_destinatario));

        // ContattiTrasmittente (opzionale)
        if (!empty($this->company_data['telefono']) || !empty($this->company_data['email'])) {
            $contatti = $this->xml->createElement('ContattiTrasmittente');
            $datiTrasmissione->appendChild($contatti);

            if (!empty($this->company_data['telefono'])) {
                $this->addElement($contatti, 'Telefono', $this->company_data['telefono']);
            }
            if (!empty($this->company_data['email'])) {
                $this->addElement($contatti, 'Email', $this->company_data['email']);
            }
        }

        // PECDestinatario (se codice destinatario è 0000000 e c'è PEC)
        if ($codice_destinatario === '0000000') {
            $pec = get_client_meta($this->client_data->userid, 'fe_pec');
            if (!empty($pec)) {
                $this->addElement($datiTrasmissione, 'PECDestinatario', strtolower($pec));
            }
        }
    }

    /**
     * Genera la sezione CedentePrestatore (dati azienda)
     */
    protected function generateCedentePrestatore($parent)
    {
        $cedente = $this->xml->createElement('CedentePrestatore');
        $parent->appendChild($cedente);

        // DatiAnagrafici
        $datiAnagrafici = $this->xml->createElement('DatiAnagrafici');
        $cedente->appendChild($datiAnagrafici);

        // IdFiscaleIVA
        $idFiscale = $this->xml->createElement('IdFiscaleIVA');
        $datiAnagrafici->appendChild($idFiscale);
        $this->addElement($idFiscale, 'IdPaese', $this->company_data['nazione']);
        $this->addElement($idFiscale, 'IdCodice', $this->company_data['partita_iva']);

        // CodiceFiscale (opzionale)
        if (!empty($this->company_data['codice_fiscale'])) {
            $this->addElement($datiAnagrafici, 'CodiceFiscale', strtoupper($this->company_data['codice_fiscale']));
        }

        // Anagrafica
        $anagrafica = $this->xml->createElement('Anagrafica');
        $datiAnagrafici->appendChild($anagrafica);
        $this->addElement($anagrafica, 'Denominazione', fe_clean_string($this->company_data['denominazione'], 80));

        // RegimeFiscale
        $this->addElement($datiAnagrafici, 'RegimeFiscale', $this->company_data['regime_fiscale']);

        // Sede
        $sede = $this->xml->createElement('Sede');
        $cedente->appendChild($sede);
        $this->addElement($sede, 'Indirizzo', fe_clean_string($this->company_data['indirizzo'], 60));
        $this->addElement($sede, 'CAP', $this->company_data['cap']);
        $this->addElement($sede, 'Comune', fe_clean_string($this->company_data['comune'], 60));

        if (!empty($this->company_data['provincia']) && $this->company_data['nazione'] == 'IT') {
            $this->addElement($sede, 'Provincia', strtoupper($this->company_data['provincia']));
        }

        $this->addElement($sede, 'Nazione', $this->company_data['nazione']);

        // IscrizioneREA (opzionale)
        if (!empty($this->company_data['rea_ufficio']) && !empty($this->company_data['rea_numero'])) {
            $rea = $this->xml->createElement('IscrizioneREA');
            $cedente->appendChild($rea);

            $this->addElement($rea, 'Ufficio', strtoupper($this->company_data['rea_ufficio']));
            $this->addElement($rea, 'NumeroREA', $this->company_data['rea_numero']);

            if (!empty($this->company_data['capitale_sociale'])) {
                $this->addElement($rea, 'CapitaleSociale', fe_format_amount($this->company_data['capitale_sociale']));
            }

            if (!empty($this->company_data['socio_unico'])) {
                $this->addElement($rea, 'SocioUnico', $this->company_data['socio_unico']);
            }

            $this->addElement($rea, 'StatoLiquidazione', $this->company_data['stato_liquidazione']);
        }

        // Contatti (opzionale)
        if (!empty($this->company_data['telefono']) || !empty($this->company_data['email'])) {
            $contatti = $this->xml->createElement('Contatti');
            $cedente->appendChild($contatti);

            if (!empty($this->company_data['telefono'])) {
                $this->addElement($contatti, 'Telefono', $this->company_data['telefono']);
            }
            if (!empty($this->company_data['email'])) {
                $this->addElement($contatti, 'Email', $this->company_data['email']);
            }
        }
    }

    /**
     * Genera la sezione CessionarioCommittente (dati cliente)
     */
    protected function generateCessionarioCommittente($parent)
    {
        $cessionario = $this->xml->createElement('CessionarioCommittente');
        $parent->appendChild($cessionario);

        // DatiAnagrafici
        $datiAnagrafici = $this->xml->createElement('DatiAnagrafici');
        $cessionario->appendChild($datiAnagrafici);

        $client_piva = fe_normalizza_partita_iva($this->client_data->vat ?? '');
        $client_cf = get_client_meta($this->client_data->userid, 'fe_codice_fiscale') ?: '';
        $client_country = $this->client_data->country ?? 'Italy';

        // Determina il codice paese
        $paese = 'IT';
        if (!empty($client_country)) {
            $this->CI->db->where('short_name', $client_country);
            $this->CI->db->or_where('long_name', $client_country);
            $country_row = $this->CI->db->get(db_prefix() . 'countries')->row();
            if ($country_row) {
                $paese = strtoupper($country_row->iso2);
            }
        }

        // IdFiscaleIVA (solo se ha P.IVA)
        if (!empty($client_piva)) {
            $idFiscale = $this->xml->createElement('IdFiscaleIVA');
            $datiAnagrafici->appendChild($idFiscale);
            $this->addElement($idFiscale, 'IdPaese', $paese);
            $this->addElement($idFiscale, 'IdCodice', $client_piva);
        }

        // CodiceFiscale (per clienti italiani)
        if (!empty($client_cf) && $paese == 'IT') {
            $this->addElement($datiAnagrafici, 'CodiceFiscale', strtoupper($client_cf));
        } elseif (!empty($client_piva) && $paese == 'IT' && strlen($client_piva) == 11) {
            // Usa la P.IVA come CF se non c'è CF specifico
            $this->addElement($datiAnagrafici, 'CodiceFiscale', strtoupper($client_piva));
        }

        // Anagrafica
        $anagrafica = $this->xml->createElement('Anagrafica');
        $datiAnagrafici->appendChild($anagrafica);

        $denominazione = $this->client_data->company ?? '';
        if (empty($denominazione)) {
            $denominazione = trim(($this->client_data->firstname ?? '') . ' ' . ($this->client_data->lastname ?? ''));
        }

        $this->addElement($anagrafica, 'Denominazione', fe_clean_string($denominazione, 80));

        // Sede
        $sede = $this->xml->createElement('Sede');
        $cessionario->appendChild($sede);

        $indirizzo = $this->client_data->address ?? '';
        if (empty($indirizzo)) {
            $indirizzo = 'N/D';
        }

        $cap = $this->client_data->zip ?? '';
        if (empty($cap) || $paese != 'IT') {
            $cap = '00000';
        }

        $comune = $this->client_data->city ?? '';
        if (empty($comune)) {
            $comune = 'N/D';
        }

        $this->addElement($sede, 'Indirizzo', fe_clean_string($indirizzo, 60));
        $this->addElement($sede, 'CAP', $cap);
        $this->addElement($sede, 'Comune', fe_clean_string($comune, 60));

        $provincia = $this->client_data->state ?? '';
        if (!empty($provincia) && $paese == 'IT' && strlen($provincia) == 2) {
            $this->addElement($sede, 'Provincia', strtoupper($provincia));
        }

        $this->addElement($sede, 'Nazione', $paese);
    }

    /**
     * Genera il body della fattura elettronica
     */
    protected function generateFatturaElettronicaBody()
    {
        $body = $this->xml->createElement('FatturaElettronicaBody');
        $this->root->appendChild($body);

        // DatiGenerali
        $this->generateDatiGenerali($body);

        // DatiBeniServizi
        $this->generateDatiBeniServizi($body);

        // DatiPagamento
        $this->generateDatiPagamento($body);

        // Allegati (opzionale)
        // $this->generateAllegati($body);
    }

    /**
     * Genera la sezione DatiGenerali
     */
    protected function generateDatiGenerali($parent)
    {
        $datiGenerali = $this->xml->createElement('DatiGenerali');
        $parent->appendChild($datiGenerali);

        // DatiGeneraliDocumento
        $datiDoc = $this->xml->createElement('DatiGeneraliDocumento');
        $datiGenerali->appendChild($datiDoc);

        // TipoDocumento
        $this->addElement($datiDoc, 'TipoDocumento', $this->tipo_documento);

        // Divisa
        $currency = $this->invoice_data->currency_name ?? 'EUR';
        $this->addElement($datiDoc, 'Divisa', strtoupper($currency));

        // Data
        $this->addElement($datiDoc, 'Data', fe_format_date($this->invoice_data->date));

        // Numero
        $numero = ($this->invoice_data->prefix ?? '') . $this->invoice_data->number;
        $this->addElement($datiDoc, 'Numero', fe_clean_string($numero, 20));

        // Bollo virtuale (se applicabile)
        $soglia_bollo = (float)get_option('fe_bollo_virtuale_soglia');
        $importo_bollo = (float)get_option('fe_bollo_virtuale_importo');

        if ($soglia_bollo > 0 && (float)$this->invoice_data->total > $soglia_bollo && $this->hasExemptItems()) {
            $bollo = $this->xml->createElement('DatiBollo');
            $datiDoc->appendChild($bollo);
            $this->addElement($bollo, 'BolloVirtuale', 'SI');
            $this->addElement($bollo, 'ImportoBollo', fe_format_amount($importo_bollo));
        }

        // Causale (opzionale)
        $note = $this->invoice_data->clientnote ?? '';
        if (!empty($note)) {
            // Split in blocchi da 200 caratteri
            $note = fe_clean_string($note, 800);
            $chunks = str_split($note, 200);
            foreach ($chunks as $chunk) {
                if (!empty(trim($chunk))) {
                    $this->addElement($datiDoc, 'Causale', $chunk);
                }
            }
        }

        // DatiOrdineAcquisto (opzionale)
        $reference = $this->invoice_data->reference_no ?? '';
        if (!empty($reference)) {
            $this->generateDatiDocumentoCorrelato($datiGenerali, 'DatiOrdineAcquisto', $reference);
        }

        // DatiFattureCollegate (per note di credito)
        if ($this->tipo_documento == 'TD04' && isset($this->invoice_data->riferimento_fattura)) {
            $datiCollegate = $this->xml->createElement('DatiFattureCollegate');
            $datiGenerali->appendChild($datiCollegate);

            $this->addElement($datiCollegate, 'IdDocumento', $this->invoice_data->riferimento_fattura['numero']);
            $this->addElement($datiCollegate, 'Data', fe_format_date($this->invoice_data->riferimento_fattura['data']));
        }
    }

    /**
     * Genera DatiDocumentoCorrelato
     */
    protected function generateDatiDocumentoCorrelato($parent, $tipo, $riferimento)
    {
        $dati = $this->xml->createElement($tipo);
        $parent->appendChild($dati);

        $this->addElement($dati, 'IdDocumento', fe_clean_string($riferimento, 20));
    }

    /**
     * Genera la sezione DatiBeniServizi
     */
    protected function generateDatiBeniServizi($parent)
    {
        $datiBeniServizi = $this->xml->createElement('DatiBeniServizi');
        $parent->appendChild($datiBeniServizi);

        // Carica gli items della fattura
        $items = $this->invoice_data->items ?? [];

        if (empty($items)) {
            $this->CI->load->model('invoices_model');
            $items = $this->CI->invoices_model->get_invoice_items($this->invoice_data->id);
        }

        $lineNumber = 0;
        $riepilogoIVA = [];

        foreach ($items as $item) {
            $lineNumber++;

            $dettaglio = $this->xml->createElement('DettaglioLinee');
            $datiBeniServizi->appendChild($dettaglio);

            // NumeroLinea
            $this->addElement($dettaglio, 'NumeroLinea', $lineNumber);

            // Descrizione
            $descrizione = fe_clean_string($item->description ?? '', 1000);
            if (empty($descrizione)) {
                $descrizione = 'Articolo ' . $lineNumber;
            }
            $this->addElement($dettaglio, 'Descrizione', $descrizione);

            // Quantita
            $quantita = (float)($item->qty ?? 1);
            $this->addElement($dettaglio, 'Quantita', fe_format_amount($quantita));

            // UnitaMisura (opzionale)
            $unit = $item->unit ?? '';
            if (!empty($unit)) {
                $this->addElement($dettaglio, 'UnitaMisura', fe_clean_string($unit, 10));
            }

            // PrezzoUnitario
            $prezzoUnitario = (float)($item->rate ?? 0);
            $this->addElement($dettaglio, 'PrezzoUnitario', fe_format_amount($prezzoUnitario));

            // ScontoMaggiorazione (se presente)
            $discount = (float)($item->discount_percent ?? 0);
            if ($discount > 0) {
                $sconto = $this->xml->createElement('ScontoMaggiorazione');
                $dettaglio->appendChild($sconto);
                $this->addElement($sconto, 'Tipo', 'SC');
                $this->addElement($sconto, 'Percentuale', fe_format_amount($discount));
            }

            // PrezzoTotale
            $prezzoTotale = $prezzoUnitario * $quantita;
            if ($discount > 0) {
                $prezzoTotale = $prezzoTotale * (1 - $discount / 100);
            }
            $this->addElement($dettaglio, 'PrezzoTotale', fe_format_amount($prezzoTotale));

            // AliquotaIVA
            $aliquotaIVA = (float)($item->taxrate ?? 0);
            $this->addElement($dettaglio, 'AliquotaIVA', fe_format_amount($aliquotaIVA));

            // Natura (solo per aliquota 0)
            $natura = null;
            if ($aliquotaIVA == 0) {
                $taxname = $item->taxname ?? '';
                $natura = fe_get_natura_iva($aliquotaIVA, $taxname);
                if ($natura) {
                    $this->addElement($dettaglio, 'Natura', $natura);
                }
            }

            // Accumula per riepilogo IVA
            $key = $aliquotaIVA . '_' . ($natura ?? '');
            if (!isset($riepilogoIVA[$key])) {
                $riepilogoIVA[$key] = [
                    'aliquota'   => $aliquotaIVA,
                    'natura'     => $natura,
                    'imponibile' => 0,
                    'imposta'    => 0,
                ];
            }
            $riepilogoIVA[$key]['imponibile'] += $prezzoTotale;
            $riepilogoIVA[$key]['imposta'] += $prezzoTotale * $aliquotaIVA / 100;
        }

        // DatiRiepilogo
        foreach ($riepilogoIVA as $riepilogo) {
            $datiRiepilogo = $this->xml->createElement('DatiRiepilogo');
            $datiBeniServizi->appendChild($datiRiepilogo);

            $this->addElement($datiRiepilogo, 'AliquotaIVA', fe_format_amount($riepilogo['aliquota']));

            if (!empty($riepilogo['natura'])) {
                $this->addElement($datiRiepilogo, 'Natura', $riepilogo['natura']);
            }

            $this->addElement($datiRiepilogo, 'ImponibileImporto', fe_format_amount($riepilogo['imponibile']));
            $this->addElement($datiRiepilogo, 'Imposta', fe_format_amount($riepilogo['imposta']));

            // EsigibilitaIVA
            $split_payment = get_client_meta($this->client_data->userid, 'fe_split_payment');
            if ($split_payment == '1') {
                $this->addElement($datiRiepilogo, 'EsigibilitaIVA', 'S'); // Split payment
            } else {
                $this->addElement($datiRiepilogo, 'EsigibilitaIVA', 'I'); // Immediata
            }

            // RiferimentoNormativo (per aliquota 0)
            if ($riepilogo['aliquota'] == 0 && !empty($riepilogo['natura'])) {
                $this->addElement($datiRiepilogo, 'RiferimentoNormativo', $this->getNormativaByNatura($riepilogo['natura']));
            }
        }
    }

    /**
     * Genera la sezione DatiPagamento
     */
    protected function generateDatiPagamento($parent)
    {
        $datiPagamento = $this->xml->createElement('DatiPagamento');
        $parent->appendChild($datiPagamento);

        // CondizioniPagamento
        $condizioni = get_option('fe_default_condizioni_pagamento') ?: 'TP02';
        $this->addElement($datiPagamento, 'CondizioniPagamento', $condizioni);

        // DettaglioPagamento
        $dettaglio = $this->xml->createElement('DettaglioPagamento');
        $datiPagamento->appendChild($dettaglio);

        // ModalitaPagamento
        $payment_mode = $this->invoice_data->payment_method ?? '';
        $modalita = get_option('fe_default_modalita_pagamento') ?: 'MP05';

        if (!empty($payment_mode)) {
            $this->CI->load->model('payment_modes_model');
            $payment = $this->CI->payment_modes_model->get($payment_mode);
            if ($payment) {
                $modalita = fe_get_modalita_pagamento($payment->name);
            }
        }

        $this->addElement($dettaglio, 'ModalitaPagamento', $modalita);

        // DataScadenzaPagamento
        $scadenza = $this->invoice_data->duedate ?? $this->invoice_data->date;
        $this->addElement($dettaglio, 'DataScadenzaPagamento', fe_format_date($scadenza));

        // ImportoPagamento
        $this->addElement($dettaglio, 'ImportoPagamento', fe_format_amount($this->invoice_data->total));

        // IBAN (opzionale)
        $iban = get_option('company_iban');
        if (!empty($iban)) {
            $this->addElement($dettaglio, 'IBAN', strtoupper(preg_replace('/\s+/', '', $iban)));
        }
    }

    /**
     * Verifica se ci sono items esenti IVA
     *
     * @return bool
     */
    protected function hasExemptItems()
    {
        $items = $this->invoice_data->items ?? [];

        foreach ($items as $item) {
            $aliquota = (float)($item->taxrate ?? 0);
            if ($aliquota == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ottiene il riferimento normativo in base alla natura
     *
     * @param string $natura Codice natura
     * @return string
     */
    protected function getNormativaByNatura($natura)
    {
        $normative = [
            'N1'   => 'Operazione esclusa ex art. 15 DPR 633/72',
            'N2.1' => 'Operazione non soggetta ex art. 7 DPR 633/72',
            'N2.2' => 'Operazione non soggetta - altri casi',
            'N3.1' => 'Non imponibile - esportazioni',
            'N3.2' => 'Non imponibile - cessioni intracomunitarie',
            'N3.3' => 'Non imponibile - cessioni verso San Marino',
            'N3.4' => 'Non imponibile - operazioni assimilate alle cessioni all\'esportazione',
            'N3.5' => 'Non imponibile - a seguito di dichiarazioni d\'intento',
            'N3.6' => 'Non imponibile - altre operazioni',
            'N4'   => 'Operazione esente ex art. 10 DPR 633/72',
            'N5'   => 'Operazione soggetta a regime del margine',
            'N6.1' => 'Inversione contabile - cessione di rottami',
            'N6.2' => 'Inversione contabile - cessione di oro e argento puro',
            'N6.3' => 'Inversione contabile - subappalto nel settore edile',
            'N6.4' => 'Inversione contabile - cessione di fabbricati',
            'N6.5' => 'Inversione contabile - cessione di telefoni cellulari',
            'N6.6' => 'Inversione contabile - cessione di prodotti elettronici',
            'N6.7' => 'Inversione contabile - prestazioni comparto edile',
            'N6.8' => 'Inversione contabile - operazioni settore energetico',
            'N6.9' => 'Inversione contabile - altri casi',
            'N7'   => 'IVA assolta in altro stato UE',
        ];

        return $normative[$natura] ?? 'Operazione senza applicazione dell\'IVA';
    }

    /**
     * Aggiunge un elemento al documento XML
     *
     * @param DOMElement $parent Elemento padre
     * @param string $name Nome elemento
     * @param string $value Valore
     * @return DOMElement
     */
    protected function addElement($parent, $name, $value)
    {
        $element = $this->xml->createElement($name, fe_xml_encode($value));
        $parent->appendChild($element);
        return $element;
    }

    /**
     * Ottiene gli errori di validazione
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Valida un XML contro lo schema XSD FatturaPA
     *
     * @param string $xml Contenuto XML
     * @return bool
     */
    public function validateXML($xml)
    {
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadXML($xml);

        // Per la validazione completa servirebbe lo schema XSD
        // che può essere scaricato da:
        // https://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2.2/Schema_del_file_xml_FatturaPA_v1.2.2.xsd

        // Validazione base: controlla che sia un XML ben formato
        $errors = libxml_get_errors();
        libxml_clear_errors();

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errors[] = "Riga {$error->line}: {$error->message}";
            }
            return false;
        }

        return true;
    }
}

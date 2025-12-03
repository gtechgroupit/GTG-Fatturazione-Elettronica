<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Parser per file XML FatturaPA
 *
 * Legge e interpreta i file XML delle fatture elettroniche ricevute
 *
 * @author GTech Group IT
 * @version 1.0.0
 */
class FatturaPA_Parser
{
    /**
     * @var DOMDocument Documento XML
     */
    protected $xml;

    /**
     * @var DOMXPath XPath per le query
     */
    protected $xpath;

    /**
     * @var array Dati estratti
     */
    protected $data;

    /**
     * @var array Errori di parsing
     */
    protected $errors = [];

    /**
     * Namespace FatturaPA
     */
    const NAMESPACE_FPA = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2';

    /**
     * Costruttore
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * Parsa un file XML FatturaPA
     *
     * @param string $xml_content Contenuto XML
     * @return array|false Dati estratti o false in caso di errore
     */
    public function parse($xml_content)
    {
        $this->errors = [];
        $this->data = [];

        libxml_use_internal_errors(true);

        $this->xml = new DOMDocument();
        $loaded = $this->xml->loadXML($xml_content);

        if (!$loaded) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                $this->errors[] = "Riga {$error->line}: {$error->message}";
            }
            libxml_clear_errors();
            return false;
        }

        $this->xpath = new DOMXPath($this->xml);
        $this->xpath->registerNamespace('p', self::NAMESPACE_FPA);

        // Estrai i dati
        $this->parseHeader();
        $this->parseBody();

        return $this->data;
    }

    /**
     * Parsa l'header della fattura
     */
    protected function parseHeader()
    {
        // DatiTrasmissione
        $this->data['trasmissione'] = [
            'progressivo_invio'    => $this->getValue('//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio'),
            'formato_trasmissione' => $this->getValue('//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione'),
            'codice_destinatario'  => $this->getValue('//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario'),
        ];

        // CedentePrestatore (fornitore)
        $this->data['fornitore'] = [
            'partita_iva'     => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice'),
            'paese_iva'       => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdPaese'),
            'codice_fiscale'  => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/CodiceFiscale'),
            'denominazione'   => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Denominazione'),
            'nome'            => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Nome'),
            'cognome'         => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Cognome'),
            'regime_fiscale'  => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RegimeFiscale'),
            'indirizzo'       => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/Sede/Indirizzo'),
            'numero_civico'   => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/Sede/NumeroCivico'),
            'cap'             => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/Sede/CAP'),
            'comune'          => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/Sede/Comune'),
            'provincia'       => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/Sede/Provincia'),
            'nazione'         => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/Sede/Nazione'),
            'telefono'        => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/Contatti/Telefono'),
            'email'           => $this->getValue('//FatturaElettronicaHeader/CedentePrestatore/Contatti/Email'),
        ];

        // Se non c'è denominazione, usa nome + cognome
        if (empty($this->data['fornitore']['denominazione'])) {
            $nome = trim($this->data['fornitore']['nome'] . ' ' . $this->data['fornitore']['cognome']);
            if (!empty($nome)) {
                $this->data['fornitore']['denominazione'] = $nome;
            }
        }

        // CessionarioCommittente (cliente/noi)
        $this->data['cliente'] = [
            'partita_iva'     => $this->getValue('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdCodice'),
            'paese_iva'       => $this->getValue('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdPaese'),
            'codice_fiscale'  => $this->getValue('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/CodiceFiscale'),
            'denominazione'   => $this->getValue('//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/Anagrafica/Denominazione'),
        ];
    }

    /**
     * Parsa il body della fattura
     */
    protected function parseBody()
    {
        // DatiGeneraliDocumento
        $this->data['documento'] = [
            'tipo_documento'        => $this->getValue('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/TipoDocumento'),
            'divisa'                => $this->getValue('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Divisa'),
            'data'                  => $this->getValue('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Data'),
            'numero'                => $this->getValue('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Numero'),
            'importo_totale'        => $this->getValue('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/ImportoTotaleDocumento'),
            'arrotondamento'        => $this->getValue('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Arrotondamento'),
        ];

        // Causale (può essere multipla)
        $causali = $this->getValues('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Causale');
        $this->data['documento']['causale'] = implode(' ', $causali);

        // Bollo
        $bollo_virtuale = $this->getValue('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/DatiBollo/BolloVirtuale');
        if ($bollo_virtuale === 'SI') {
            $this->data['documento']['bollo'] = [
                'virtuale' => true,
                'importo'  => $this->getValue('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/DatiBollo/ImportoBollo'),
            ];
        }

        // DatiOrdineAcquisto
        $this->data['ordine_acquisto'] = $this->getValue('//FatturaElettronicaBody/DatiGenerali/DatiOrdineAcquisto/IdDocumento');

        // DettaglioLinee
        $this->data['linee'] = [];
        $linee = $this->xpath->query('//FatturaElettronicaBody/DatiBeniServizi/DettaglioLinee');

        foreach ($linee as $linea) {
            $item = [
                'numero_linea'     => $this->getNodeValue($linea, 'NumeroLinea'),
                'codice_articolo'  => $this->getNodeValue($linea, 'CodiceArticolo/CodiceValore'),
                'descrizione'      => $this->getNodeValue($linea, 'Descrizione'),
                'quantita'         => (float)$this->getNodeValue($linea, 'Quantita'),
                'unita_misura'     => $this->getNodeValue($linea, 'UnitaMisura'),
                'prezzo_unitario'  => (float)$this->getNodeValue($linea, 'PrezzoUnitario'),
                'prezzo_totale'    => (float)$this->getNodeValue($linea, 'PrezzoTotale'),
                'aliquota_iva'     => (float)$this->getNodeValue($linea, 'AliquotaIVA'),
                'natura'           => $this->getNodeValue($linea, 'Natura'),
            ];

            // Gestione sconto
            $sconto_tipo = $this->getNodeValue($linea, 'ScontoMaggiorazione/Tipo');
            if ($sconto_tipo === 'SC') {
                $item['sconto_percentuale'] = (float)$this->getNodeValue($linea, 'ScontoMaggiorazione/Percentuale');
                $item['sconto_importo'] = (float)$this->getNodeValue($linea, 'ScontoMaggiorazione/Importo');
            }

            $this->data['linee'][] = $item;
        }

        // DatiRiepilogo
        $this->data['riepilogo'] = [];
        $riepilogo_nodes = $this->xpath->query('//FatturaElettronicaBody/DatiBeniServizi/DatiRiepilogo');

        foreach ($riepilogo_nodes as $riepilogo) {
            $this->data['riepilogo'][] = [
                'aliquota_iva'        => (float)$this->getNodeValue($riepilogo, 'AliquotaIVA'),
                'natura'              => $this->getNodeValue($riepilogo, 'Natura'),
                'spese_accessorie'    => (float)$this->getNodeValue($riepilogo, 'SpeseAccessorie'),
                'arrotondamento'      => (float)$this->getNodeValue($riepilogo, 'Arrotondamento'),
                'imponibile_importo'  => (float)$this->getNodeValue($riepilogo, 'ImponibileImporto'),
                'imposta'             => (float)$this->getNodeValue($riepilogo, 'Imposta'),
                'esigibilita_iva'     => $this->getNodeValue($riepilogo, 'EsigibilitaIVA'),
                'riferimento_normativo' => $this->getNodeValue($riepilogo, 'RiferimentoNormativo'),
            ];
        }

        // Calcola totali
        $this->data['totali'] = [
            'imponibile' => 0,
            'iva'        => 0,
            'totale'     => 0,
        ];

        foreach ($this->data['riepilogo'] as $r) {
            $this->data['totali']['imponibile'] += $r['imponibile_importo'];
            $this->data['totali']['iva'] += $r['imposta'];
        }

        // Se c'è ImportoTotaleDocumento usa quello, altrimenti calcola
        if (!empty($this->data['documento']['importo_totale'])) {
            $this->data['totali']['totale'] = (float)$this->data['documento']['importo_totale'];
        } else {
            $this->data['totali']['totale'] = $this->data['totali']['imponibile'] + $this->data['totali']['iva'];
        }

        // DatiPagamento
        $this->data['pagamento'] = [];
        $pagamenti = $this->xpath->query('//FatturaElettronicaBody/DatiPagamento');

        foreach ($pagamenti as $pagamento) {
            $condizioni = $this->getNodeValue($pagamento, 'CondizioniPagamento');

            $dettagli = $this->xpath->query('DettaglioPagamento', $pagamento);
            foreach ($dettagli as $dettaglio) {
                $this->data['pagamento'][] = [
                    'condizioni'         => $condizioni,
                    'modalita'           => $this->getNodeValue($dettaglio, 'ModalitaPagamento'),
                    'data_scadenza'      => $this->getNodeValue($dettaglio, 'DataScadenzaPagamento'),
                    'giorni_termine'     => $this->getNodeValue($dettaglio, 'GiorniTerminiPagamento'),
                    'data_decorrenza'    => $this->getNodeValue($dettaglio, 'DataDecorrenzaPenale'),
                    'importo'            => (float)$this->getNodeValue($dettaglio, 'ImportoPagamento'),
                    'iban'               => $this->getNodeValue($dettaglio, 'IBAN'),
                    'istituto_finanziario' => $this->getNodeValue($dettaglio, 'IstitutoFinanziario'),
                ];
            }
        }

        // Allegati
        $this->data['allegati'] = [];
        $allegati = $this->xpath->query('//FatturaElettronicaBody/Allegati');

        foreach ($allegati as $allegato) {
            $this->data['allegati'][] = [
                'nome_attachment'     => $this->getNodeValue($allegato, 'NomeAttachment'),
                'algoritmo_compressione' => $this->getNodeValue($allegato, 'AlgoritmoCompressione'),
                'formato_attachment'  => $this->getNodeValue($allegato, 'FormatoAttachment'),
                'descrizione'         => $this->getNodeValue($allegato, 'DescrizioneAttachment'),
                'attachment'          => $this->getNodeValue($allegato, 'Attachment'),
            ];
        }
    }

    /**
     * Ottiene un valore da XPath
     *
     * @param string $query Query XPath
     * @return string|null
     */
    protected function getValue($query)
    {
        // Prova prima con namespace
        $nodes = $this->xpath->query(str_replace('//', '//p:', $query));

        if ($nodes->length == 0) {
            // Prova senza namespace
            $nodes = $this->xpath->query($query);
        }

        if ($nodes->length > 0) {
            return trim($nodes->item(0)->nodeValue);
        }

        return null;
    }

    /**
     * Ottiene tutti i valori da XPath
     *
     * @param string $query Query XPath
     * @return array
     */
    protected function getValues($query)
    {
        $values = [];

        $nodes = $this->xpath->query(str_replace('//', '//p:', $query));

        if ($nodes->length == 0) {
            $nodes = $this->xpath->query($query);
        }

        foreach ($nodes as $node) {
            $values[] = trim($node->nodeValue);
        }

        return $values;
    }

    /**
     * Ottiene il valore di un nodo figlio
     *
     * @param DOMNode $parent Nodo padre
     * @param string $name Nome del nodo figlio
     * @return string|null
     */
    protected function getNodeValue($parent, $name)
    {
        // Supporta path con /
        $parts = explode('/', $name);
        $current = $parent;

        foreach ($parts as $part) {
            $found = false;
            foreach ($current->childNodes as $child) {
                if ($child->nodeName === $part || $child->localName === $part) {
                    $current = $child;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return null;
            }
        }

        return trim($current->nodeValue);
    }

    /**
     * Ottiene gli errori di parsing
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Ottiene i dati estratti
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Ottiene un riepilogo della fattura
     *
     * @return array
     */
    public function getSummary()
    {
        return [
            'fornitore_denominazione' => $this->data['fornitore']['denominazione'] ?? '',
            'fornitore_partita_iva'   => $this->data['fornitore']['partita_iva'] ?? '',
            'fornitore_codice_fiscale' => $this->data['fornitore']['codice_fiscale'] ?? '',
            'tipo_documento'          => $this->data['documento']['tipo_documento'] ?? '',
            'numero_documento'        => $this->data['documento']['numero'] ?? '',
            'data_documento'          => $this->data['documento']['data'] ?? '',
            'imponibile'              => $this->data['totali']['imponibile'] ?? 0,
            'iva'                     => $this->data['totali']['iva'] ?? 0,
            'totale'                  => $this->data['totali']['totale'] ?? 0,
            'divisa'                  => $this->data['documento']['divisa'] ?? 'EUR',
        ];
    }

    /**
     * Verifica se la fattura è una nota di credito
     *
     * @return bool
     */
    public function isNotaCredito()
    {
        $tipo = $this->data['documento']['tipo_documento'] ?? '';
        return $tipo === 'TD04';
    }

    /**
     * Verifica se la fattura è una nota di debito
     *
     * @return bool
     */
    public function isNotaDebito()
    {
        $tipo = $this->data['documento']['tipo_documento'] ?? '';
        return $tipo === 'TD05';
    }

    /**
     * Ottiene la descrizione del tipo documento
     *
     * @return string
     */
    public function getTipoDocumentoLabel()
    {
        return fe_get_tipo_documento_label($this->data['documento']['tipo_documento'] ?? '');
    }
}

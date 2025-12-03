<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model per la gestione delle fatture elettroniche
 *
 * @author GTech Group IT
 * @version 1.0.0
 */
class Fatturazione_elettronica_model extends App_Model
{
    /**
     * Costruttore
     */
    public function __construct()
    {
        parent::__construct();
    }

    // =========================================================================
    // FATTURE ATTIVE (VENDITA)
    // =========================================================================

    /**
     * Ottiene una fattura attiva per ID
     *
     * @param int $id ID della fattura elettronica
     * @return object|null
     */
    public function get_fattura_attiva($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'fe_fatture_attive')->row();
    }

    /**
     * Ottiene una fattura attiva per ID fattura Perfex
     *
     * @param int $invoice_id ID della fattura Perfex
     * @return object|null
     */
    public function get_fattura_attiva_by_invoice_id($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        $this->db->where('credit_note_id IS NULL', null, false);
        return $this->db->get(db_prefix() . 'fe_fatture_attive')->row();
    }

    /**
     * Ottiene tutte le fatture attive
     *
     * @param array $filters Filtri opzionali
     * @return array
     */
    public function get_fatture_attive($filters = [])
    {
        if (!empty($filters['stato'])) {
            $this->db->where('stato', $filters['stato']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('created_at >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('created_at <=', $filters['to_date']);
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get(db_prefix() . 'fe_fatture_attive')->result();
    }

    /**
     * Crea un record per una fattura attiva
     *
     * @param int $invoice_id ID fattura Perfex
     * @param string $tipo_documento Tipo documento (TD01, ecc.)
     * @return int|false ID del record creato o false
     */
    public function create_fattura_attiva($invoice_id, $tipo_documento = 'TD01')
    {
        // Verifica che non esista già
        $existing = $this->get_fattura_attiva_by_invoice_id($invoice_id);
        if ($existing) {
            return $existing->id;
        }

        // Genera l'XML
        $this->load->library('fatturazione_elettronica/FatturaPA_Generator');
        $progressivo = fe_genera_progressivo();
        $xml = $this->fatturapa_generator->generateFromInvoice($invoice_id, $tipo_documento, $progressivo);

        if ($xml === false) {
            fe_log('errore_generazione', implode(', ', $this->fatturapa_generator->getErrors()), null, null);
            return false;
        }

        // Genera il nome file
        $partita_iva = get_option('fe_partita_iva');
        $nome_file = fe_genera_nome_file($partita_iva, $progressivo);

        // Salva il record
        $data = [
            'invoice_id'        => $invoice_id,
            'tipo_documento'    => $tipo_documento,
            'progressivo_invio' => $progressivo,
            'nome_file'         => $nome_file,
            'xml_content'       => $xml,
            'stato'             => FE_STATO_GENERATA,
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'fe_fatture_attive', $data);
        $id = $this->db->insert_id();

        // Salva il file XML
        $path = fe_get_upload_path('attive');
        file_put_contents($path . $nome_file, $xml);

        fe_log('fattura_creata', "Fattura elettronica creata: {$nome_file}", $id);

        return $id;
    }

    /**
     * Aggiorna una fattura attiva da una fattura Perfex
     *
     * @param int $invoice_id ID fattura Perfex
     * @return bool
     */
    public function update_fattura_attiva_from_invoice($invoice_id)
    {
        $fe_fattura = $this->get_fattura_attiva_by_invoice_id($invoice_id);

        if (!$fe_fattura || $fe_fattura->stato != FE_STATO_BOZZA && $fe_fattura->stato != FE_STATO_GENERATA) {
            return false;
        }

        // Rigenera l'XML
        $this->load->library('fatturazione_elettronica/FatturaPA_Generator');
        $xml = $this->fatturapa_generator->generateFromInvoice(
            $invoice_id,
            $fe_fattura->tipo_documento,
            $fe_fattura->progressivo_invio
        );

        if ($xml === false) {
            return false;
        }

        // Aggiorna il record
        $this->db->where('id', $fe_fattura->id);
        $this->db->update(db_prefix() . 'fe_fatture_attive', [
            'xml_content' => $xml,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        // Aggiorna il file
        $path = fe_get_upload_path('attive');
        file_put_contents($path . $fe_fattura->nome_file, $xml);

        fe_log('fattura_aggiornata', "Fattura elettronica aggiornata: {$fe_fattura->nome_file}", $fe_fattura->id);

        return true;
    }

    /**
     * Invia una fattura attiva allo SDI
     *
     * @param int $id ID della fattura elettronica
     * @return bool
     */
    public function send_fattura_attiva($id)
    {
        $fattura = $this->get_fattura_attiva($id);

        if (!$fattura) {
            return false;
        }

        if (!in_array($fattura->stato, [FE_STATO_GENERATA, FE_STATO_SCARTATA])) {
            return false;
        }

        $this->load->library('fatturazione_elettronica/Sdi_client');

        $result = $this->sdi_client->sendInvoice(
            $fattura->xml_content,
            $fattura->nome_file,
            !empty($fattura->xml_firmato)
        );

        if ($result === false) {
            // Aggiorna con l'errore
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'fe_fatture_attive', [
                'tentativi_invio' => $fattura->tentativi_invio + 1,
                'ultimo_errore'   => $this->sdi_client->getLastError(),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            fe_log('errore_invio', $this->sdi_client->getLastError(), $id);
            return false;
        }

        // Aggiorna con successo
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'fe_fatture_attive', [
            'stato'             => FE_STATO_INVIATA,
            'identificativo_sdi' => $result['identificativo_sdi'],
            'data_invio'        => date('Y-m-d H:i:s'),
            'tentativi_invio'   => $fattura->tentativi_invio + 1,
            'ultimo_errore'     => null,
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        fe_log('fattura_inviata', "Fattura inviata, ID SDI: {$result['identificativo_sdi']}", $id);

        // Invia notifica email
        $this->send_notification($id, 'inviata');

        return true;
    }

    /**
     * Controlla lo stato delle fatture inviate
     */
    public function check_fatture_status()
    {
        // Ottieni le fatture in attesa di esito
        $this->db->where_in('stato', [FE_STATO_INVIATA, FE_STATO_NON_CONSEGNATA]);
        $this->db->where('identificativo_sdi IS NOT NULL', null, false);
        $fatture = $this->db->get(db_prefix() . 'fe_fatture_attive')->result();

        $this->load->library('fatturazione_elettronica/Sdi_client');

        foreach ($fatture as $fattura) {
            $status = $this->sdi_client->checkInvoiceStatus($fattura->identificativo_sdi);

            if ($status === false) {
                continue;
            }

            if ($status['stato'] != $fattura->stato) {
                // Aggiorna lo stato
                $this->db->where('id', $fattura->id);
                $this->db->update(db_prefix() . 'fe_fatture_attive', [
                    'stato'               => $status['stato'],
                    'esito_sdi'           => $status['stato'],
                    'descrizione_esito'   => $status['message'] ?? '',
                    'data_ricezione_esito' => date('Y-m-d H:i:s'),
                    'data_ultimo_check'   => date('Y-m-d H:i:s'),
                    'updated_at'          => date('Y-m-d H:i:s'),
                ]);

                fe_log('stato_aggiornato', "Stato aggiornato a: {$status['stato']}", $fattura->id);

                // Invia notifica
                $this->send_notification($fattura->id, $status['stato']);
            } else {
                // Aggiorna solo la data di ultimo check
                $this->db->where('id', $fattura->id);
                $this->db->update(db_prefix() . 'fe_fatture_attive', [
                    'data_ultimo_check' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    /**
     * Elimina una fattura attiva (solo se in bozza)
     *
     * @param int $id ID della fattura
     * @return bool
     */
    public function delete_fattura_attiva($id)
    {
        $fattura = $this->get_fattura_attiva($id);

        if (!$fattura || !in_array($fattura->stato, [FE_STATO_BOZZA, FE_STATO_GENERATA])) {
            return false;
        }

        // Rimuovi il file
        $path = fe_get_upload_path('attive');
        if (file_exists($path . $fattura->nome_file)) {
            unlink($path . $fattura->nome_file);
        }

        // Rimuovi il record
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'fe_fatture_attive');

        fe_log('fattura_eliminata', "Fattura eliminata: {$fattura->nome_file}");

        return true;
    }

    // =========================================================================
    // NOTE DI CREDITO
    // =========================================================================

    /**
     * Ottiene una nota di credito elettronica
     *
     * @param int $credit_note_id ID nota di credito Perfex
     * @return object|null
     */
    public function get_nota_credito_by_credit_note_id($credit_note_id)
    {
        $this->db->where('credit_note_id', $credit_note_id);
        return $this->db->get(db_prefix() . 'fe_fatture_attive')->row();
    }

    /**
     * Crea una nota di credito elettronica
     *
     * @param int $credit_note_id ID nota di credito Perfex
     * @return int|false
     */
    public function create_nota_credito($credit_note_id)
    {
        $existing = $this->get_nota_credito_by_credit_note_id($credit_note_id);
        if ($existing) {
            return $existing->id;
        }

        $this->load->library('fatturazione_elettronica/FatturaPA_Generator');
        $progressivo = fe_genera_progressivo();
        $xml = $this->fatturapa_generator->generateFromCreditNote($credit_note_id, $progressivo);

        if ($xml === false) {
            return false;
        }

        $partita_iva = get_option('fe_partita_iva');
        $nome_file = fe_genera_nome_file($partita_iva, $progressivo);

        $data = [
            'credit_note_id'    => $credit_note_id,
            'tipo_documento'    => 'TD04',
            'progressivo_invio' => $progressivo,
            'nome_file'         => $nome_file,
            'xml_content'       => $xml,
            'stato'             => FE_STATO_GENERATA,
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'fe_fatture_attive', $data);
        $id = $this->db->insert_id();

        $path = fe_get_upload_path('attive');
        file_put_contents($path . $nome_file, $xml);

        fe_log('nota_credito_creata', "Nota di credito creata: {$nome_file}", $id);

        return $id;
    }

    /**
     * Aggiorna una nota di credito da Perfex
     *
     * @param int $credit_note_id ID nota di credito
     * @return bool
     */
    public function update_nota_credito_from_credit_note($credit_note_id)
    {
        $fe_nota = $this->get_nota_credito_by_credit_note_id($credit_note_id);

        if (!$fe_nota || !in_array($fe_nota->stato, [FE_STATO_BOZZA, FE_STATO_GENERATA])) {
            return false;
        }

        $this->load->library('fatturazione_elettronica/FatturaPA_Generator');
        $xml = $this->fatturapa_generator->generateFromCreditNote($credit_note_id, $fe_nota->progressivo_invio);

        if ($xml === false) {
            return false;
        }

        $this->db->where('id', $fe_nota->id);
        $this->db->update(db_prefix() . 'fe_fatture_attive', [
            'xml_content' => $xml,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $path = fe_get_upload_path('attive');
        file_put_contents($path . $fe_nota->nome_file, $xml);

        return true;
    }

    // =========================================================================
    // FATTURE PASSIVE (ACQUISTO)
    // =========================================================================

    /**
     * Ottiene una fattura passiva
     *
     * @param int $id ID
     * @return object|null
     */
    public function get_fattura_passiva($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'fe_fatture_passive')->row();
    }

    /**
     * Ottiene una fattura passiva per identificativo SDI
     *
     * @param string $identificativo_sdi Identificativo SDI
     * @return object|null
     */
    public function get_fattura_passiva_by_sdi($identificativo_sdi)
    {
        $this->db->where('identificativo_sdi', $identificativo_sdi);
        return $this->db->get(db_prefix() . 'fe_fatture_passive')->row();
    }

    /**
     * Ottiene tutte le fatture passive
     *
     * @param array $filters Filtri
     * @return array
     */
    public function get_fatture_passive($filters = [])
    {
        if (!empty($filters['stato'])) {
            $this->db->where('stato', $filters['stato']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('data_documento >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('data_documento <=', $filters['to_date']);
        }

        if (isset($filters['letto'])) {
            $this->db->where('letto', $filters['letto']);
        }

        if (isset($filters['archiviato'])) {
            $this->db->where('archiviato', $filters['archiviato']);
        }

        $this->db->order_by('data_ricezione', 'DESC');
        return $this->db->get(db_prefix() . 'fe_fatture_passive')->result();
    }

    /**
     * Scarica le fatture passive dal provider
     *
     * @param string|null $from_date Data di inizio
     * @return int Numero di fatture scaricate
     */
    public function download_fatture_passive($from_date = null)
    {
        $this->load->library('fatturazione_elettronica/Sdi_client');
        $this->load->library('fatturazione_elettronica/FatturaPA_Parser');

        $invoices = $this->sdi_client->downloadPassiveInvoices($from_date);

        if ($invoices === false || empty($invoices)) {
            return 0;
        }

        $count = 0;

        foreach ($invoices as $inv) {
            // Verifica se esiste già
            $existing = $this->get_fattura_passiva_by_sdi($inv['identificativo_sdi']);
            if ($existing) {
                continue;
            }

            // Parsa l'XML
            $parsed = $this->fatturapa_parser->parse($inv['xml_content']);

            if ($parsed === false) {
                fe_log('errore_parsing', implode(', ', $this->fatturapa_parser->getErrors()));
                continue;
            }

            $summary = $this->fatturapa_parser->getSummary();

            // Salva nel database
            $data = [
                'identificativo_sdi'      => $inv['identificativo_sdi'],
                'nome_file'               => $inv['nome_file'],
                'fornitore_denominazione' => $summary['fornitore_denominazione'],
                'fornitore_partita_iva'   => $summary['fornitore_partita_iva'],
                'fornitore_codice_fiscale' => $summary['fornitore_codice_fiscale'],
                'tipo_documento'          => $summary['tipo_documento'],
                'numero_documento'        => $summary['numero_documento'],
                'data_documento'          => $summary['data_documento'],
                'imponibile'              => $summary['imponibile'],
                'iva'                     => $summary['iva'],
                'totale'                  => $summary['totale'],
                'xml_content'             => $inv['xml_content'],
                'xml_originale'           => $inv['xml_content'],
                'stato'                   => 'ricevuta',
                'data_ricezione'          => $inv['data_ricezione'],
                'letto'                   => 0,
                'archiviato'              => 0,
                'created_at'              => date('Y-m-d H:i:s'),
            ];

            // Cerca fornitore esistente
            $this->db->where('vat', $summary['fornitore_partita_iva']);
            $vendor = $this->db->get(db_prefix() . 'vendors')->row();
            if ($vendor) {
                $data['vendor_id'] = $vendor->vendorid;
            }

            $this->db->insert(db_prefix() . 'fe_fatture_passive', $data);
            $id = $this->db->insert_id();

            // Salva il file
            $path = fe_get_upload_path('passive');
            file_put_contents($path . $inv['nome_file'], $inv['xml_content']);

            fe_log('fattura_passiva_ricevuta', "Ricevuta fattura: {$inv['nome_file']}", null, $id);

            $count++;
        }

        return $count;
    }

    /**
     * Segna una fattura passiva come letta
     *
     * @param int $id ID
     * @return bool
     */
    public function mark_as_read($id)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'fe_fatture_passive', [
            'letto'      => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Archivia una fattura passiva
     *
     * @param int $id ID
     * @return bool
     */
    public function archive_fattura_passiva($id)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'fe_fatture_passive', [
            'archiviato' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Collega una fattura passiva a una spesa Perfex
     *
     * @param int $fattura_id ID fattura passiva
     * @param int $expense_id ID spesa Perfex
     * @return bool
     */
    public function link_to_expense($fattura_id, $expense_id)
    {
        $this->db->where('id', $fattura_id);
        return $this->db->update(db_prefix() . 'fe_fatture_passive', [
            'expense_id' => $expense_id,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Crea una spesa da una fattura passiva
     *
     * @param int $fattura_id ID fattura passiva
     * @return int|false ID della spesa creata o false
     */
    public function create_expense_from_fattura($fattura_id)
    {
        $fattura = $this->get_fattura_passiva($fattura_id);

        if (!$fattura) {
            return false;
        }

        $this->load->model('expenses_model');

        // Cerca o crea la categoria spese "Fatture Elettroniche"
        $this->db->where('name', 'Fatture Elettroniche');
        $category = $this->db->get(db_prefix() . 'expenses_categories')->row();

        if (!$category) {
            $this->db->insert(db_prefix() . 'expenses_categories', [
                'name'        => 'Fatture Elettroniche',
                'description' => 'Spese da fatture elettroniche passive',
            ]);
            $category_id = $this->db->insert_id();
        } else {
            $category_id = $category->id;
        }

        // Prepara i dati della spesa
        $expense_data = [
            'category'     => $category_id,
            'amount'       => $fattura->totale,
            'tax'          => $fattura->iva,
            'tax2'         => 0,
            'date'         => $fattura->data_documento,
            'note'         => "Fattura: {$fattura->numero_documento}\nFornitore: {$fattura->fornitore_denominazione}\nP.IVA: {$fattura->fornitore_partita_iva}",
            'paymentmode'  => '',
            'reference_no' => $fattura->numero_documento,
            'billable'     => 0,
        ];

        $expense_id = $this->expenses_model->add($expense_data);

        if ($expense_id) {
            $this->link_to_expense($fattura_id, $expense_id);
            fe_log('spesa_creata', "Spesa creata da fattura passiva: {$expense_id}", null, $fattura_id);
        }

        return $expense_id;
    }

    // =========================================================================
    // NOTIFICHE
    // =========================================================================

    /**
     * Salva una notifica SDI
     *
     * @param array $data Dati della notifica
     * @return int|false
     */
    public function save_notification($data)
    {
        $this->db->insert(db_prefix() . 'fe_notifiche', [
            'fattura_attiva_id'  => $data['fattura_attiva_id'] ?? null,
            'fattura_passiva_id' => $data['fattura_passiva_id'] ?? null,
            'identificativo_sdi' => $data['identificativo_sdi'],
            'tipo_notifica'      => $data['tipo_notifica'],
            'nome_file'          => $data['nome_file'],
            'xml_content'        => $data['xml_content'],
            'descrizione'        => $data['descrizione'] ?? null,
            'codice_errore'      => $data['codice_errore'] ?? null,
            'data_ricezione'     => $data['data_ricezione'] ?? date('Y-m-d H:i:s'),
            'elaborata'          => 0,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        return $this->db->insert_id();
    }

    /**
     * Invia una notifica email
     *
     * @param int $fattura_id ID fattura
     * @param string $tipo_notifica Tipo di notifica
     */
    protected function send_notification($fattura_id, $tipo_notifica)
    {
        if (get_option('fe_email_notifiche') != '1') {
            return;
        }

        $fattura = $this->get_fattura_attiva($fattura_id);
        if (!$fattura) {
            return;
        }

        $subject = _l('fe_notifica_email_subject_' . $tipo_notifica);
        if (empty($subject)) {
            $subject = _l('fe_notifica_email_subject', fe_get_stato_label($tipo_notifica));
        }

        $message = _l('fe_notifica_email_body_' . $tipo_notifica);
        if (empty($message)) {
            $message = _l('fe_notifica_email_body', [
                'nome_file' => $fattura->nome_file,
                'stato'     => fe_get_stato_label($tipo_notifica),
            ]);
        }

        $to = get_option('fe_email') ?: get_option('invoice_company_email');

        fe_send_email($to, $subject, $message);
    }

    // =========================================================================
    // STATISTICHE
    // =========================================================================

    /**
     * Ottiene le statistiche per la dashboard
     *
     * @return array
     */
    public function get_statistics()
    {
        $stats = [
            'attive' => [
                'totale'     => 0,
                'bozza'      => 0,
                'generate'   => 0,
                'inviate'    => 0,
                'consegnate' => 0,
                'errori'     => 0,
            ],
            'passive' => [
                'totale'      => 0,
                'non_lette'   => 0,
                'da_processare' => 0,
            ],
        ];

        // Statistiche fatture attive
        $this->db->select('stato, COUNT(*) as count');
        $this->db->group_by('stato');
        $result = $this->db->get(db_prefix() . 'fe_fatture_attive')->result();

        foreach ($result as $row) {
            $stats['attive']['totale'] += $row->count;

            switch ($row->stato) {
                case FE_STATO_BOZZA:
                    $stats['attive']['bozza'] = $row->count;
                    break;
                case FE_STATO_GENERATA:
                    $stats['attive']['generate'] = $row->count;
                    break;
                case FE_STATO_INVIATA:
                    $stats['attive']['inviate'] = $row->count;
                    break;
                case FE_STATO_CONSEGNATA:
                case FE_STATO_ACCETTATA:
                    $stats['attive']['consegnate'] += $row->count;
                    break;
                case FE_STATO_SCARTATA:
                case FE_STATO_RIFIUTATA:
                    $stats['attive']['errori'] += $row->count;
                    break;
            }
        }

        // Statistiche fatture passive
        $this->db->select('COUNT(*) as totale, SUM(CASE WHEN letto = 0 THEN 1 ELSE 0 END) as non_lette, SUM(CASE WHEN expense_id IS NULL AND archiviato = 0 THEN 1 ELSE 0 END) as da_processare');
        $result = $this->db->get(db_prefix() . 'fe_fatture_passive')->row();

        if ($result) {
            $stats['passive']['totale'] = (int)$result->totale;
            $stats['passive']['non_lette'] = (int)$result->non_lette;
            $stats['passive']['da_processare'] = (int)$result->da_processare;
        }

        return $stats;
    }

    /**
     * Ottiene i log delle operazioni
     *
     * @param int $limit Limite
     * @param int $offset Offset
     * @return array
     */
    public function get_logs($limit = 50, $offset = 0)
    {
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get(db_prefix() . 'fe_log')->result();
    }

    /**
     * Ottiene le statistiche mensili per i grafici
     *
     * @param int $anno Anno
     * @return array
     */
    public function get_monthly_stats($anno)
    {
        $stats = [
            'inviate'  => array_fill(0, 12, 0),
            'ricevute' => array_fill(0, 12, 0),
        ];

        // Fatture inviate (attive)
        $this->db->select('MONTH(data_invio) as mese, COUNT(*) as count');
        $this->db->where('YEAR(data_invio)', $anno);
        $this->db->where('data_invio IS NOT NULL');
        $this->db->group_by('MONTH(data_invio)');
        $result = $this->db->get(db_prefix() . 'fe_fatture_attive')->result();

        foreach ($result as $row) {
            $stats['inviate'][$row->mese - 1] = (int)$row->count;
        }

        // Fatture ricevute (passive)
        $this->db->select('MONTH(data_ricezione) as mese, COUNT(*) as count');
        $this->db->where('YEAR(data_ricezione)', $anno);
        $this->db->where('data_ricezione IS NOT NULL');
        $this->db->group_by('MONTH(data_ricezione)');
        $result = $this->db->get(db_prefix() . 'fe_fatture_passive')->result();

        foreach ($result as $row) {
            $stats['ricevute'][$row->mese - 1] = (int)$row->count;
        }

        return $stats;
    }

    /**
     * Aggiorna una fattura attiva
     *
     * @param int $id ID della fattura
     * @param array $data Dati da aggiornare
     * @return bool
     */
    public function update_fattura_attiva($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'fe_fatture_attive', $data);
    }

    /**
     * Metodo per il cron job: verifica stati fatture
     *
     * @return int Numero di fatture aggiornate
     */
    public function cron_verifica_stati()
    {
        $CI = &get_instance();
        $CI->load->library('fatturazione_elettronica/Sdi_client');

        $this->db->where('stato', FE_STATO_INVIATA);
        $this->db->where('identificativo_sdi IS NOT NULL');
        $fatture = $this->db->get(db_prefix() . 'fe_fatture_attive')->result();

        $updated = 0;

        foreach ($fatture as $fattura) {
            $status = $CI->sdi_client->checkInvoiceStatus($fattura->identificativo_sdi);

            if ($status && isset($status['stato']) && $status['stato'] != $fattura->stato) {
                $this->update_fattura_attiva($fattura->id, [
                    'stato'      => $status['stato'],
                    'esito_sdi'  => $status['message'] ?? null,
                    'data_esito' => date('Y-m-d H:i:s'),
                ]);

                // Log
                fe_log('cron', 'Stato aggiornato: ' . $status['stato'], $fattura->id);

                // Notifica email
                if (in_array($status['stato'], [FE_STATO_CONSEGNATA, FE_STATO_ACCETTATA, FE_STATO_RIFIUTATA, FE_STATO_SCARTATA])) {
                    $this->send_status_notification($fattura, $status['stato']);
                }

                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Metodo per il cron job: sincronizza fatture passive
     *
     * @return int Numero di fatture scaricate
     */
    public function cron_sync_passive()
    {
        $CI = &get_instance();
        $CI->load->library('fatturazione_elettronica/Sdi_client');

        // Scarica nuove fatture passive
        $fatture = $CI->sdi_client->downloadPassiveInvoices();

        if (!$fatture || !is_array($fatture)) {
            return 0;
        }

        $imported = 0;

        foreach ($fatture as $fattura_data) {
            // Verifica se già esiste
            $this->db->where('identificativo_sdi', $fattura_data['identificativo_sdi']);
            $existing = $this->db->get(db_prefix() . 'fe_fatture_passive')->row();

            if (!$existing) {
                $this->add_fattura_passiva($fattura_data);
                $imported++;
            }
        }

        if ($imported > 0) {
            fe_log('cron', "Sincronizzate {$imported} nuove fatture passive");
        }

        return $imported;
    }

    /**
     * Invia notifica email per cambio stato
     *
     * @param object $fattura Fattura
     * @param string $nuovo_stato Nuovo stato
     */
    protected function send_status_notification($fattura, $nuovo_stato)
    {
        if (get_option('fe_email_notifiche') != '1') {
            return;
        }

        $to = get_option('fe_email') ?: get_option('admin_email');

        $subject = sprintf(_l('fe_notifica_email_subject'), $fattura->nome_file);
        $message = sprintf(_l('fe_notifica_email_body'), $fattura->nome_file, fe_get_stato_label($nuovo_stato));

        fe_send_email($to, $subject, $message);
    }
}

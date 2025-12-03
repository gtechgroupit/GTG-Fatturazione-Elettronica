<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller principale per il modulo Fatturazione Elettronica
 *
 * @author GTech Group IT
 * @version 1.0.0
 */
class Fatturazione_elettronica extends AdminController
{
    /**
     * Costruttore
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('fatturazione_elettronica/Fatturazione_elettronica_model');
        $this->load->helper('fatturazione_elettronica/fatturazione_elettronica');

        // Verifica permessi
        if (!has_permission('fatturazione_elettronica', '', 'view') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }
    }

    /**
     * Dashboard principale
     */
    public function index()
    {
        $data['title'] = _l('fe_dashboard');
        $data['statistics'] = $this->Fatturazione_elettronica_model->get_statistics();
        $data['recent_logs'] = $this->Fatturazione_elettronica_model->get_logs(10);

        // Fatture recenti
        $data['fatture_attive_recenti'] = $this->Fatturazione_elettronica_model->get_fatture_attive(['limit' => 10]);
        $data['fatture_passive_recenti'] = $this->Fatturazione_elettronica_model->get_fatture_passive(['limit' => 10]);

        // Fatture da inviare (generate ma non inviate)
        $data['fatture_da_inviare'] = $this->Fatturazione_elettronica_model->get_fatture_attive(['stato' => FE_STATO_GENERATA]);

        // Provider info
        $this->load->library('fatturazione_elettronica/Sdi_client');
        $providers = Sdi_client::getAvailableProviders();
        $current_provider = get_option('fe_provider') ?: 'test';
        $data['provider_info'] = $providers[$current_provider] ?? $providers['test'];
        $data['provider_configured'] = $this->sdi_client->isProviderConfigured()['configured'];

        // Dati per il grafico andamento mensile
        $data['chart_labels'] = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
        $data['chart_data'] = $this->Fatturazione_elettronica_model->get_monthly_stats(date('Y'));

        // Fatture Perfex non ancora importate
        $data['invoices_to_import'] = $this->get_invoices_to_import();

        $this->load->view('fatturazione_elettronica/admin/dashboard', $data);
    }

    /**
     * Ottiene le fatture Perfex non ancora importate
     */
    protected function get_invoices_to_import()
    {
        // Ottieni gli ID fatture già importate
        $this->db->select('invoice_id');
        $this->db->from(db_prefix() . 'fe_fatture_attive');
        $this->db->where('invoice_id IS NOT NULL');
        $existing = $this->db->get()->result();

        $existing_ids = array_map(function ($f) {
            return $f->invoice_id;
        }, $existing);

        // Ottieni fatture non ancora importate
        $this->db->select('id, number, clientid, total, status, date');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where_in('status', [2, 3]); // Solo fatture inviate o pagate

        if (!empty($existing_ids)) {
            $this->db->where_not_in('id', $existing_ids);
        }

        $this->db->order_by('date', 'DESC');
        $this->db->limit(50);

        return $this->db->get()->result();
    }

    /**
     * Invia tutte le fatture generate
     */
    public function invia_tutte_generate()
    {
        if (!has_permission('fatturazione_elettronica', '', 'edit') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $fatture = $this->Fatturazione_elettronica_model->get_fatture_attive(['stato' => FE_STATO_GENERATA]);
        $sent = 0;
        $errors = 0;

        foreach ($fatture as $fattura) {
            $result = $this->Fatturazione_elettronica_model->invia_fattura($fattura->id);
            if ($result) {
                $sent++;
            } else {
                $errors++;
            }
        }

        set_alert('success', sprintf(_l('fe_bulk_send_result'), $sent, $errors));
        redirect(admin_url('fatturazione_elettronica'));
    }

    /**
     * Verifica stati delle fatture inviate
     */
    public function verifica_stati()
    {
        if (!has_permission('fatturazione_elettronica', '', 'edit') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $fatture = $this->Fatturazione_elettronica_model->get_fatture_attive(['stato' => FE_STATO_INVIATA]);
        $updated = 0;

        $this->load->library('fatturazione_elettronica/Sdi_client');

        foreach ($fatture as $fattura) {
            if (!empty($fattura->identificativo_sdi)) {
                $status = $this->sdi_client->checkInvoiceStatus($fattura->identificativo_sdi);

                if ($status && isset($status['stato'])) {
                    $this->Fatturazione_elettronica_model->update_fattura_attiva($fattura->id, [
                        'stato'       => $status['stato'],
                        'esito_sdi'   => $status['message'] ?? null,
                        'data_esito'  => date('Y-m-d H:i:s'),
                    ]);
                    $updated++;
                }
            }
        }

        set_alert('success', sprintf(_l('fe_stati_aggiornati'), $updated));
        redirect(admin_url('fatturazione_elettronica'));
    }

    // =========================================================================
    // FATTURE ATTIVE
    // =========================================================================

    /**
     * Lista fatture attive
     */
    public function fatture_attive()
    {
        $data['title'] = _l('fe_fatture_attive');

        $filters = [];
        if ($this->input->get('stato')) {
            $filters['stato'] = $this->input->get('stato');
        }
        if ($this->input->get('from_date')) {
            $filters['from_date'] = $this->input->get('from_date');
        }
        if ($this->input->get('to_date')) {
            $filters['to_date'] = $this->input->get('to_date');
        }

        $data['fatture'] = $this->Fatturazione_elettronica_model->get_fatture_attive($filters);
        $data['filters'] = $filters;

        $this->load->view('fatturazione_elettronica/admin/fatture_attive/index', $data);
    }

    /**
     * Dettaglio fattura attiva
     */
    public function fattura_attiva($id)
    {
        $fattura = $this->Fatturazione_elettronica_model->get_fattura_attiva($id);

        if (!$fattura) {
            set_alert('danger', _l('fe_fattura_not_found'));
            redirect(admin_url('fatturazione_elettronica/fatture_attive'));
        }

        $data['title'] = _l('fe_fattura_dettaglio') . ' - ' . $fattura->nome_file;
        $data['fattura'] = $fattura;

        // Carica la fattura Perfex collegata
        if ($fattura->invoice_id) {
            $this->load->model('invoices_model');
            $data['invoice'] = $this->invoices_model->get($fattura->invoice_id);
        }

        if ($fattura->credit_note_id) {
            $this->load->model('credit_notes_model');
            $data['credit_note'] = $this->credit_notes_model->get($fattura->credit_note_id);
        }

        // Log della fattura
        $this->db->where('fattura_attiva_id', $id);
        $this->db->order_by('created_at', 'DESC');
        $data['logs'] = $this->db->get(db_prefix() . 'fe_log')->result();

        // Notifiche della fattura
        $this->db->where('fattura_attiva_id', $id);
        $this->db->order_by('data_ricezione', 'DESC');
        $data['notifiche'] = $this->db->get(db_prefix() . 'fe_notifiche')->result();

        $this->load->view('fatturazione_elettronica/admin/fatture_attive/view', $data);
    }

    /**
     * Genera XML per una fattura
     */
    public function genera_xml($invoice_id = null)
    {
        if (!has_permission('fatturazione_elettronica', '', 'create') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        if ($invoice_id === null) {
            $invoice_id = $this->input->post('invoice_id');
        }

        if (!$invoice_id) {
            set_alert('danger', _l('fe_invoice_id_required'));
            redirect(admin_url('fatturazione_elettronica/fatture_attive'));
        }

        $tipo_documento = $this->input->post('tipo_documento') ?: 'TD01';

        $id = $this->Fatturazione_elettronica_model->create_fattura_attiva($invoice_id, $tipo_documento);

        if ($id === false) {
            set_alert('danger', _l('fe_error_generating_xml'));
        } else {
            set_alert('success', _l('fe_xml_generated'));
        }

        redirect(admin_url('fatturazione_elettronica/fattura_attiva/' . $id));
    }

    /**
     * Invia fattura allo SDI
     */
    public function invia_fattura($id)
    {
        if (!has_permission('fatturazione_elettronica', '', 'create') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $result = $this->Fatturazione_elettronica_model->send_fattura_attiva($id);

        if ($result === false) {
            set_alert('danger', _l('fe_error_sending'));
        } else {
            set_alert('success', _l('fe_invoice_sent'));
        }

        redirect(admin_url('fatturazione_elettronica/fattura_attiva/' . $id));
    }

    /**
     * Download XML
     */
    public function download_xml($id)
    {
        $fattura = $this->Fatturazione_elettronica_model->get_fattura_attiva($id);

        if (!$fattura) {
            set_alert('danger', _l('fe_fattura_not_found'));
            redirect(admin_url('fatturazione_elettronica/fatture_attive'));
        }

        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . $fattura->nome_file . '"');
        echo $fattura->xml_content;
        exit;
    }

    /**
     * Anteprima XML
     */
    public function preview_xml($id)
    {
        $fattura = $this->Fatturazione_elettronica_model->get_fattura_attiva($id);

        if (!$fattura) {
            echo '<p>Fattura non trovata</p>';
            exit;
        }

        header('Content-Type: text/xml');
        echo $fattura->xml_content;
        exit;
    }

    /**
     * Elimina fattura elettronica
     */
    public function delete_fattura_attiva($id)
    {
        if (!has_permission('fatturazione_elettronica', '', 'delete') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $result = $this->Fatturazione_elettronica_model->delete_fattura_attiva($id);

        if ($result === false) {
            set_alert('danger', _l('fe_cannot_delete'));
        } else {
            set_alert('success', _l('fe_deleted'));
        }

        redirect(admin_url('fatturazione_elettronica/fatture_attive'));
    }

    /**
     * Rigenera XML
     */
    public function rigenera_xml($id)
    {
        if (!has_permission('fatturazione_elettronica', '', 'edit') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $fattura = $this->Fatturazione_elettronica_model->get_fattura_attiva($id);

        if (!$fattura) {
            set_alert('danger', _l('fe_fattura_not_found'));
            redirect(admin_url('fatturazione_elettronica/fatture_attive'));
        }

        if ($fattura->invoice_id) {
            $result = $this->Fatturazione_elettronica_model->update_fattura_attiva_from_invoice($fattura->invoice_id);
        } elseif ($fattura->credit_note_id) {
            $result = $this->Fatturazione_elettronica_model->update_nota_credito_from_credit_note($fattura->credit_note_id);
        } else {
            $result = false;
        }

        if ($result === false) {
            set_alert('danger', _l('fe_error_regenerating'));
        } else {
            set_alert('success', _l('fe_xml_regenerated'));
        }

        redirect(admin_url('fatturazione_elettronica/fattura_attiva/' . $id));
    }

    /**
     * Invio multiplo
     */
    public function invia_multiple()
    {
        if (!has_permission('fatturazione_elettronica', '', 'create') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $ids = $this->input->post('ids');

        if (empty($ids)) {
            set_alert('danger', _l('fe_no_selection'));
            redirect(admin_url('fatturazione_elettronica/fatture_attive'));
        }

        $success = 0;
        $failed = 0;

        foreach ($ids as $id) {
            $result = $this->Fatturazione_elettronica_model->send_fattura_attiva($id);
            if ($result) {
                $success++;
            } else {
                $failed++;
            }
        }

        set_alert('success', _l('fe_bulk_send_result', ['success' => $success, 'failed' => $failed]));
        redirect(admin_url('fatturazione_elettronica/fatture_attive'));
    }

    // =========================================================================
    // FATTURE PASSIVE
    // =========================================================================

    /**
     * Lista fatture passive
     */
    public function fatture_passive()
    {
        $data['title'] = _l('fe_fatture_passive');

        $filters = [];
        if ($this->input->get('stato')) {
            $filters['stato'] = $this->input->get('stato');
        }
        if ($this->input->get('from_date')) {
            $filters['from_date'] = $this->input->get('from_date');
        }
        if ($this->input->get('to_date')) {
            $filters['to_date'] = $this->input->get('to_date');
        }
        if ($this->input->get('non_lette')) {
            $filters['letto'] = 0;
        }

        $data['fatture'] = $this->Fatturazione_elettronica_model->get_fatture_passive($filters);
        $data['filters'] = $filters;

        $this->load->view('fatturazione_elettronica/admin/fatture_passive/index', $data);
    }

    /**
     * Dettaglio fattura passiva
     */
    public function fattura_passiva($id)
    {
        $fattura = $this->Fatturazione_elettronica_model->get_fattura_passiva($id);

        if (!$fattura) {
            set_alert('danger', _l('fe_fattura_not_found'));
            redirect(admin_url('fatturazione_elettronica/fatture_passive'));
        }

        // Segna come letta
        $this->Fatturazione_elettronica_model->mark_as_read($id);

        $data['title'] = _l('fe_fattura_passiva_dettaglio') . ' - ' . $fattura->numero_documento;
        $data['fattura'] = $fattura;

        // Parsa l'XML per i dettagli
        $this->load->library('fatturazione_elettronica/FatturaPA_Parser');
        $parsed = $this->fatturapa_parser->parse($fattura->xml_content);
        $data['dettagli'] = $parsed;

        // Spesa collegata
        if ($fattura->expense_id) {
            $this->load->model('expenses_model');
            $data['expense'] = $this->expenses_model->get($fattura->expense_id);
        }

        // Fornitore
        if ($fattura->vendor_id) {
            $this->db->where('vendorid', $fattura->vendor_id);
            $data['vendor'] = $this->db->get(db_prefix() . 'vendors')->row();
        }

        $this->load->view('fatturazione_elettronica/admin/fatture_passive/view', $data);
    }

    /**
     * Download XML fattura passiva
     */
    public function download_xml_passiva($id)
    {
        $fattura = $this->Fatturazione_elettronica_model->get_fattura_passiva($id);

        if (!$fattura) {
            set_alert('danger', _l('fe_fattura_not_found'));
            redirect(admin_url('fatturazione_elettronica/fatture_passive'));
        }

        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . $fattura->nome_file . '"');
        echo $fattura->xml_content;
        exit;
    }

    /**
     * Crea spesa da fattura passiva
     */
    public function crea_spesa($id)
    {
        if (!has_permission('expenses', '', 'create') && !is_admin()) {
            access_denied('expenses');
        }

        $expense_id = $this->Fatturazione_elettronica_model->create_expense_from_fattura($id);

        if ($expense_id === false) {
            set_alert('danger', _l('fe_error_creating_expense'));
        } else {
            set_alert('success', _l('fe_expense_created'));
        }

        redirect(admin_url('fatturazione_elettronica/fattura_passiva/' . $id));
    }

    /**
     * Archivia fattura passiva
     */
    public function archivia_passiva($id)
    {
        $result = $this->Fatturazione_elettronica_model->archive_fattura_passiva($id);

        if ($result) {
            set_alert('success', _l('fe_archived'));
        }

        redirect(admin_url('fatturazione_elettronica/fatture_passive'));
    }

    /**
     * Sincronizza fatture passive
     */
    public function sync_passive()
    {
        if (!has_permission('fatturazione_elettronica', '', 'create') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $count = $this->Fatturazione_elettronica_model->download_fatture_passive();

        set_alert('success', _l('fe_sync_result', $count));
        redirect(admin_url('fatturazione_elettronica/fatture_passive'));
    }

    // =========================================================================
    // SETUP WIZARD
    // =========================================================================

    /**
     * Wizard di setup per la configurazione del provider
     */
    public function setup($step = 1)
    {
        if (!has_permission('fatturazione_elettronica', '', 'edit') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $data['title'] = _l('fe_setup_wizard');
        $data['step'] = (int)$step;
        $data['current_provider'] = get_option('fe_provider') ?: 'test';

        // Carica i provider disponibili
        $this->load->library('fatturazione_elettronica/Sdi_client');
        $data['providers'] = Sdi_client::getAvailableProviders();

        // Provider selezionato (da query string o configurazione)
        $selected_provider = $this->input->get('provider') ?: $data['current_provider'];
        $data['selected_provider'] = $selected_provider;
        $data['provider_info'] = $data['providers'][$selected_provider] ?? $data['providers']['test'];

        // Carica settings
        $data['settings'] = $this->get_all_settings();

        // Per step 3: regimi fiscali
        if ($step == 3) {
            $data['regimi_fiscali'] = $this->get_regimi_fiscali();
        }

        $this->load->view('fatturazione_elettronica/admin/setup/wizard', $data);
    }

    /**
     * Salva le credenziali OAuth per FattureInCloud
     */
    public function save_oauth_credentials()
    {
        if (!has_permission('fatturazione_elettronica', '', 'edit') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $client_id = $this->input->post('fe_fic_client_id');
        $client_secret = $this->input->post('fe_fic_client_secret');

        if (!empty($client_id) && !empty($client_secret)) {
            update_option('fe_fic_client_id', $client_id);
            update_option('fe_fic_client_secret', $client_secret);
            update_option('fe_provider', 'fattureincloud');

            // Genera URL di autorizzazione e reindirizza
            $this->load->library('fatturazione_elettronica/Fattureincloud_client');
            $auth_url = $this->fattureincloud_client->getAuthorizationUrl();

            if ($auth_url) {
                redirect($auth_url);
            }
        }

        set_alert('danger', _l('fe_oauth_error'));
        redirect(admin_url('fatturazione_elettronica/setup/2?provider=fattureincloud'));
    }

    /**
     * Callback OAuth per FattureInCloud
     */
    public function oauth_callback()
    {
        $code = $this->input->get('code');
        $state = $this->input->get('state');
        $error = $this->input->get('error');

        if ($error) {
            set_alert('danger', _l('fe_oauth_denied') . ': ' . $error);
            redirect(admin_url('fatturazione_elettronica/setup/2?provider=fattureincloud'));
        }

        if (empty($code)) {
            set_alert('danger', _l('fe_oauth_no_code'));
            redirect(admin_url('fatturazione_elettronica/setup/2?provider=fattureincloud'));
        }

        $this->load->library('fatturazione_elettronica/Fattureincloud_client');
        $result = $this->fattureincloud_client->handleCallback($code);

        if ($result) {
            set_alert('success', _l('fe_oauth_success'));
            redirect(admin_url('fatturazione_elettronica/setup/3'));
        } else {
            set_alert('danger', _l('fe_oauth_error') . ': ' . $this->fattureincloud_client->getLastError());
            redirect(admin_url('fatturazione_elettronica/setup/2?provider=fattureincloud'));
        }
    }

    /**
     * Disconnetti OAuth
     */
    public function oauth_disconnect()
    {
        if (!has_permission('fatturazione_elettronica', '', 'edit') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $provider = get_option('fe_provider');

        if ($provider == 'fattureincloud') {
            $this->load->library('fatturazione_elettronica/Fattureincloud_client');
            $this->fattureincloud_client->disconnect();
        }

        set_alert('success', _l('fe_oauth_disconnected'));
        redirect(admin_url('fatturazione_elettronica/setup/2?provider=' . $provider));
    }

    /**
     * Salva credenziali provider
     */
    public function save_credentials()
    {
        if (!has_permission('fatturazione_elettronica', '', 'edit') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $provider = $this->input->get('provider') ?: $this->input->post('provider');

        if (!empty($provider)) {
            update_option('fe_provider', $provider);
        }

        // Salva tutti i campi che iniziano con fe_
        $fields = $this->input->post();
        foreach ($fields as $key => $value) {
            if (strpos($key, 'fe_') === 0) {
                update_option($key, $value);
            }
        }

        // Gestione upload file (es. certificato per AdE)
        if (!empty($_FILES)) {
            foreach ($_FILES as $field_name => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $upload_path = fe_get_upload_path('certificates');

                    if (!is_dir($upload_path)) {
                        mkdir($upload_path, 0755, true);
                    }

                    $new_name = $field_name . '_' . time() . '_' . basename($file['name']);
                    $target = $upload_path . $new_name;

                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        update_option($field_name, $target);
                    }
                }
            }
        }

        set_alert('success', _l('fe_credentials_saved'));
        redirect(admin_url('fatturazione_elettronica/setup/3'));
    }

    /**
     * Salva dati azienda e completa setup
     */
    public function save_company()
    {
        if (!has_permission('fatturazione_elettronica', '', 'edit') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        $fields = $this->input->post();

        foreach ($fields as $key => $value) {
            if (strpos($key, 'fe_') === 0) {
                update_option($key, $value);
            }
        }

        // Segna il setup come completato
        update_option('fe_setup_completed', '1');

        set_alert('success', _l('fe_setup_complete'));
        redirect(admin_url('fatturazione_elettronica'));
    }

    /**
     * Ottiene tutte le impostazioni
     */
    protected function get_all_settings()
    {
        return [
            // Dati azienda
            'fe_denominazione'        => get_option('fe_denominazione'),
            'fe_partita_iva'          => get_option('fe_partita_iva'),
            'fe_codice_fiscale'       => get_option('fe_codice_fiscale'),
            'fe_regime_fiscale'       => get_option('fe_regime_fiscale'),
            'fe_indirizzo'            => get_option('fe_indirizzo'),
            'fe_cap'                  => get_option('fe_cap'),
            'fe_comune'               => get_option('fe_comune'),
            'fe_provincia'            => get_option('fe_provincia'),
            'fe_nazione'              => get_option('fe_nazione'),
            'fe_telefono'             => get_option('fe_telefono'),
            'fe_email'                => get_option('fe_email'),
            'fe_pec'                  => get_option('fe_pec'),
            'fe_codice_destinatario'  => get_option('fe_codice_destinatario'),
            'fe_rea_ufficio'          => get_option('fe_rea_ufficio'),
            'fe_rea_numero'           => get_option('fe_rea_numero'),
            'fe_capitale_sociale'     => get_option('fe_capitale_sociale'),
            'fe_socio_unico'          => get_option('fe_socio_unico'),
            'fe_stato_liquidazione'   => get_option('fe_stato_liquidazione'),

            // Provider SDI
            'fe_provider'             => get_option('fe_provider'),
            'fe_api_endpoint'         => get_option('fe_api_endpoint'),
            'fe_api_username'         => get_option('fe_api_username'),
            'fe_api_password'         => get_option('fe_api_password'),
            'fe_api_key'              => get_option('fe_api_key'),
            'fe_api_secret'           => get_option('fe_api_secret'),
            'fe_ambiente'             => get_option('fe_ambiente'),

            // Agenzia Entrate
            'fe_ade_certificato_path' => get_option('fe_ade_certificato_path'),
            'fe_ade_certificato_password' => get_option('fe_ade_certificato_password'),
            'fe_ade_codice_accreditamento' => get_option('fe_ade_codice_accreditamento'),

            // Fattura24
            'fe_f24_api_key'          => get_option('fe_f24_api_key'),

            // FattureInCloud
            'fe_fic_client_id'        => get_option('fe_fic_client_id'),
            'fe_fic_client_secret'    => get_option('fe_fic_client_secret'),
            'fe_fic_access_token'     => get_option('fe_fic_access_token'),
            'fe_fic_company_id'       => get_option('fe_fic_company_id'),
            'fe_fic_company_name'     => get_option('fe_fic_company_name'),

            // Opzioni
            'fe_auto_generate_xml'    => get_option('fe_auto_generate_xml'),
            'fe_auto_send'            => get_option('fe_auto_send'),
            'fe_bollo_virtuale_soglia' => get_option('fe_bollo_virtuale_soglia'),
            'fe_bollo_virtuale_importo' => get_option('fe_bollo_virtuale_importo'),
            'fe_email_notifiche'      => get_option('fe_email_notifiche'),
            'fe_default_modalita_pagamento' => get_option('fe_default_modalita_pagamento'),
            'fe_default_condizioni_pagamento' => get_option('fe_default_condizioni_pagamento'),
            'fe_webhook_enabled'      => get_option('fe_webhook_enabled'),
            'fe_webhook_secret'       => get_option('fe_webhook_secret'),
        ];
    }

    // =========================================================================
    // IMPOSTAZIONI
    // =========================================================================

    /**
     * Pagina impostazioni
     */
    public function impostazioni()
    {
        if (!has_permission('fatturazione_elettronica', '', 'edit') && !is_admin()) {
            access_denied('fatturazione_elettronica');
        }

        if ($this->input->post()) {
            $this->save_settings();
        }

        $data['title'] = _l('fe_impostazioni');

        // Carica tutte le opzioni
        $data['settings'] = $this->get_all_settings();

        // Carica provider info
        $this->load->library('fatturazione_elettronica/Sdi_client');
        $data['available_providers'] = Sdi_client::getAvailableProviders();
        $data['current_provider'] = get_option('fe_provider') ?: 'test';
        $data['provider_info'] = $data['available_providers'][$data['current_provider']] ?? $data['available_providers']['test'];

        // Liste per i select
        $data['regimi_fiscali'] = $this->get_regimi_fiscali();
        $data['modalita_pagamento'] = $this->get_modalita_pagamento();
        $data['condizioni_pagamento'] = $this->get_condizioni_pagamento();
        $data['providers'] = $this->get_providers_list();

        $this->load->view('fatturazione_elettronica/admin/impostazioni/index', $data);
    }

    /**
     * Salva le impostazioni
     */
    protected function save_settings()
    {
        $settings = $this->input->post();

        foreach ($settings as $key => $value) {
            if (strpos($key, 'fe_') === 0) {
                update_option($key, $value);
            }
        }

        set_alert('success', _l('settings_updated'));
        redirect(admin_url('fatturazione_elettronica/impostazioni'));
    }

    /**
     * Test connessione provider
     */
    public function test_connection()
    {
        $this->load->library('fatturazione_elettronica/Sdi_client');

        $result = $this->sdi_client->testConnection();

        $response = [
            'success' => $result,
            'message' => $result ? _l('fe_connection_ok') : _l('fe_connection_failed'),
        ];

        if (!$result) {
            $response['error'] = $this->sdi_client->getLastError();
        }

        echo json_encode($response);
    }

    // =========================================================================
    // WEBHOOK
    // =========================================================================

    /**
     * Endpoint webhook per ricezione notifiche SDI
     */
    public function webhook()
    {
        // Verifica che sia una richiesta POST
        if ($this->input->method() !== 'post') {
            show_404();
        }

        // Verifica che il webhook sia abilitato
        if (get_option('fe_webhook_enabled') != '1') {
            http_response_code(403);
            exit('Webhook disabled');
        }

        $this->load->library('fatturazione_elettronica/Sdi_client');

        $payload = file_get_contents('php://input');
        $signature = $this->input->get_request_header('X-Signature');

        $data = $this->sdi_client->handleWebhook($payload, $signature);

        if ($data === false) {
            http_response_code(400);
            echo json_encode(['error' => $this->sdi_client->getLastError()]);
            exit;
        }

        // Processa la notifica
        $this->process_webhook_notification($data);

        http_response_code(200);
        echo json_encode(['success' => true]);
    }

    /**
     * Processa una notifica webhook
     */
    protected function process_webhook_notification($data)
    {
        $identificativo_sdi = $data['identificativo_sdi'] ?? '';
        $tipo = $data['tipo'] ?? '';

        if (empty($identificativo_sdi)) {
            return;
        }

        // Cerca la fattura
        $this->db->where('identificativo_sdi', $identificativo_sdi);
        $fattura = $this->db->get(db_prefix() . 'fe_fatture_attive')->row();

        if (!$fattura) {
            // Potrebbe essere una fattura passiva nuova
            if ($tipo === 'fattura_ricevuta') {
                $this->Fatturazione_elettronica_model->download_fatture_passive();
            }
            return;
        }

        // Aggiorna lo stato
        $stato = $data['stato'] ?? FE_STATO_INVIATA;
        $this->db->where('id', $fattura->id);
        $this->db->update(db_prefix() . 'fe_fatture_attive', [
            'stato'               => $stato,
            'esito_sdi'           => $data['esito'] ?? '',
            'descrizione_esito'   => $data['descrizione'] ?? '',
            'data_ricezione_esito' => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        // Salva la notifica
        if (!empty($data['notifica_xml'])) {
            $this->Fatturazione_elettronica_model->save_notification([
                'fattura_attiva_id'  => $fattura->id,
                'identificativo_sdi' => $identificativo_sdi,
                'tipo_notifica'      => $data['tipo_notifica'] ?? 'GENERICO',
                'nome_file'          => $data['nome_file'] ?? '',
                'xml_content'        => $data['notifica_xml'],
                'descrizione'        => $data['descrizione'] ?? '',
            ]);
        }

        fe_log('webhook_ricevuto', "Ricevuta notifica: {$stato}", $fattura->id);
    }

    // =========================================================================
    // AJAX
    // =========================================================================

    /**
     * Ottiene le fatture Perfex non ancora importate
     */
    public function ajax_get_invoices_to_import()
    {
        $this->load->model('invoices_model');

        // Ottieni le fatture che non hanno ancora una fattura elettronica
        $this->db->select(db_prefix() . 'invoices.*');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->join(
            db_prefix() . 'fe_fatture_attive',
            db_prefix() . 'fe_fatture_attive.invoice_id = ' . db_prefix() . 'invoices.id',
            'left'
        );
        $this->db->where(db_prefix() . 'fe_fatture_attive.id IS NULL', null, false);
        $this->db->where(db_prefix() . 'invoices.status !=', 6); // Non cancellate
        $this->db->order_by(db_prefix() . 'invoices.date', 'DESC');
        $this->db->limit(100);

        $invoices = $this->db->get()->result();

        echo json_encode(['invoices' => $invoices]);
    }

    /**
     * Controlla lo stato di una fattura via AJAX
     */
    public function ajax_check_status($id)
    {
        $fattura = $this->Fatturazione_elettronica_model->get_fattura_attiva($id);

        if (!$fattura || empty($fattura->identificativo_sdi)) {
            echo json_encode(['error' => 'Fattura non trovata o non inviata']);
            return;
        }

        $this->load->library('fatturazione_elettronica/Sdi_client');
        $status = $this->sdi_client->checkInvoiceStatus($fattura->identificativo_sdi);

        echo json_encode([
            'stato'   => $status['stato'] ?? $fattura->stato,
            'message' => $status['message'] ?? '',
            'label'   => fe_get_stato_label($status['stato'] ?? $fattura->stato),
            'class'   => fe_get_stato_class($status['stato'] ?? $fattura->stato),
        ]);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Lista regimi fiscali
     */
    protected function get_regimi_fiscali()
    {
        return [
            'RF01' => 'RF01 - Ordinario',
            'RF02' => 'RF02 - Contribuenti minimi',
            'RF04' => 'RF04 - Agricoltura',
            'RF05' => 'RF05 - Vendita sali e tabacchi',
            'RF06' => 'RF06 - Commercio fiammiferi',
            'RF07' => 'RF07 - Editoria',
            'RF08' => 'RF08 - Telefonia pubblica',
            'RF09' => 'RF09 - Rivendita trasporti',
            'RF10' => 'RF10 - Intrattenimenti',
            'RF11' => 'RF11 - Agenzie viaggi',
            'RF12' => 'RF12 - Agro-industria',
            'RF13' => 'RF13 - Vendite a domicilio',
            'RF14' => 'RF14 - Beni usati',
            'RF15' => 'RF15 - Agenzie vendite asta',
            'RF16' => 'RF16 - IVA per cassa PA',
            'RF17' => 'RF17 - IVA per cassa',
            'RF18' => 'RF18 - Altro',
            'RF19' => 'RF19 - Forfettario',
        ];
    }

    /**
     * Lista modalità pagamento
     */
    protected function get_modalita_pagamento()
    {
        return [
            'MP01' => 'MP01 - Contanti',
            'MP02' => 'MP02 - Assegno',
            'MP03' => 'MP03 - Assegno circolare',
            'MP04' => 'MP04 - Contanti presso Tesoreria',
            'MP05' => 'MP05 - Bonifico',
            'MP06' => 'MP06 - Vaglia cambiario',
            'MP07' => 'MP07 - Bollettino bancario',
            'MP08' => 'MP08 - Carta di pagamento',
            'MP09' => 'MP09 - RID',
            'MP10' => 'MP10 - RID utenze',
            'MP11' => 'MP11 - RID veloce',
            'MP12' => 'MP12 - RIBA',
            'MP13' => 'MP13 - MAV',
            'MP14' => 'MP14 - Quietanza erario',
            'MP15' => 'MP15 - Giroconto su conti di contabilità speciale',
            'MP16' => 'MP16 - Domiciliazione bancaria',
            'MP17' => 'MP17 - Domiciliazione postale',
            'MP18' => 'MP18 - Bollettino di c/c postale',
            'MP19' => 'MP19 - SEPA Direct Debit',
            'MP20' => 'MP20 - SEPA Direct Debit CORE',
            'MP21' => 'MP21 - SEPA Direct Debit B2B',
            'MP22' => 'MP22 - Trattenuta su somme già riscosse',
            'MP23' => 'MP23 - PagoPA',
        ];
    }

    /**
     * Lista condizioni pagamento
     */
    protected function get_condizioni_pagamento()
    {
        return [
            'TP01' => 'TP01 - Pagamento a rate',
            'TP02' => 'TP02 - Pagamento completo',
            'TP03' => 'TP03 - Anticipo',
        ];
    }

    /**
     * Lista provider SDI per dropdown
     */
    protected function get_providers_list()
    {
        $providers = Sdi_client::getAvailableProviders();
        $list = [];

        foreach ($providers as $id => $info) {
            $list[$id] = $info['name'];
        }

        return $list;
    }
}

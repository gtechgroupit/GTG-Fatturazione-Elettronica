<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Plugin Name: Fatturazione Elettronica SDI
 * Plugin URI: https://github.com/gtechgroupit/GTG-Fatturazione-Elettronica
 * Description: Modulo per la fatturazione elettronica italiana tramite SDI (Sistema di Interscambio)
 *              dell'Agenzia delle Entrate. Supporta invio e ricezione fatture B2B/B2C/PA.
 * Version: 1.1.0
 * Author: GTech Group IT
 * Author URI: https://gtechgroup.it
 * Requires at least: 3.2
 * Tested up to: 3.4
 * Requires PHP: 8.3
 */

define('FATTURAZIONE_ELETTRONICA_MODULE_NAME', 'fatturazione_elettronica');
define('FATTURAZIONE_ELETTRONICA_MODULE_VERSION', '1.1.0');
define('FATTURAZIONE_ELETTRONICA_MODULE_PATH', __DIR__);

/**
 * Registra il modulo in Perfex CRM
 */
register_module([
    'module_name'          => FATTURAZIONE_ELETTRONICA_MODULE_NAME,
    'description'          => 'Modulo per la fatturazione elettronica italiana tramite SDI (Sistema di Interscambio) dell\'Agenzia delle Entrate. Supporta invio e ricezione fatture B2B/B2C/PA.',
    'author'               => 'GTech Group IT',
    'author_uri'           => 'https://gtechgroup.it',
    'version'              => FATTURAZIONE_ELETTRONICA_MODULE_VERSION,
    'requires_at_least'    => '3.2',
    'tested_up_to'         => '3.4',
    'requires_php'         => '8.3',
    'url_documentation'    => '',
    'url_changelog'        => '',
]);

// Percorsi del modulo
define('FE_ASSETS_PATH', module_dir_url(FATTURAZIONE_ELETTRONICA_MODULE_NAME, 'assets/'));
define('FE_VIEWS_PATH', FATTURAZIONE_ELETTRONICA_MODULE_PATH . '/views/');
define('FE_LIBRARIES_PATH', FATTURAZIONE_ELETTRONICA_MODULE_PATH . '/libraries/');

// Costanti per lo stato delle fatture SDI
define('FE_STATO_BOZZA', 'bozza');
define('FE_STATO_GENERATA', 'generata');
define('FE_STATO_INVIATA', 'inviata');
define('FE_STATO_CONSEGNATA', 'consegnata');
define('FE_STATO_NON_CONSEGNATA', 'non_consegnata');
define('FE_STATO_ACCETTATA', 'accettata');
define('FE_STATO_RIFIUTATA', 'rifiutata');
define('FE_STATO_DECORRENZA_TERMINI', 'decorrenza_termini');
define('FE_STATO_IMPOSSIBILITA_RECAPITO', 'impossibilita_recapito');
define('FE_STATO_SCARTATA', 'scartata');
define('FE_STATO_MANCATA_CONSEGNA', 'mancata_consegna');

// Costanti per il tipo di documento
define('FE_TD01', 'TD01'); // Fattura
define('FE_TD02', 'TD02'); // Acconto/Anticipo su fattura
define('FE_TD03', 'TD03'); // Acconto/Anticipo su parcella
define('FE_TD04', 'TD04'); // Nota di Credito
define('FE_TD05', 'TD05'); // Nota di Debito
define('FE_TD06', 'TD06'); // Parcella
define('FE_TD16', 'TD16'); // Integrazione fattura reverse charge interno
define('FE_TD17', 'TD17'); // Integrazione/autofattura per acquisto servizi dall'estero
define('FE_TD18', 'TD18'); // Integrazione per acquisto di beni intracomunitari
define('FE_TD19', 'TD19'); // Integrazione/autofattura per acquisto di beni ex art.17 c.2 DPR 633/72
define('FE_TD20', 'TD20'); // Autofattura per regolarizzazione e integrazione delle fatture
define('FE_TD21', 'TD21'); // Autofattura per splafonamento
define('FE_TD22', 'TD22'); // Estrazione beni da Deposito IVA
define('FE_TD23', 'TD23'); // Estrazione beni da Deposito IVA con versamento dell'IVA
define('FE_TD24', 'TD24'); // Fattura differita di cui all'art. 21, comma 4, lett. a)
define('FE_TD25', 'TD25'); // Fattura differita di cui all'art. 21, comma 4, terzo periodo lett. b)
define('FE_TD26', 'TD26'); // Cessione di beni ammortizzabili e per passaggi interni
define('FE_TD27', 'TD27'); // Fattura per autoconsumo o per cessioni gratuite senza rivalsa

// Regime fiscale
define('FE_RF01', 'RF01'); // Ordinario
define('FE_RF02', 'RF02'); // Contribuenti minimi
define('FE_RF04', 'RF04'); // Agricoltura e attività connesse e pesca
define('FE_RF05', 'RF05'); // Vendita sali e tabacchi
define('FE_RF06', 'RF06'); // Commercio dei fiammiferi
define('FE_RF07', 'RF07'); // Editoria
define('FE_RF08', 'RF08'); // Gestione di servizi di telefonia pubblica
define('FE_RF09', 'RF09'); // Rivendita di documenti di trasporto pubblico e di sosta
define('FE_RF10', 'RF10'); // Intrattenimenti, giochi e altre attività di cui alla tariffa allegata al DPR 640/72
define('FE_RF11', 'RF11'); // Agenzie di viaggi e turismo
define('FE_RF12', 'RF12'); // Agro-industria
define('FE_RF13', 'RF13'); // Vendite a domicilio
define('FE_RF14', 'RF14'); // Rivendita di beni usati, di oggetti d'arte, d'antiquariato o da collezione
define('FE_RF15', 'RF15'); // Agenzie di vendite all'asta di oggetti d'arte, antiquariato o da collezione
define('FE_RF16', 'RF16'); // IVA per cassa P.A.
define('FE_RF17', 'RF17'); // IVA per cassa
define('FE_RF18', 'RF18'); // Altro
define('FE_RF19', 'RF19'); // Forfettario

/**
 * Registra il modulo al caricamento
 */
hooks()->add_action('app_init', 'fatturazione_elettronica_init_module');
hooks()->add_action('admin_init', 'fatturazione_elettronica_admin_init');
hooks()->add_action('app_admin_head', 'fatturazione_elettronica_add_head_css');
hooks()->add_action('app_admin_footer', 'fatturazione_elettronica_add_footer_js');

// Hook per le fatture
hooks()->add_action('after_invoice_added', 'fatturazione_elettronica_after_invoice_added');
hooks()->add_action('after_invoice_updated', 'fatturazione_elettronica_after_invoice_updated');
hooks()->add_action('invoice_html_pdf_data', 'fatturazione_elettronica_invoice_pdf_data');

// Hook per le note di credito
hooks()->add_action('after_credit_note_added', 'fatturazione_elettronica_after_credit_note_added');
hooks()->add_action('after_credit_note_updated', 'fatturazione_elettronica_after_credit_note_updated');

// Menu administration
hooks()->add_action('admin_init', 'fatturazione_elettronica_register_menu');

// Permessi
hooks()->add_action('admin_init', 'fatturazione_elettronica_register_permissions');

// Cron job per controllo notifiche SDI
hooks()->add_action('cron_job', 'fatturazione_elettronica_cron');

/**
 * Inizializzazione del modulo
 */
function fatturazione_elettronica_init_module()
{
    $CI = &get_instance();

    // Carica l'helper
    $CI->load->helper(FATTURAZIONE_ELETTRONICA_MODULE_NAME . '/fatturazione_elettronica');

    // Registra le autoload per le librerie
    spl_autoload_register(function ($class) {
        $prefix = 'FatturazioneElettronica\\';
        $base_dir = FE_LIBRARIES_PATH;

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });
}

/**
 * Inizializzazione admin
 */
function fatturazione_elettronica_admin_init()
{
    $CI = &get_instance();

    // Aggiungi i campi personalizzati per i clienti (Codice Destinatario, PEC)
    if (is_admin()) {
        hooks()->add_action('after_customer_billing_and_shipping_fields', 'fatturazione_elettronica_customer_fields');
        hooks()->add_filter('before_client_added', 'fatturazione_elettronica_before_client_added');
        hooks()->add_filter('before_client_updated', 'fatturazione_elettronica_before_client_updated');
    }
}

/**
 * Aggiunge CSS nell'head admin
 */
function fatturazione_elettronica_add_head_css()
{
    echo '<link rel="stylesheet" type="text/css" href="' . FE_ASSETS_PATH . 'css/fatturazione_elettronica.css?v=' . FATTURAZIONE_ELETTRONICA_MODULE_VERSION . '">';
}

/**
 * Aggiunge JS nel footer admin
 */
function fatturazione_elettronica_add_footer_js()
{
    echo '<script src="' . FE_ASSETS_PATH . 'js/fatturazione_elettronica.js?v=' . FATTURAZIONE_ELETTRONICA_MODULE_VERSION . '"></script>';
}

/**
 * Registra il menu nel pannello admin
 */
function fatturazione_elettronica_register_menu()
{
    $CI = &get_instance();

    // Menu principale Fatturazione Elettronica
    if (has_permission('fatturazione_elettronica', '', 'view') || is_admin()) {
        $CI->app_menu->add_sidebar_menu_item('fatturazione-elettronica', [
            'name'     => _l('fe_menu_title'),
            'icon'     => 'fa-solid fa-file-invoice',
            'position' => 15,
        ]);

        // Sottomenu Dashboard
        $CI->app_menu->add_sidebar_children_item('fatturazione-elettronica', [
            'slug'     => 'fe-dashboard',
            'name'     => _l('fe_dashboard'),
            'href'     => admin_url('fatturazione_elettronica'),
            'position' => 1,
        ]);

        // Sottomenu Fatture Attive (Vendita)
        $CI->app_menu->add_sidebar_children_item('fatturazione-elettronica', [
            'slug'     => 'fe-fatture-attive',
            'name'     => _l('fe_fatture_attive'),
            'href'     => admin_url('fatturazione_elettronica/fatture_attive'),
            'position' => 2,
        ]);

        // Sottomenu Fatture Passive (Acquisto)
        $CI->app_menu->add_sidebar_children_item('fatturazione-elettronica', [
            'slug'     => 'fe-fatture-passive',
            'name'     => _l('fe_fatture_passive'),
            'href'     => admin_url('fatturazione_elettronica/fatture_passive'),
            'position' => 3,
        ]);

        // Sottomenu Impostazioni
        if (has_permission('fatturazione_elettronica', '', 'edit') || is_admin()) {
            $CI->app_menu->add_sidebar_children_item('fatturazione-elettronica', [
                'slug'     => 'fe-impostazioni',
                'name'     => _l('fe_impostazioni'),
                'href'     => admin_url('fatturazione_elettronica/impostazioni'),
                'position' => 10,
            ]);
        }
    }
}

/**
 * Registra i permessi del modulo
 */
function fatturazione_elettronica_register_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('fatturazione_elettronica', $capabilities, _l('fe_menu_title'));
}

/**
 * Campi aggiuntivi per i clienti (Codice Destinatario, PEC)
 */
function fatturazione_elettronica_customer_fields($client)
{
    $CI = &get_instance();
    $CI->load->model(FATTURAZIONE_ELETTRONICA_MODULE_NAME . '/Fatturazione_elettronica_model');

    $codice_destinatario = '';
    $pec = '';
    $codice_fiscale = '';
    $split_payment = 0;

    if (isset($client->userid) && $client->userid > 0) {
        $codice_destinatario = get_client_meta($client->userid, 'fe_codice_destinatario');
        $pec = get_client_meta($client->userid, 'fe_pec');
        $codice_fiscale = get_client_meta($client->userid, 'fe_codice_fiscale');
        $split_payment = get_client_meta($client->userid, 'fe_split_payment');
    }

    echo $CI->load->view(
        FATTURAZIONE_ELETTRONICA_MODULE_NAME . '/partials/customer_fields',
        [
            'codice_destinatario' => $codice_destinatario,
            'pec'                 => $pec,
            'codice_fiscale'      => $codice_fiscale,
            'split_payment'       => $split_payment,
        ],
        true
    );
}

/**
 * Salva i campi personalizzati prima di aggiungere un cliente
 */
function fatturazione_elettronica_before_client_added($data)
{
    return fatturazione_elettronica_process_client_data($data);
}

/**
 * Salva i campi personalizzati prima di aggiornare un cliente
 */
function fatturazione_elettronica_before_client_updated($data)
{
    return fatturazione_elettronica_process_client_data($data);
}

/**
 * Processa i dati del cliente per i campi SDI
 */
function fatturazione_elettronica_process_client_data($data)
{
    $CI = &get_instance();

    // Salva i campi personalizzati nella sessione per usarli dopo
    if (isset($_POST['fe_codice_destinatario'])) {
        $CI->session->set_userdata('fe_codice_destinatario', $_POST['fe_codice_destinatario']);
    }
    if (isset($_POST['fe_pec'])) {
        $CI->session->set_userdata('fe_pec', $_POST['fe_pec']);
    }
    if (isset($_POST['fe_codice_fiscale'])) {
        $CI->session->set_userdata('fe_codice_fiscale', $_POST['fe_codice_fiscale']);
    }
    if (isset($_POST['fe_split_payment'])) {
        $CI->session->set_userdata('fe_split_payment', $_POST['fe_split_payment']);
    }

    return $data;
}

/**
 * Hook dopo l'aggiunta di una fattura
 */
function fatturazione_elettronica_after_invoice_added($invoice_id)
{
    fatturazione_elettronica_process_new_invoice($invoice_id);
}

/**
 * Hook dopo l'aggiornamento di una fattura
 */
function fatturazione_elettronica_after_invoice_updated($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model(FATTURAZIONE_ELETTRONICA_MODULE_NAME . '/Fatturazione_elettronica_model');

    // Se la fattura non è già stata inviata, aggiorna il record
    $fe_invoice = $CI->Fatturazione_elettronica_model->get_fattura_attiva_by_invoice_id($invoice_id);

    if ($fe_invoice && $fe_invoice->stato == FE_STATO_BOZZA) {
        // Rigenera l'XML se necessario
        $CI->Fatturazione_elettronica_model->update_fattura_attiva_from_invoice($invoice_id);
    }
}

/**
 * Processa una nuova fattura per la fatturazione elettronica
 */
function fatturazione_elettronica_process_new_invoice($invoice_id)
{
    $CI = &get_instance();
    $CI->load->model(FATTURAZIONE_ELETTRONICA_MODULE_NAME . '/Fatturazione_elettronica_model');

    // Verifica se l'invio automatico è abilitato
    $auto_generate = get_option('fe_auto_generate_xml');

    if ($auto_generate == '1') {
        // Crea il record per la fattura elettronica
        $CI->Fatturazione_elettronica_model->create_fattura_attiva($invoice_id);
    }
}

/**
 * Hook dopo l'aggiunta di una nota di credito
 */
function fatturazione_elettronica_after_credit_note_added($credit_note_id)
{
    $CI = &get_instance();
    $CI->load->model(FATTURAZIONE_ELETTRONICA_MODULE_NAME . '/Fatturazione_elettronica_model');

    $auto_generate = get_option('fe_auto_generate_xml');

    if ($auto_generate == '1') {
        $CI->Fatturazione_elettronica_model->create_nota_credito($credit_note_id);
    }
}

/**
 * Hook dopo l'aggiornamento di una nota di credito
 */
function fatturazione_elettronica_after_credit_note_updated($credit_note_id)
{
    $CI = &get_instance();
    $CI->load->model(FATTURAZIONE_ELETTRONICA_MODULE_NAME . '/Fatturazione_elettronica_model');

    $fe_credit = $CI->Fatturazione_elettronica_model->get_nota_credito_by_credit_note_id($credit_note_id);

    if ($fe_credit && $fe_credit->stato == FE_STATO_BOZZA) {
        $CI->Fatturazione_elettronica_model->update_nota_credito_from_credit_note($credit_note_id);
    }
}

/**
 * Dati aggiuntivi per il PDF della fattura
 */
function fatturazione_elettronica_invoice_pdf_data($data)
{
    $CI = &get_instance();

    if (isset($data['invoice']->clientid)) {
        $codice_destinatario = get_client_meta($data['invoice']->clientid, 'fe_codice_destinatario');
        $pec = get_client_meta($data['invoice']->clientid, 'fe_pec');
        $codice_fiscale = get_client_meta($data['invoice']->clientid, 'fe_codice_fiscale');

        $data['fe_codice_destinatario'] = $codice_destinatario ?: '';
        $data['fe_pec'] = $pec ?: '';
        $data['fe_codice_fiscale'] = $codice_fiscale ?: '';
    }

    return $data;
}

/**
 * Cron job per controllo stato fatture e ricezione notifiche
 */
function fatturazione_elettronica_cron()
{
    $CI = &get_instance();
    $CI->load->model(FATTURAZIONE_ELETTRONICA_MODULE_NAME . '/Fatturazione_elettronica_model');
    $CI->load->helper(FATTURAZIONE_ELETTRONICA_MODULE_NAME . '/fatturazione_elettronica');

    // Non eseguire se il provider è in modalità test
    if (fe_is_test_mode()) {
        return;
    }

    $stats = [
        'stati_verificati' => 0,
        'passive_scaricate' => 0,
    ];

    try {
        // Controlla lo stato delle fatture inviate
        $stats['stati_verificati'] = $CI->Fatturazione_elettronica_model->cron_verifica_stati();

        // Scarica le fatture passive
        $stats['passive_scaricate'] = $CI->Fatturazione_elettronica_model->cron_sync_passive();

        // Log dell'esecuzione
        if ($stats['stati_verificati'] > 0 || $stats['passive_scaricate'] > 0) {
            fe_log('cron', sprintf(
                'Cron completato: %d stati aggiornati, %d fatture passive scaricate',
                $stats['stati_verificati'],
                $stats['passive_scaricate']
            ));
        }

        log_activity('Fatturazione Elettronica - Cron: ' . json_encode($stats));
    } catch (Exception $e) {
        fe_log('errore', 'Errore cron: ' . $e->getMessage());
        log_activity('Fatturazione Elettronica - Errore cron: ' . $e->getMessage());
    }
}

/**
 * Funzione di attivazione del modulo
 */
function fatturazione_elettronica_activate()
{
    $CI = &get_instance();

    // Esegui le migrazioni
    require_once(FATTURAZIONE_ELETTRONICA_MODULE_PATH . '/install.php');
}

/**
 * Funzione di disattivazione del modulo
 */
function fatturazione_elettronica_deactivate()
{
    // Non rimuoviamo i dati per sicurezza
}

/**
 * Info sul modulo
 */
register_activation_hook(FATTURAZIONE_ELETTRONICA_MODULE_NAME, 'fatturazione_elettronica_activate');
register_deactivation_hook(FATTURAZIONE_ELETTRONICA_MODULE_NAME, 'fatturazione_elettronica_deactivate');

// Registra il modulo per gli aggiornamenti automatici
hooks()->add_filter('module_' . FATTURAZIONE_ELETTRONICA_MODULE_NAME . '_action_links', function ($actions) {
    $actions[] = '<a href="' . admin_url('fatturazione_elettronica/impostazioni') . '">' . _l('settings') . '</a>';
    return $actions;
});

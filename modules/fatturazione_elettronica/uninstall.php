<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * File di disinstallazione del modulo Fatturazione Elettronica
 *
 * ATTENZIONE: Questo file rimuove TUTTI i dati del modulo.
 * Le fatture elettroniche devono essere conservate per 10 anni secondo la normativa italiana.
 * Assicurarsi di aver esportato tutti i dati prima di procedere.
 */

$CI = &get_instance();

// Conferma richiesta per la rimozione dei dati
$remove_data = isset($_POST['remove_data']) && $_POST['remove_data'] == '1';

if ($remove_data) {
    // Rimuovi le tabelle
    $CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "fe_fatture_attive`");
    $CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "fe_fatture_passive`");
    $CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "fe_notifiche`");
    $CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "fe_progressivo`");
    $CI->db->query("DROP TABLE IF EXISTS `" . db_prefix() . "fe_log`");

    // Rimuovi le opzioni
    $options = [
        'fe_denominazione',
        'fe_partita_iva',
        'fe_codice_fiscale',
        'fe_regime_fiscale',
        'fe_indirizzo',
        'fe_cap',
        'fe_comune',
        'fe_provincia',
        'fe_nazione',
        'fe_telefono',
        'fe_email',
        'fe_pec',
        'fe_codice_destinatario',
        'fe_rea_ufficio',
        'fe_rea_numero',
        'fe_capitale_sociale',
        'fe_socio_unico',
        'fe_stato_liquidazione',
        'fe_rappresentante_fiscale_piva',
        'fe_provider',
        'fe_api_endpoint',
        'fe_api_username',
        'fe_api_password',
        'fe_api_key',
        'fe_api_secret',
        'fe_ambiente',
        'fe_firma_automatica',
        'fe_certificato_path',
        'fe_certificato_password',
        'fe_auto_generate_xml',
        'fe_auto_send',
        'fe_formato_progressivo',
        'fe_bollo_virtuale_soglia',
        'fe_bollo_virtuale_importo',
        'fe_email_notifiche',
        'fe_default_tipo_cassa',
        'fe_default_modalita_pagamento',
        'fe_default_condizioni_pagamento',
        'fe_conservazione_sostitutiva',
        'fe_webhook_secret',
        'fe_webhook_enabled',
    ];

    foreach ($options as $option) {
        delete_option($option);
    }

    // Rimuovi i meta dei clienti
    $CI->db->where('fieldname LIKE', 'fe_%');
    $CI->db->delete(db_prefix() . 'customfieldsvalues');

    // Rimuovi i file XML salvati
    $upload_path = get_upload_path_by_type('fatturazione_elettronica');
    if (is_dir($upload_path)) {
        delete_dir($upload_path);
    }

    log_activity('Modulo Fatturazione Elettronica disinstallato - tutti i dati rimossi');
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * File di installazione del modulo Fatturazione Elettronica
 * Crea le tabelle necessarie e le opzioni di configurazione
 */

$CI = &get_instance();

// ============================================================================
// TABELLA: tblfe_fatture_attive (fatture di vendita)
// ============================================================================
if (!$CI->db->table_exists(db_prefix() . 'fe_fatture_attive')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "fe_fatture_attive` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `invoice_id` INT(11) NOT NULL,
            `credit_note_id` INT(11) NULL DEFAULT NULL,
            `tipo_documento` VARCHAR(10) NOT NULL DEFAULT 'TD01',
            `progressivo_invio` VARCHAR(10) NULL DEFAULT NULL,
            `identificativo_sdi` VARCHAR(50) NULL DEFAULT NULL,
            `nome_file` VARCHAR(255) NULL DEFAULT NULL,
            `xml_content` LONGTEXT NULL DEFAULT NULL,
            `xml_firmato` LONGTEXT NULL DEFAULT NULL,
            `stato` VARCHAR(50) NOT NULL DEFAULT 'bozza',
            `data_invio` DATETIME NULL DEFAULT NULL,
            `data_ricezione_esito` DATETIME NULL DEFAULT NULL,
            `esito_sdi` VARCHAR(50) NULL DEFAULT NULL,
            `descrizione_esito` TEXT NULL DEFAULT NULL,
            `notifica_xml` LONGTEXT NULL DEFAULT NULL,
            `tentativi_invio` INT(11) NOT NULL DEFAULT 0,
            `ultimo_errore` TEXT NULL DEFAULT NULL,
            `data_ultimo_check` DATETIME NULL DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `invoice_id` (`invoice_id`),
            KEY `credit_note_id` (`credit_note_id`),
            KEY `stato` (`stato`),
            KEY `identificativo_sdi` (`identificativo_sdi`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// ============================================================================
// TABELLA: tblfe_fatture_passive (fatture di acquisto)
// ============================================================================
if (!$CI->db->table_exists(db_prefix() . 'fe_fatture_passive')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "fe_fatture_passive` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `identificativo_sdi` VARCHAR(50) NOT NULL,
            `nome_file` VARCHAR(255) NOT NULL,
            `fornitore_denominazione` VARCHAR(255) NULL DEFAULT NULL,
            `fornitore_partita_iva` VARCHAR(20) NULL DEFAULT NULL,
            `fornitore_codice_fiscale` VARCHAR(20) NULL DEFAULT NULL,
            `tipo_documento` VARCHAR(10) NOT NULL,
            `numero_documento` VARCHAR(50) NOT NULL,
            `data_documento` DATE NOT NULL,
            `imponibile` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `iva` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `totale` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `xml_content` LONGTEXT NOT NULL,
            `xml_originale` LONGTEXT NULL DEFAULT NULL,
            `stato` VARCHAR(50) NOT NULL DEFAULT 'ricevuta',
            `data_ricezione` DATETIME NOT NULL,
            `expense_id` INT(11) NULL DEFAULT NULL COMMENT 'ID spesa collegata in Perfex',
            `vendor_id` INT(11) NULL DEFAULT NULL COMMENT 'ID fornitore collegato',
            `note` TEXT NULL DEFAULT NULL,
            `letto` TINYINT(1) NOT NULL DEFAULT 0,
            `archiviato` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `identificativo_sdi` (`identificativo_sdi`),
            KEY `fornitore_partita_iva` (`fornitore_partita_iva`),
            KEY `data_documento` (`data_documento`),
            KEY `stato` (`stato`),
            KEY `expense_id` (`expense_id`),
            KEY `vendor_id` (`vendor_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// ============================================================================
// TABELLA: tblfe_notifiche (storico notifiche SDI)
// ============================================================================
if (!$CI->db->table_exists(db_prefix() . 'fe_notifiche')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "fe_notifiche` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `fattura_attiva_id` INT(11) NULL DEFAULT NULL,
            `fattura_passiva_id` INT(11) NULL DEFAULT NULL,
            `identificativo_sdi` VARCHAR(50) NOT NULL,
            `tipo_notifica` VARCHAR(50) NOT NULL COMMENT 'RC, NS, MC, EC, DT, AT, NE, MT',
            `nome_file` VARCHAR(255) NOT NULL,
            `xml_content` LONGTEXT NOT NULL,
            `descrizione` TEXT NULL DEFAULT NULL,
            `codice_errore` VARCHAR(20) NULL DEFAULT NULL,
            `data_ricezione` DATETIME NOT NULL,
            `elaborata` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fattura_attiva_id` (`fattura_attiva_id`),
            KEY `fattura_passiva_id` (`fattura_passiva_id`),
            KEY `identificativo_sdi` (`identificativo_sdi`),
            KEY `tipo_notifica` (`tipo_notifica`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// ============================================================================
// TABELLA: tblfe_progressivo (contatore progressivo invio)
// ============================================================================
if (!$CI->db->table_exists(db_prefix() . 'fe_progressivo')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "fe_progressivo` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `anno` INT(4) NOT NULL,
            `ultimo_progressivo` INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `anno` (`anno`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Inserisci il record per l'anno corrente
    $CI->db->insert(db_prefix() . 'fe_progressivo', [
        'anno'              => date('Y'),
        'ultimo_progressivo' => 0
    ]);
}

// ============================================================================
// TABELLA: tblfe_log (log operazioni)
// ============================================================================
if (!$CI->db->table_exists(db_prefix() . 'fe_log')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "fe_log` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `fattura_attiva_id` INT(11) NULL DEFAULT NULL,
            `fattura_passiva_id` INT(11) NULL DEFAULT NULL,
            `azione` VARCHAR(100) NOT NULL,
            `descrizione` TEXT NULL DEFAULT NULL,
            `staff_id` INT(11) NULL DEFAULT NULL,
            `ip_address` VARCHAR(45) NULL DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fattura_attiva_id` (`fattura_attiva_id`),
            KEY `fattura_passiva_id` (`fattura_passiva_id`),
            KEY `azione` (`azione`),
            KEY `created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// ============================================================================
// OPZIONI DI CONFIGURAZIONE
// ============================================================================

// Dati azienda per FatturaPA
add_option('fe_denominazione', '', 0);
add_option('fe_partita_iva', '', 0);
add_option('fe_codice_fiscale', '', 0);
add_option('fe_regime_fiscale', 'RF01', 0);
add_option('fe_indirizzo', '', 0);
add_option('fe_cap', '', 0);
add_option('fe_comune', '', 0);
add_option('fe_provincia', '', 0);
add_option('fe_nazione', 'IT', 0);
add_option('fe_telefono', '', 0);
add_option('fe_email', '', 0);
add_option('fe_pec', '', 0);
add_option('fe_codice_destinatario', '', 0);
add_option('fe_rea_ufficio', '', 0);
add_option('fe_rea_numero', '', 0);
add_option('fe_capitale_sociale', '', 0);
add_option('fe_socio_unico', '', 0); // SU, SM
add_option('fe_stato_liquidazione', 'LN', 0); // LN, LS
add_option('fe_rappresentante_fiscale_piva', '', 0);

// Configurazione SDI Provider
add_option('fe_provider', 'aruba', 0); // aruba, infocert, custom, test
add_option('fe_api_endpoint', '', 0);
add_option('fe_api_username', '', 0);
add_option('fe_api_password', '', 0);
add_option('fe_api_key', '', 0);
add_option('fe_api_secret', '', 0);
add_option('fe_ambiente', 'test', 0); // test, produzione

// Configurazione certificato firma
add_option('fe_firma_automatica', '0', 0);
add_option('fe_certificato_path', '', 0);
add_option('fe_certificato_password', '', 0);

// Opzioni generali
add_option('fe_auto_generate_xml', '1', 0);
add_option('fe_auto_send', '0', 0);
add_option('fe_formato_progressivo', 'XXXXX', 0); // 5 caratteri alfanumerici
add_option('fe_bollo_virtuale_soglia', '77.47', 0);
add_option('fe_bollo_virtuale_importo', '2.00', 0);
add_option('fe_email_notifiche', '1', 0);
add_option('fe_default_tipo_cassa', '', 0); // TC01-TC22
add_option('fe_default_modalita_pagamento', 'MP05', 0);
add_option('fe_default_condizioni_pagamento', 'TP02', 0);
add_option('fe_conservazione_sostitutiva', '0', 0);

// Webhook per ricezione notifiche
add_option('fe_webhook_secret', bin2hex(random_bytes(32)), 0);
add_option('fe_webhook_enabled', '1', 0);

// Log dell'installazione
log_activity('Modulo Fatturazione Elettronica installato - versione ' . FATTURAZIONE_ELETTRONICA_MODULE_VERSION);

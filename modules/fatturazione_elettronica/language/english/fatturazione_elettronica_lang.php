<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * English language file for Electronic Invoicing module
 */

// Menu and titles
$lang['fe_menu_title'] = 'Electronic Invoicing';
$lang['fe_dashboard'] = 'Dashboard';
$lang['fe_fatture_attive'] = 'Sales Invoices';
$lang['fe_fatture_passive'] = 'Purchase Invoices';
$lang['fe_impostazioni'] = 'Settings';
$lang['fe_fattura_dettaglio'] = 'Invoice Detail';
$lang['fe_fattura_passiva_dettaglio'] = 'Purchase Invoice Detail';

// Dashboard
$lang['fe_totale_fatture_attive'] = 'Total Sales Invoices';
$lang['fe_consegnate'] = 'Delivered';
$lang['fe_in_attesa'] = 'Pending';
$lang['fe_con_errori'] = 'With Errors';
$lang['fe_fatture_passive_totali'] = 'Total Purchase Invoices';
$lang['fe_non_lette'] = 'Unread';
$lang['fe_da_processare'] = 'To Process';
$lang['fe_vedi_tutte'] = 'View all';
$lang['fe_vedi_dettaglio'] = 'View detail';
$lang['fe_azioni_rapide'] = 'Quick Actions';
$lang['fe_genera_fattura'] = 'Generate Invoice';
$lang['fe_sincronizza_passive'] = 'Sync Purchase Invoices';
$lang['fe_test_connessione'] = 'Test Connection';
$lang['fe_log_recenti'] = 'Recent Logs';
$lang['fe_no_logs'] = 'No logs available';
$lang['fe_da_inviare'] = 'Invoices to Send';
$lang['fe_no_fatture_da_inviare'] = 'No invoices to send';
$lang['fe_configurazione_incompleta'] = 'Incomplete Configuration';
$lang['fe_configurazione_incompleta_msg'] = 'Before using the module, you need to complete the configuration with company data and SDI provider credentials.';
$lang['fe_vai_impostazioni'] = 'Go to Settings';

// Invoice statuses
$lang['fe_stato_bozza'] = 'Draft';
$lang['fe_stato_generata'] = 'Generated';
$lang['fe_stato_inviata'] = 'Sent';
$lang['fe_stato_consegnata'] = 'Delivered';
$lang['fe_stato_non_consegnata'] = 'Not Delivered';
$lang['fe_stato_accettata'] = 'Accepted';
$lang['fe_stato_rifiutata'] = 'Rejected';
$lang['fe_stato_decorrenza_termini'] = 'Terms Expiry';
$lang['fe_stato_impossibilita_recapito'] = 'Delivery Failure';
$lang['fe_stato_scartata'] = 'Discarded';
$lang['fe_stato_mancata_consegna'] = 'Failed Delivery';
$lang['fe_stato_ricevuta'] = 'Received';

// Invoice table
$lang['fe_nome_file'] = 'File Name';
$lang['fe_tipo'] = 'Type';
$lang['fe_id_sdi'] = 'SDI ID';
$lang['fe_stato'] = 'Status';
$lang['fe_data_creazione'] = 'Created Date';
$lang['fe_data_invio'] = 'Send Date';
$lang['fe_data_ricezione'] = 'Received Date';
$lang['fe_importa_fattura'] = 'Import Invoice';
$lang['fe_tutti_stati'] = 'All statuses';
$lang['fe_da_data'] = 'From date';
$lang['fe_a_data'] = 'To date';
$lang['fe_filtra'] = 'Filter';

// Actions
$lang['fe_visualizza'] = 'View';
$lang['fe_download'] = 'Download';
$lang['fe_invia'] = 'Send';
$lang['fe_elimina'] = 'Delete';
$lang['fe_archivia'] = 'Archive';
$lang['fe_invia_sdi'] = 'Send to SDI';
$lang['fe_download_xml'] = 'Download XML';
$lang['fe_rigenera_xml'] = 'Regenerate XML';
$lang['fe_verifica_stato'] = 'Check Status';
$lang['fe_crea_spesa'] = 'Create Expense';
$lang['fe_invia_selezionate'] = 'Send Selected';
$lang['fe_sincronizza'] = 'Synchronize';

// Confirmations
$lang['fe_confirm_send'] = 'Are you sure you want to send this invoice to SDI?';
$lang['fe_confirm_bulk_send'] = 'Are you sure you want to send the selected invoices to SDI?';
$lang['fe_confirm_regenerate'] = 'Are you sure you want to regenerate the XML? Previous changes will be lost.';

// Invoice detail
$lang['fe_info_documento'] = 'Document Information';
$lang['fe_progressivo'] = 'Progressive';
$lang['fe_date'] = 'Dates';
$lang['fe_data_esito'] = 'Outcome Date';
$lang['fe_tentativi_invio'] = 'Send Attempts';
$lang['fe_ultimo_errore'] = 'Last Error';
$lang['fe_esito_sdi'] = 'SDI Outcome';
$lang['fe_fattura_collegata'] = 'Linked Invoice';
$lang['fe_nota_credito_collegata'] = 'Linked Credit Note';
$lang['fe_anteprima_xml'] = 'XML Preview';
$lang['fe_notifiche_sdi'] = 'SDI Notifications';
$lang['fe_azioni'] = 'Actions';
$lang['fe_storico'] = 'History';
$lang['fe_torna_lista'] = 'Back to List';
$lang['fe_fattura_perfex'] = 'Perfex Invoice';
$lang['fe_nota_credito'] = 'Credit Note';
$lang['fe_testing'] = 'Testing...';
$lang['fe_verificando'] = 'Checking...';

// Purchase invoices
$lang['fe_fornitore'] = 'Supplier';
$lang['fe_numero'] = 'Number';
$lang['fe_data'] = 'Date';
$lang['fe_totale'] = 'Total';
$lang['fe_solo_non_lette'] = 'Unread only';
$lang['fe_collegata'] = 'Linked';
$lang['fe_archiviata'] = 'Archived';
$lang['fe_non_letta'] = 'Unread';
$lang['fe_dati_fornitore'] = 'Supplier Data';
$lang['fe_dati_documento'] = 'Document Data';
$lang['fe_righe_documento'] = 'Document Lines';
$lang['fe_riepilogo_iva'] = 'VAT Summary';
$lang['fe_dati_pagamento'] = 'Payment Data';
$lang['fe_descrizione'] = 'Description';
$lang['fe_quantita'] = 'Quantity';
$lang['fe_prezzo_unitario'] = 'Unit Price';
$lang['fe_iva'] = 'VAT';
$lang['fe_aliquota'] = 'Rate';
$lang['fe_natura'] = 'Nature';
$lang['fe_imponibile'] = 'Taxable';
$lang['fe_imposta'] = 'Tax';
$lang['fe_modalita'] = 'Method';
$lang['fe_scadenza'] = 'Due Date';
$lang['fe_importo'] = 'Amount';
$lang['fe_iban'] = 'IBAN';
$lang['fe_totali'] = 'Totals';
$lang['fe_spesa_collegata'] = 'Linked Expense';
$lang['fe_note'] = 'Notes';

// Settings
$lang['fe_dati_azienda'] = 'Company Data';
$lang['fe_provider_sdi'] = 'SDI Provider';
$lang['fe_opzioni'] = 'Options';
$lang['fe_denominazione'] = 'Company Name';
$lang['fe_partita_iva'] = 'VAT Number';
$lang['fe_codice_fiscale'] = 'Tax Code';
$lang['fe_regime_fiscale'] = 'Tax Regime';
$lang['fe_codice_destinatario'] = 'Recipient Code';
$lang['fe_codice_destinatario_help'] = 'Your company SDI recipient code (to receive purchase invoices)';
$lang['fe_codice_destinatario_cliente_help'] = '7-character SDI code (e.g., M5UXCR1) or 6 for PA';
$lang['fe_pec'] = 'Certified Email (PEC)';
$lang['fe_pec_help'] = 'Used if customer has no recipient code';
$lang['fe_sede_legale'] = 'Registered Office';
$lang['fe_indirizzo'] = 'Address';
$lang['fe_cap'] = 'ZIP Code';
$lang['fe_comune'] = 'City';
$lang['fe_provincia'] = 'Province';
$lang['fe_nazione'] = 'Country';
$lang['fe_telefono'] = 'Phone';
$lang['fe_email'] = 'Email';
$lang['fe_dati_rea'] = 'REA Data';
$lang['fe_rea_ufficio'] = 'REA Office';
$lang['fe_rea_numero'] = 'REA Number';
$lang['fe_capitale_sociale'] = 'Share Capital';
$lang['fe_socio_unico'] = 'Sole Shareholder';
$lang['fe_stato_liquidazione'] = 'Liquidation Status';

// Provider
$lang['fe_provider'] = 'Provider';
$lang['fe_ambiente'] = 'Environment';
$lang['fe_ambiente_test'] = 'Test / Sandbox';
$lang['fe_ambiente_produzione'] = 'Production';
$lang['fe_api_endpoint'] = 'API Endpoint';
$lang['fe_api_username'] = 'Username';
$lang['fe_api_password'] = 'Password';
$lang['fe_api_key'] = 'API Key';
$lang['fe_api_secret'] = 'API Secret';
$lang['fe_provider_test'] = 'Test Mode (Local)';
$lang['fe_provider_custom'] = 'Custom API';
$lang['fe_provider_test_help'] = 'In test mode, invoices are saved locally without being sent to SDI. Useful for development and testing.';
$lang['fe_provider_aruba_help'] = 'To use Aruba, enter your Aruba Electronic Invoicing service credentials.';
$lang['fe_provider_infocert_help'] = 'To use InfoCert, enter the API Key provided by Legalinvoice service.';
$lang['fe_provider_fattureincloud_help'] = 'To use Fatture in Cloud, enter the API Key and Secret from the developers area.';
$lang['fe_provider_custom_help'] = 'Enter your custom API endpoint and authentication credentials.';
$lang['fe_connection_ok'] = 'Connection successful';
$lang['fe_connection_failed'] = 'Connection failed';

// Options
$lang['fe_opzioni_generazione'] = 'Generation Options';
$lang['fe_auto_generate_xml'] = 'Automatically generate XML';
$lang['fe_auto_generate_xml_help'] = 'Automatically generate electronic invoice XML when a new invoice is created';
$lang['fe_auto_send'] = 'Automatic send';
$lang['fe_auto_send_help'] = 'Automatically send the invoice to SDI after XML generation';
$lang['fe_bollo_virtuale'] = 'Virtual Stamp Duty';
$lang['fe_bollo_soglia'] = 'Stamp Threshold';
$lang['fe_bollo_soglia_help'] = 'Amount above which to apply virtual stamp duty (default: €77.47)';
$lang['fe_bollo_importo'] = 'Stamp Amount';
$lang['fe_default_pagamento'] = 'Default Payment';
$lang['fe_modalita_pagamento'] = 'Payment Method';
$lang['fe_condizioni_pagamento'] = 'Payment Conditions';
$lang['fe_notifiche'] = 'Notifications';
$lang['fe_email_notifiche'] = 'Send email notifications for SDI outcomes';

// Webhook
$lang['fe_webhook_config'] = 'Webhook Configuration';
$lang['fe_webhook_help'] = 'Configure webhook to receive SDI notifications in real-time. Enter the webhook URL in your SDI provider panel.';
$lang['fe_webhook_enabled'] = 'Enable Webhook';
$lang['fe_webhook_url'] = 'Webhook URL';
$lang['fe_webhook_secret'] = 'Webhook Secret';
$lang['fe_webhook_secret_help'] = 'This secret is used to validate webhook requests. Configure it also in your SDI provider if supported.';
$lang['fe_copied'] = 'Copied!';

// Customer fields
$lang['fe_dati_fatturazione_elettronica'] = 'Electronic Invoicing Data';
$lang['fe_split_payment'] = 'Split Payment';
$lang['fe_split_payment_help'] = 'Enable for PA customers subject to split payment';

// Import modal
$lang['fe_seleziona_fattura'] = 'Select Invoice';
$lang['fe_seleziona'] = 'Select...';
$lang['fe_fatture_non_importate'] = 'Show only invoices not yet imported';
$lang['fe_tipo_documento'] = 'Document Type';
$lang['fe_genera_xml'] = 'Generate XML';
$lang['fe_caricamento'] = 'Loading...';
$lang['fe_no_fatture_importabili'] = 'No invoices to import';

// Messages
$lang['fe_fattura_not_found'] = 'Invoice not found';
$lang['fe_invoice_id_required'] = 'Invoice ID required';
$lang['fe_error_generating_xml'] = 'Error generating XML';
$lang['fe_xml_generated'] = 'XML generated successfully';
$lang['fe_error_sending'] = 'Error sending invoice';
$lang['fe_invoice_sent'] = 'Invoice sent successfully';
$lang['fe_cannot_delete'] = 'Cannot delete invoice';
$lang['fe_deleted'] = 'Invoice deleted successfully';
$lang['fe_error_regenerating'] = 'Error regenerating XML';
$lang['fe_xml_regenerated'] = 'XML regenerated successfully';
$lang['fe_no_selection'] = 'No invoice selected';
$lang['fe_bulk_send_result'] = 'Sent: %s, Failed: %s';
$lang['fe_sync_result'] = 'Synchronized %d new purchase invoices';
$lang['fe_error_creating_expense'] = 'Error creating expense';
$lang['fe_expense_created'] = 'Expense created successfully';
$lang['fe_archived'] = 'Invoice archived';

// Email notifications
$lang['fe_notifica_email_subject'] = 'Electronic Invoice: %s';
$lang['fe_notifica_email_body'] = 'Invoice %s changed status to: %s';
$lang['fe_notifica_email_subject_consegnata'] = 'Electronic Invoice Delivered';
$lang['fe_notifica_email_subject_accettata'] = 'Electronic Invoice Accepted';
$lang['fe_notifica_email_subject_rifiutata'] = 'Electronic Invoice Rejected';
$lang['fe_notifica_email_subject_scartata'] = 'Electronic Invoice Discarded';

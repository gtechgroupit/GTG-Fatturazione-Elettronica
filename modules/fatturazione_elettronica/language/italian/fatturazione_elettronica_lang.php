<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * File di lingua italiano per il modulo Fatturazione Elettronica
 */

// Menu e titoli
$lang['fe_menu_title'] = 'Fatturazione Elettronica';
$lang['fe_dashboard'] = 'Dashboard';
$lang['fe_fatture_attive'] = 'Fatture Attive';
$lang['fe_fatture_passive'] = 'Fatture Passive';
$lang['fe_impostazioni'] = 'Impostazioni';
$lang['fe_fattura_dettaglio'] = 'Dettaglio Fattura';
$lang['fe_fattura_passiva_dettaglio'] = 'Dettaglio Fattura Passiva';

// Dashboard
$lang['fe_totale_fatture_attive'] = 'Totale Fatture Attive';
$lang['fe_consegnate'] = 'Consegnate';
$lang['fe_in_attesa'] = 'In Attesa';
$lang['fe_con_errori'] = 'Con Errori';
$lang['fe_fatture_passive_totali'] = 'Fatture Passive Totali';
$lang['fe_non_lette'] = 'Non Lette';
$lang['fe_da_processare'] = 'Da Processare';
$lang['fe_vedi_tutte'] = 'Vedi tutte';
$lang['fe_vedi_dettaglio'] = 'Vedi dettaglio';
$lang['fe_azioni_rapide'] = 'Azioni Rapide';
$lang['fe_genera_fattura'] = 'Genera Fattura';
$lang['fe_sincronizza_passive'] = 'Sincronizza Passive';
$lang['fe_test_connessione'] = 'Test Connessione';
$lang['fe_log_recenti'] = 'Log Recenti';
$lang['fe_no_logs'] = 'Nessun log disponibile';
$lang['fe_da_inviare'] = 'Fatture da Inviare';
$lang['fe_no_fatture_da_inviare'] = 'Nessuna fattura da inviare';
$lang['fe_configurazione_incompleta'] = 'Configurazione Incompleta';
$lang['fe_configurazione_incompleta_msg'] = 'Prima di poter utilizzare il modulo, è necessario completare la configurazione con i dati aziendali e le credenziali del provider SDI.';
$lang['fe_vai_impostazioni'] = 'Vai alle Impostazioni';

// Stati fattura
$lang['fe_stato_bozza'] = 'Bozza';
$lang['fe_stato_generata'] = 'Generata';
$lang['fe_stato_inviata'] = 'Inviata';
$lang['fe_stato_consegnata'] = 'Consegnata';
$lang['fe_stato_non_consegnata'] = 'Non Consegnata';
$lang['fe_stato_accettata'] = 'Accettata';
$lang['fe_stato_rifiutata'] = 'Rifiutata';
$lang['fe_stato_decorrenza_termini'] = 'Decorrenza Termini';
$lang['fe_stato_impossibilita_recapito'] = 'Impossibilità Recapito';
$lang['fe_stato_scartata'] = 'Scartata';
$lang['fe_stato_mancata_consegna'] = 'Mancata Consegna';
$lang['fe_stato_ricevuta'] = 'Ricevuta';

// Tabella fatture
$lang['fe_nome_file'] = 'Nome File';
$lang['fe_tipo'] = 'Tipo';
$lang['fe_id_sdi'] = 'ID SDI';
$lang['fe_stato'] = 'Stato';
$lang['fe_data_creazione'] = 'Data Creazione';
$lang['fe_data_invio'] = 'Data Invio';
$lang['fe_data_ricezione'] = 'Data Ricezione';
$lang['fe_importa_fattura'] = 'Importa Fattura';
$lang['fe_tutti_stati'] = 'Tutti gli stati';
$lang['fe_da_data'] = 'Da data';
$lang['fe_a_data'] = 'A data';
$lang['fe_filtra'] = 'Filtra';

// Azioni
$lang['fe_visualizza'] = 'Visualizza';
$lang['fe_download'] = 'Download';
$lang['fe_invia'] = 'Invia';
$lang['fe_elimina'] = 'Elimina';
$lang['fe_archivia'] = 'Archivia';
$lang['fe_invia_sdi'] = 'Invia allo SDI';
$lang['fe_download_xml'] = 'Download XML';
$lang['fe_rigenera_xml'] = 'Rigenera XML';
$lang['fe_verifica_stato'] = 'Verifica Stato';
$lang['fe_crea_spesa'] = 'Crea Spesa';
$lang['fe_invia_selezionate'] = 'Invia Selezionate';
$lang['fe_sincronizza'] = 'Sincronizza';

// Conferme
$lang['fe_confirm_send'] = 'Sei sicuro di voler inviare questa fattura allo SDI?';
$lang['fe_confirm_bulk_send'] = 'Sei sicuro di voler inviare le fatture selezionate allo SDI?';
$lang['fe_confirm_regenerate'] = 'Sei sicuro di voler rigenerare l\'XML? Le modifiche precedenti andranno perse.';

// Dettaglio fattura
$lang['fe_info_documento'] = 'Informazioni Documento';
$lang['fe_progressivo'] = 'Progressivo';
$lang['fe_date'] = 'Date';
$lang['fe_data_esito'] = 'Data Esito';
$lang['fe_tentativi_invio'] = 'Tentativi Invio';
$lang['fe_ultimo_errore'] = 'Ultimo Errore';
$lang['fe_esito_sdi'] = 'Esito SDI';
$lang['fe_fattura_collegata'] = 'Fattura Collegata';
$lang['fe_nota_credito_collegata'] = 'Nota di Credito Collegata';
$lang['fe_anteprima_xml'] = 'Anteprima XML';
$lang['fe_notifiche_sdi'] = 'Notifiche SDI';
$lang['fe_azioni'] = 'Azioni';
$lang['fe_storico'] = 'Storico';
$lang['fe_torna_lista'] = 'Torna alla Lista';
$lang['fe_fattura_perfex'] = 'Fattura Perfex';
$lang['fe_nota_credito'] = 'Nota di Credito';
$lang['fe_testing'] = 'Verifica in corso...';
$lang['fe_verificando'] = 'Verifica in corso...';

// Fatture passive
$lang['fe_fornitore'] = 'Fornitore';
$lang['fe_numero'] = 'Numero';
$lang['fe_data'] = 'Data';
$lang['fe_totale'] = 'Totale';
$lang['fe_solo_non_lette'] = 'Solo non lette';
$lang['fe_collegata'] = 'Collegata';
$lang['fe_archiviata'] = 'Archiviata';
$lang['fe_non_letta'] = 'Non letta';
$lang['fe_dati_fornitore'] = 'Dati Fornitore';
$lang['fe_dati_documento'] = 'Dati Documento';
$lang['fe_righe_documento'] = 'Righe Documento';
$lang['fe_riepilogo_iva'] = 'Riepilogo IVA';
$lang['fe_dati_pagamento'] = 'Dati Pagamento';
$lang['fe_descrizione'] = 'Descrizione';
$lang['fe_quantita'] = 'Quantità';
$lang['fe_prezzo_unitario'] = 'Prezzo Unitario';
$lang['fe_iva'] = 'IVA';
$lang['fe_aliquota'] = 'Aliquota';
$lang['fe_natura'] = 'Natura';
$lang['fe_imponibile'] = 'Imponibile';
$lang['fe_imposta'] = 'Imposta';
$lang['fe_modalita'] = 'Modalità';
$lang['fe_scadenza'] = 'Scadenza';
$lang['fe_importo'] = 'Importo';
$lang['fe_iban'] = 'IBAN';
$lang['fe_totali'] = 'Totali';
$lang['fe_spesa_collegata'] = 'Spesa Collegata';
$lang['fe_note'] = 'Note';

// Impostazioni
$lang['fe_dati_azienda'] = 'Dati Azienda';
$lang['fe_provider_sdi'] = 'Provider SDI';
$lang['fe_opzioni'] = 'Opzioni';
$lang['fe_denominazione'] = 'Denominazione';
$lang['fe_partita_iva'] = 'Partita IVA';
$lang['fe_codice_fiscale'] = 'Codice Fiscale';
$lang['fe_regime_fiscale'] = 'Regime Fiscale';
$lang['fe_codice_destinatario'] = 'Codice Destinatario';
$lang['fe_codice_destinatario_help'] = 'Il codice destinatario SDI della tua azienda (per ricevere le fatture passive)';
$lang['fe_codice_destinatario_cliente_help'] = 'Codice SDI a 7 caratteri (es: M5UXCR1) o 6 per PA';
$lang['fe_pec'] = 'PEC';
$lang['fe_pec_help'] = 'Usata se il cliente non ha codice destinatario';
$lang['fe_sede_legale'] = 'Sede Legale';
$lang['fe_indirizzo'] = 'Indirizzo';
$lang['fe_cap'] = 'CAP';
$lang['fe_comune'] = 'Comune';
$lang['fe_provincia'] = 'Provincia';
$lang['fe_nazione'] = 'Nazione';
$lang['fe_telefono'] = 'Telefono';
$lang['fe_email'] = 'Email';
$lang['fe_dati_rea'] = 'Dati REA';
$lang['fe_rea_ufficio'] = 'Ufficio REA';
$lang['fe_rea_numero'] = 'Numero REA';
$lang['fe_capitale_sociale'] = 'Capitale Sociale';
$lang['fe_socio_unico'] = 'Socio Unico';
$lang['fe_stato_liquidazione'] = 'Stato Liquidazione';

// Provider
$lang['fe_provider'] = 'Provider';
$lang['fe_ambiente'] = 'Ambiente';
$lang['fe_ambiente_test'] = 'Test / Sandbox';
$lang['fe_ambiente_produzione'] = 'Produzione';
$lang['fe_api_endpoint'] = 'Endpoint API';
$lang['fe_api_username'] = 'Username';
$lang['fe_api_password'] = 'Password';
$lang['fe_api_key'] = 'API Key';
$lang['fe_api_secret'] = 'API Secret';
$lang['fe_provider_test'] = 'Modalità Test (Locale)';
$lang['fe_provider_custom'] = 'API Personalizzata';
$lang['fe_provider_test_help'] = 'In modalità test, le fatture vengono salvate localmente senza essere inviate allo SDI. Utile per lo sviluppo e i test.';
$lang['fe_provider_aruba_help'] = 'Per utilizzare Aruba, inserisci le credenziali del servizio Fatturazione Elettronica Aruba.';
$lang['fe_provider_infocert_help'] = 'Per utilizzare InfoCert, inserisci l\'API Key fornita dal servizio Legalinvoice.';
$lang['fe_provider_fattureincloud_help'] = 'Per utilizzare Fatture in Cloud, inserisci l\'API Key e il Secret dall\'area sviluppatori.';
$lang['fe_provider_custom_help'] = 'Inserisci l\'endpoint della tua API personalizzata e le credenziali di autenticazione.';
$lang['fe_connection_ok'] = 'Connessione riuscita';
$lang['fe_connection_failed'] = 'Connessione fallita';

// Opzioni
$lang['fe_opzioni_generazione'] = 'Opzioni Generazione';
$lang['fe_auto_generate_xml'] = 'Genera automaticamente XML';
$lang['fe_auto_generate_xml_help'] = 'Genera automaticamente l\'XML della fattura elettronica quando viene creata una nuova fattura';
$lang['fe_auto_send'] = 'Invio automatico';
$lang['fe_auto_send_help'] = 'Invia automaticamente la fattura allo SDI dopo la generazione dell\'XML';
$lang['fe_bollo_virtuale'] = 'Bollo Virtuale';
$lang['fe_bollo_soglia'] = 'Soglia Bollo';
$lang['fe_bollo_soglia_help'] = 'Importo oltre il quale applicare il bollo virtuale (default: 77.47€)';
$lang['fe_bollo_importo'] = 'Importo Bollo';
$lang['fe_default_pagamento'] = 'Pagamento Predefinito';
$lang['fe_modalita_pagamento'] = 'Modalità Pagamento';
$lang['fe_condizioni_pagamento'] = 'Condizioni Pagamento';
$lang['fe_notifiche'] = 'Notifiche';
$lang['fe_email_notifiche'] = 'Invia notifiche email per gli esiti SDI';

// Webhook
$lang['fe_webhook_config'] = 'Configurazione Webhook';
$lang['fe_webhook_help'] = 'Configura il webhook per ricevere le notifiche SDI in tempo reale. Inserisci l\'URL del webhook nel pannello del tuo provider SDI.';
$lang['fe_webhook_enabled'] = 'Abilita Webhook';
$lang['fe_webhook_url'] = 'URL Webhook';
$lang['fe_webhook_secret'] = 'Secret Webhook';
$lang['fe_webhook_secret_help'] = 'Questo secret viene usato per validare le richieste webhook. Configuralo anche nel tuo provider SDI se supportato.';
$lang['fe_copied'] = 'Copiato!';

// Campi cliente
$lang['fe_dati_fatturazione_elettronica'] = 'Dati Fatturazione Elettronica';
$lang['fe_split_payment'] = 'Split Payment';
$lang['fe_split_payment_help'] = 'Abilita per clienti PA soggetti a split payment';

// Modal import
$lang['fe_seleziona_fattura'] = 'Seleziona Fattura';
$lang['fe_seleziona'] = 'Seleziona...';
$lang['fe_fatture_non_importate'] = 'Mostra solo le fatture non ancora importate';
$lang['fe_tipo_documento'] = 'Tipo Documento';
$lang['fe_genera_xml'] = 'Genera XML';
$lang['fe_caricamento'] = 'Caricamento...';
$lang['fe_no_fatture_importabili'] = 'Nessuna fattura da importare';

// Messaggi
$lang['fe_fattura_not_found'] = 'Fattura non trovata';
$lang['fe_invoice_id_required'] = 'ID fattura richiesto';
$lang['fe_error_generating_xml'] = 'Errore durante la generazione dell\'XML';
$lang['fe_xml_generated'] = 'XML generato con successo';
$lang['fe_error_sending'] = 'Errore durante l\'invio della fattura';
$lang['fe_invoice_sent'] = 'Fattura inviata con successo';
$lang['fe_cannot_delete'] = 'Impossibile eliminare la fattura';
$lang['fe_deleted'] = 'Fattura eliminata con successo';
$lang['fe_error_regenerating'] = 'Errore durante la rigenerazione dell\'XML';
$lang['fe_xml_regenerated'] = 'XML rigenerato con successo';
$lang['fe_no_selection'] = 'Nessuna fattura selezionata';
$lang['fe_bulk_send_result'] = 'Inviate: %s, Fallite: %s';
$lang['fe_sync_result'] = 'Sincronizzate %d nuove fatture passive';
$lang['fe_error_creating_expense'] = 'Errore durante la creazione della spesa';
$lang['fe_expense_created'] = 'Spesa creata con successo';
$lang['fe_archived'] = 'Fattura archiviata';

// Notifiche email
$lang['fe_notifica_email_subject'] = 'Fattura Elettronica: %s';
$lang['fe_notifica_email_body'] = 'La fattura %s ha cambiato stato: %s';
$lang['fe_notifica_email_subject_consegnata'] = 'Fattura Elettronica Consegnata';
$lang['fe_notifica_email_subject_accettata'] = 'Fattura Elettronica Accettata';
$lang['fe_notifica_email_subject_rifiutata'] = 'Fattura Elettronica Rifiutata';
$lang['fe_notifica_email_subject_scartata'] = 'Fattura Elettronica Scartata';

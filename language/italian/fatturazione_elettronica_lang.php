<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * File di lingua italiano per il modulo Fatturazione Elettronica
 */

// Menu e titoli
$lang['fe_menu_title'] = 'Fatt. Elettronica';
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
$lang['fe_applica_filtri'] = 'Applica filtri';
$lang['fe_reset_filtri'] = 'Rimuovi filtri';

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

// Setup Wizard
$lang['fe_setup_wizard'] = 'Configurazione Fatturazione Elettronica';
$lang['fe_setup_wizard_title'] = 'Configura la Fatturazione Elettronica';
$lang['fe_setup_wizard_subtitle'] = 'Segui i passaggi per configurare il modulo di fatturazione elettronica';
$lang['fe_step_provider'] = 'Provider';
$lang['fe_step_credentials'] = 'Credenziali';
$lang['fe_step_company'] = 'Azienda';
$lang['fe_select_provider'] = 'Seleziona il tuo provider di fatturazione elettronica';
$lang['fe_select'] = 'Seleziona';
$lang['fe_selected'] = 'Selezionato';
$lang['fe_skip_setup'] = 'Salta configurazione';
$lang['fe_configure_credentials'] = 'Inserisci le credenziali per connetterti al servizio';
$lang['fe_requirements'] = 'Requisiti';
$lang['fe_back'] = 'Indietro';
$lang['fe_continue'] = 'Continua';
$lang['fe_complete_setup'] = 'Completa Configurazione';
$lang['fe_company_data'] = 'Dati Aziendali';
$lang['fe_file_uploaded'] = 'File caricato';
$lang['fe_credentials_saved'] = 'Credenziali salvate con successo';
$lang['fe_setup_complete'] = 'Configurazione completata con successo!';

// OAuth
$lang['fe_oauth_status'] = 'Stato Connessione OAuth';
$lang['fe_oauth_connected'] = 'Account collegato';
$lang['fe_oauth_not_connected'] = 'Account non collegato';
$lang['fe_oauth_instructions'] = 'Inserisci le credenziali OAuth dell\'applicazione creata nel portale sviluppatori';
$lang['fe_connect_account'] = 'Collega Account';
$lang['fe_disconnect_account'] = 'Scollega Account';
$lang['fe_confirm_disconnect'] = 'Sei sicuro di voler scollegare l\'account?';
$lang['fe_oauth_success'] = 'Account collegato con successo!';
$lang['fe_oauth_error'] = 'Errore durante l\'autenticazione OAuth';
$lang['fe_oauth_denied'] = 'Autorizzazione negata';
$lang['fe_oauth_no_code'] = 'Codice di autorizzazione mancante';
$lang['fe_oauth_disconnected'] = 'Account scollegato con successo';

// Provider Settings
$lang['fe_change_provider'] = 'Cambia Provider';
$lang['fe_provider_credentials'] = 'Credenziali Provider';

// Dashboard migliorata
$lang['fe_last_update'] = 'Ultimo aggiornamento';
$lang['fe_welcome_title'] = 'Benvenuto in Fatturazione Elettronica';
$lang['fe_welcome_message'] = 'Completa la configurazione iniziale per iniziare a inviare e ricevere fatture elettroniche.';
$lang['fe_start_setup'] = 'Avvia Configurazione';
$lang['fe_provider_configured'] = 'Provider Configurato';
$lang['fe_provider_not_configured'] = 'Configurazione Incompleta';
$lang['fe_andamento_mensile'] = 'Andamento Mensile';
$lang['fe_fatture_inviate'] = 'Fatture Inviate';
$lang['fe_fatture_ricevute'] = 'Fatture Ricevute';
$lang['fe_invia_tutte'] = 'Invia Tutte';
$lang['fe_verifica_stati'] = 'Verifica Stati';
$lang['fe_stati_aggiornati'] = 'Aggiornati %d stati fatture';
$lang['fe_passive_non_lette'] = 'Fatture Passive Non Lette';

// Log labels
$lang['fe_log_invio_fattura'] = 'Fattura Inviata';
$lang['fe_log_invio_test'] = 'Invio Test';
$lang['fe_log_consegnata'] = 'Fattura Consegnata';
$lang['fe_log_accettata'] = 'Fattura Accettata';
$lang['fe_log_rifiutata'] = 'Fattura Rifiutata';
$lang['fe_log_scartata'] = 'Fattura Scartata';
$lang['fe_log_errore'] = 'Errore';
$lang['fe_log_errore_invio'] = 'Errore Invio';
$lang['fe_log_generazione'] = 'XML Generato';
$lang['fe_log_sync'] = 'Sincronizzazione';
$lang['fe_log_download'] = 'Download';
$lang['fe_log_webhook'] = 'Webhook';
$lang['fe_log_cron'] = 'Cron Job';
$lang['fe_log_config'] = 'Configurazione';

// Validazione
$lang['fe_piva_non_valida'] = 'Partita IVA non valida';
$lang['fe_cf_non_valido'] = 'Codice Fiscale non valido';
$lang['fe_codice_dest_non_valido'] = 'Codice Destinatario non valido';
$lang['fe_iban_non_valido'] = 'IBAN non valido';
$lang['fe_pec_non_valida'] = 'PEC non valida';
$lang['fe_xml_non_valido'] = 'XML non valido';
$lang['fe_xml_errori'] = 'Errori nella validazione XML';

// Cron
$lang['fe_cron_eseguito'] = 'Cron job eseguito con successo';
$lang['fe_cron_errore'] = 'Errore durante l\'esecuzione del cron job';

// Pulsante nella pagina fattura Perfex
$lang['fe_fatturazione_elettronica'] = 'Fatturazione Elettronica';
$lang['fe_fattura_non_generata'] = 'La fattura elettronica non è ancora stata generata per questa fattura.';
$lang['fe_genera_fattura_elettronica'] = 'Genera Fattura Elettronica';
$lang['fe_vedi_dettagli'] = 'Vedi Dettagli';

// Tooltip Dashboard
$lang['fe_totale_fatture_attive_tooltip'] = 'Numero totale di fatture elettroniche generate';
$lang['fe_consegnate_tooltip'] = 'Fatture consegnate con successo al destinatario';
$lang['fe_in_attesa_tooltip'] = 'Fatture inviate in attesa di conferma SDI';
$lang['fe_con_errori_tooltip'] = 'Fatture scartate o con errori da correggere';
$lang['fe_ambiente_test_tooltip'] = 'Le fatture non vengono inviate realmente allo SDI';
$lang['fe_test_connessione_tooltip'] = 'Verifica la connessione con il provider SDI';
$lang['fe_change_provider_tooltip'] = 'Modifica il provider o le credenziali SDI';
$lang['fe_chart_not_available'] = 'Grafico non disponibile';
$lang['fe_sincronizza_passive_tooltip'] = 'Scarica le nuove fatture passive dal provider SDI';
$lang['fe_salva_impostazioni_tooltip'] = 'Salva tutte le impostazioni del modulo';

// Tooltip Azioni Dashboard
$lang['fe_genera_fattura_tooltip'] = 'Importa una fattura da Perfex e genera l\'XML per la fatturazione elettronica';
$lang['fe_sincronizza_passive_btn_tooltip'] = 'Controlla e scarica le nuove fatture passive dal provider SDI';
$lang['fe_vedi_tutte_tooltip'] = 'Visualizza l\'elenco completo delle fatture';
$lang['fe_vedi_dettaglio_tooltip'] = 'Apri il dettaglio completo di questa fattura';
$lang['fe_vai_impostazioni_tooltip'] = 'Configura i dati aziendali e il provider SDI';
$lang['fe_log_recenti_tooltip'] = 'Ultimi eventi registrati dal sistema';
$lang['fe_da_inviare_tooltip'] = 'Fatture generate ma non ancora inviate allo SDI';
$lang['fe_invia_tutte_tooltip'] = 'Invia tutte le fatture in attesa allo SDI in un\'unica operazione';
$lang['fe_verifica_stati_tooltip'] = 'Aggiorna lo stato di tutte le fatture inviate controllando gli esiti SDI';

// Tooltip Fatture Passive
$lang['fe_passive_non_lette_tooltip'] = 'Fatture ricevute dai fornitori non ancora visualizzate';
$lang['fe_solo_non_lette_tooltip'] = 'Mostra solo le fatture che non hai ancora aperto';
$lang['fe_collegata_tooltip'] = 'Questa fattura è stata collegata a una spesa in Perfex';
$lang['fe_archiviata_tooltip'] = 'Fattura archiviata manualmente senza creare una spesa';
$lang['fe_da_processare_tooltip'] = 'Fattura da elaborare: puoi crearci una spesa o archiviarla';
$lang['fe_crea_spesa_tooltip'] = 'Crea automaticamente una spesa in Perfex basata su questa fattura';
$lang['fe_archivia_tooltip'] = 'Archivia la fattura senza creare una spesa';

// Tooltip Fatture Attive
$lang['fe_nome_file_tooltip'] = 'Nome del file XML generato secondo il formato SDI';
$lang['fe_tipo_tooltip'] = 'Tipo di documento: Fattura (TD01), Nota di Credito (TD04), ecc.';
$lang['fe_id_sdi_tooltip'] = 'Identificativo univoco assegnato dallo SDI dopo l\'invio';
$lang['fe_stato_tooltip'] = 'Stato attuale della fattura nel flusso SDI';
$lang['fe_data_creazione_tooltip'] = 'Data in cui è stato generato l\'XML della fattura';
$lang['fe_data_invio_tooltip'] = 'Data e ora dell\'ultimo tentativo di invio allo SDI';
$lang['fe_importa_fattura_tooltip'] = 'Seleziona una fattura Perfex per generare l\'XML elettronico';
$lang['fe_visualizza_tooltip'] = 'Apri il dettaglio completo della fattura elettronica';
$lang['fe_download_xml_tooltip'] = 'Scarica il file XML della fattura elettronica';
$lang['fe_invia_sdi_tooltip'] = 'Invia questa fattura al Sistema di Interscambio';
$lang['fe_rigenera_xml_tooltip'] = 'Rigenera l\'XML con i dati aggiornati della fattura Perfex';
$lang['fe_verifica_stato_tooltip'] = 'Controlla lo stato attuale della fattura presso lo SDI';
$lang['fe_elimina_tooltip'] = 'Elimina questa fattura elettronica (solo se non ancora inviata)';
$lang['fe_invia_selezionate_tooltip'] = 'Invia tutte le fatture selezionate allo SDI';

// Tooltip Stati Fattura
$lang['fe_stato_bozza_tooltip'] = 'XML non ancora generato';
$lang['fe_stato_generata_tooltip'] = 'XML generato, pronto per l\'invio allo SDI';
$lang['fe_stato_inviata_tooltip'] = 'Fattura inviata, in attesa di risposta dallo SDI';
$lang['fe_stato_consegnata_tooltip'] = 'Fattura recapitata con successo al destinatario';
$lang['fe_stato_non_consegnata_tooltip'] = 'SDI non è riuscito a consegnare al destinatario, disponibile nell\'area riservata';
$lang['fe_stato_accettata_tooltip'] = 'Il destinatario ha accettato la fattura';
$lang['fe_stato_rifiutata_tooltip'] = 'Il destinatario ha rifiutato la fattura';
$lang['fe_stato_scartata_tooltip'] = 'SDI ha scartato la fattura per errori formali, da correggere e reinviare';
$lang['fe_stato_decorrenza_termini_tooltip'] = 'Trascorsi 15 giorni senza risposta, fattura considerata accettata';
$lang['fe_stato_mancata_consegna_tooltip'] = 'Impossibile recapitare al destinatario';

// Tooltip Impostazioni - Dati Azienda
$lang['fe_denominazione_tooltip'] = 'Ragione sociale o nome dell\'azienda come risulta alla Camera di Commercio';
$lang['fe_partita_iva_tooltip'] = 'Partita IVA italiana a 11 cifre (senza prefisso IT)';
$lang['fe_codice_fiscale_tooltip'] = 'Codice fiscale dell\'azienda (può coincidere con la P.IVA per le società)';
$lang['fe_regime_fiscale_tooltip'] = 'Regime fiscale dell\'azienda secondo la normativa italiana';
$lang['fe_codice_destinatario_tooltip'] = 'Codice SDI a 7 caratteri per ricevere le fatture passive';
$lang['fe_pec_tooltip'] = 'PEC aziendale per la ricezione delle fatture se non si usa il codice destinatario';
$lang['fe_indirizzo_tooltip'] = 'Indirizzo della sede legale (via/piazza e numero civico)';
$lang['fe_cap_tooltip'] = 'Codice di Avviamento Postale a 5 cifre';
$lang['fe_comune_tooltip'] = 'Comune della sede legale';
$lang['fe_provincia_tooltip'] = 'Sigla provincia a 2 lettere (es: RM, MI, NA)';
$lang['fe_nazione_tooltip'] = 'Codice nazione ISO a 2 lettere (IT per Italia)';
$lang['fe_telefono_tooltip'] = 'Numero di telefono aziendale (opzionale)';
$lang['fe_email_tooltip'] = 'Email aziendale di contatto (opzionale)';

// Tooltip Impostazioni - Dati REA
$lang['fe_dati_rea_tooltip'] = 'Dati del Registro delle Imprese (obbligatori per le società)';
$lang['fe_rea_ufficio_tooltip'] = 'Sigla della provincia della Camera di Commercio';
$lang['fe_rea_numero_tooltip'] = 'Numero di iscrizione al Registro delle Imprese';
$lang['fe_capitale_sociale_tooltip'] = 'Capitale sociale in euro (es: 10000.00)';
$lang['fe_socio_unico_tooltip'] = 'Indica se la società ha un socio unico o più soci';
$lang['fe_stato_liquidazione_tooltip'] = 'Indica se la società è in stato di liquidazione';

// Tooltip Impostazioni - Provider
$lang['fe_provider_tooltip'] = 'Servizio esterno utilizzato per l\'invio e la ricezione delle fatture elettroniche';
$lang['fe_ambiente_tooltip'] = 'Test per simulazioni senza invio reale, Produzione per invii effettivi';
$lang['fe_api_endpoint_tooltip'] = 'URL dell\'API del provider per l\'invio delle fatture';
$lang['fe_api_username_tooltip'] = 'Nome utente per l\'autenticazione al servizio';
$lang['fe_api_password_tooltip'] = 'Password per l\'autenticazione al servizio';
$lang['fe_api_key_tooltip'] = 'Chiave API fornita dal provider';
$lang['fe_api_secret_tooltip'] = 'Secret API per la firma delle richieste';

// Tooltip Impostazioni - Opzioni
$lang['fe_opzioni_generazione_tooltip'] = 'Configura come vengono generate e inviate automaticamente le fatture elettroniche';
$lang['fe_auto_generate_xml_tooltip'] = 'Se attivo, l\'XML viene generato automaticamente quando crei una fattura in Perfex';
$lang['fe_auto_send_tooltip'] = 'Se attivo, la fattura viene inviata automaticamente allo SDI dopo la generazione';
$lang['fe_bollo_virtuale_tooltip'] = 'Configurazione del bollo virtuale per fatture esenti IVA oltre una certa soglia';
$lang['fe_bollo_soglia_tooltip'] = 'Importo oltre il quale applicare il bollo virtuale di 2€ (default 77.47€)';
$lang['fe_bollo_importo_tooltip'] = 'Importo del bollo virtuale (normalmente 2.00€)';
$lang['fe_default_pagamento_tooltip'] = 'Modalità e condizioni di pagamento predefinite per le nuove fatture';
$lang['fe_modalita_pagamento_tooltip'] = 'Modalità di pagamento predefinita per le nuove fatture';
$lang['fe_condizioni_pagamento_tooltip'] = 'Condizioni di pagamento predefinite (pagamento completo, rate, ecc.)';
$lang['fe_notifiche_tooltip'] = 'Configura le notifiche email per gli eventi relativi alle fatture elettroniche';
$lang['fe_email_notifiche_tooltip'] = 'Ricevi un\'email quando cambia lo stato di una fattura (consegnata, scartata, ecc.)';

// Tooltip Impostazioni - Webhook
$lang['fe_webhook_config_tooltip'] = 'Il webhook permette di ricevere automaticamente le notifiche dallo SDI in tempo reale';
$lang['fe_webhook_enabled_tooltip'] = 'Abilita la ricezione automatica delle notifiche SDI tramite webhook';
$lang['fe_webhook_url_tooltip'] = 'Copia questo URL nel pannello del tuo provider SDI per ricevere le notifiche';
$lang['fe_webhook_url_help'] = 'Configura questo URL nel pannello del tuo provider per ricevere le notifiche automaticamente';
$lang['fe_webhook_secret_tooltip'] = 'Chiave segreta per validare l\'autenticità delle richieste webhook';
$lang['fe_copia_url'] = 'Copia URL webhook';
$lang['fe_copia_secret'] = 'Copia secret webhook';
$lang['fe_copy_tooltip'] = 'Copia negli appunti';

// Tooltip Filtri
$lang['fe_da_data_tooltip'] = 'Mostra solo le fatture dalla data selezionata in poi';
$lang['fe_a_data_tooltip'] = 'Mostra solo le fatture fino alla data selezionata';
$lang['fe_tutti_stati_tooltip'] = 'Seleziona uno stato per filtrare le fatture';

// Tooltip Tabella
$lang['fe_checkbox_seleziona_tooltip'] = 'Seleziona questa fattura per le azioni di gruppo';
$lang['fe_checkbox_seleziona_tutti_tooltip'] = 'Seleziona/deseleziona tutte le fatture visibili';
$lang['fe_ordina_tooltip'] = 'Clicca per ordinare per questa colonna';

// Tooltip Dettaglio Fattura
$lang['fe_progressivo_tooltip'] = 'Numero progressivo univoco della fattura elettronica';
$lang['fe_data_esito_tooltip'] = 'Data in cui lo SDI ha comunicato l\'esito';
$lang['fe_tentativi_invio_tooltip'] = 'Numero di tentativi di invio effettuati';
$lang['fe_ultimo_errore_tooltip'] = 'Ultimo messaggio di errore ricevuto dallo SDI';
$lang['fe_esito_sdi_tooltip'] = 'Codice esito restituito dal Sistema di Interscambio';
$lang['fe_fattura_collegata_tooltip'] = 'Fattura Perfex da cui è stata generata questa fattura elettronica';
$lang['fe_anteprima_xml_tooltip'] = 'Visualizza il contenuto del file XML formattato';
$lang['fe_notifiche_sdi_tooltip'] = 'Storico delle notifiche ricevute dallo SDI';
$lang['fe_storico_tooltip'] = 'Cronologia di tutti gli eventi relativi a questa fattura';
$lang['fe_torna_lista_tooltip'] = 'Torna all\'elenco delle fatture';

// Tooltip OAuth
$lang['fe_oauth_status_tooltip'] = 'Stato della connessione OAuth con il provider';
$lang['fe_connect_account_tooltip'] = 'Avvia il processo di autenticazione OAuth';
$lang['fe_disconnect_account_tooltip'] = 'Scollega l\'account e rimuovi le credenziali salvate';

// Tooltip Setup Wizard
$lang['fe_setup_wizard_title_tooltip'] = 'Procedura guidata per configurare il modulo di fatturazione elettronica';
$lang['fe_step_provider_tooltip'] = 'Scegli il servizio che gestirà l\'invio e la ricezione delle fatture';
$lang['fe_step_credentials_tooltip'] = 'Inserisci le credenziali di accesso al servizio selezionato';
$lang['fe_step_company_tooltip'] = 'Configura i dati della tua azienda per le fatture elettroniche';
$lang['fe_select_provider_tooltip'] = 'Clicca su un provider per selezionarlo e proseguire';
$lang['fe_skip_setup_tooltip'] = 'Salta la configurazione guidata e vai alla dashboard';
$lang['fe_provider_card_tooltip'] = 'Clicca per selezionare questo provider';
$lang['fe_back_tooltip'] = 'Torna al passaggio precedente';
$lang['fe_continue_tooltip'] = 'Prosegui al passaggio successivo';
$lang['fe_complete_setup_tooltip'] = 'Salva i dati e completa la configurazione';
$lang['fe_client_id_tooltip'] = 'ID dell\'applicazione fornito dal provider nel portale sviluppatori';
$lang['fe_client_secret_tooltip'] = 'Chiave segreta dell\'applicazione fornita dal provider';

// Documentazione
$lang['fe_documentazione'] = 'Documentazione';
$lang['fe_doc_indice'] = 'Indice';
$lang['fe_doc_introduzione'] = 'Introduzione';
$lang['fe_doc_requisiti'] = 'Requisiti';
$lang['fe_doc_configurazione'] = 'Configurazione';
$lang['fe_doc_providers'] = 'Provider SDI';
$lang['fe_doc_fatture_attive'] = 'Fatture Attive (Vendita)';
$lang['fe_doc_fatture_passive'] = 'Fatture Passive (Acquisto)';
$lang['fe_doc_webhook'] = 'Webhook';
$lang['fe_doc_stati'] = 'Stati Fattura';
$lang['fe_doc_faq'] = 'Domande Frequenti';

// Introduzione
$lang['fe_doc_intro_text'] = 'Il modulo Fatturazione Elettronica per Perfex CRM consente di gestire l\'intero ciclo di fatturazione elettronica italiana, dalla generazione degli XML FatturaPA all\'invio tramite SDI, fino alla ricezione delle fatture passive dai fornitori.';
$lang['fe_doc_funzionalita'] = 'Funzionalita principali';
$lang['fe_doc_func_1'] = 'Generazione automatica XML FatturaPA conformi alle specifiche tecniche';
$lang['fe_doc_func_2'] = 'Invio fatture allo SDI tramite provider certificati';
$lang['fe_doc_func_3'] = 'Ricezione e gestione delle notifiche SDI (consegna, scarto, accettazione)';
$lang['fe_doc_func_4'] = 'Sincronizzazione delle fatture passive ricevute dai fornitori';
$lang['fe_doc_func_5'] = 'Creazione automatica di spese dalle fatture passive';
$lang['fe_doc_func_6'] = 'Webhook per notifiche in tempo reale';

// Requisiti
$lang['fe_doc_requisiti_text'] = 'Prima di poter utilizzare il modulo, assicurati di soddisfare i seguenti requisiti:';
$lang['fe_doc_requisito'] = 'Requisito';
$lang['fe_doc_valore'] = 'Valore';
$lang['fe_doc_provider_account'] = 'Account Provider';
$lang['fe_doc_provider_account_desc'] = 'Account attivo con uno dei provider supportati';

// Configurazione
$lang['fe_doc_config_text'] = 'La configurazione del modulo si articola in tre passaggi principali:';
$lang['fe_doc_step_1'] = '1. Selezione del Provider';
$lang['fe_doc_step_1_text'] = 'Seleziona il provider SDI che utilizzerai per l\'invio e la ricezione delle fatture elettroniche. Ogni provider ha caratteristiche e prezzi differenti.';
$lang['fe_doc_step_2'] = '2. Configurazione Credenziali';
$lang['fe_doc_step_2_text'] = 'Inserisci le credenziali di accesso fornite dal provider. A seconda del provider, potrebbero essere necessarie API Key, OAuth credentials o certificati.';
$lang['fe_doc_step_3'] = '3. Dati Aziendali';
$lang['fe_doc_step_3_text'] = 'Configura i dati della tua azienda che verranno utilizzati per la generazione delle fatture elettroniche: denominazione, partita IVA, indirizzo, regime fiscale, ecc.';
$lang['fe_doc_importante'] = 'Importante';
$lang['fe_doc_config_warning'] = 'Assicurati che i dati aziendali siano corretti e corrispondano a quelli registrati presso la Camera di Commercio. Errori nei dati possono causare lo scarto delle fatture da parte dello SDI.';

// Provider
$lang['fe_doc_providers_text'] = 'Il modulo supporta diversi provider per l\'invio delle fatture elettroniche. Ogni provider offre funzionalita e prezzi differenti:';
$lang['fe_doc_attivo'] = 'Attivo';

// Fatture Attive
$lang['fe_doc_fatture_attive_text'] = 'Le fatture attive sono le fatture che emetti ai tuoi clienti. Il modulo consente di generare l\'XML FatturaPA a partire dalle fatture create in Perfex CRM e di inviarle allo SDI.';
$lang['fe_doc_generazione_xml'] = 'Generazione XML';
$lang['fe_doc_gen_step_1'] = 'Dalla dashboard, clicca su "Genera Fattura" e seleziona la fattura Perfex da importare';
$lang['fe_doc_gen_step_2'] = 'Il sistema generer automaticamente l\'XML FatturaPA conforme alle specifiche tecniche';
$lang['fe_doc_gen_step_3'] = 'La fattura appare nella lista "Fatture Attive" pronta per essere inviata';
$lang['fe_doc_invio_sdi'] = 'Invio allo SDI';
$lang['fe_doc_invio_sdi_text'] = 'Una volta generato l\'XML, puoi inviare la fattura allo SDI cliccando sul pulsante "Invia allo SDI". Il sistema trasmetter la fattura al provider selezionato che la inoltrer al Sistema di Interscambio.';
$lang['fe_doc_automazione'] = 'Automazione';
$lang['fe_doc_automazione_text'] = 'Puoi attivare la generazione e l\'invio automatico dalle impostazioni. In questo modo, ogni nuova fattura creata in Perfex verr automaticamente convertita in XML e inviata allo SDI.';

// Fatture Passive
$lang['fe_doc_fatture_passive_text'] = 'Le fatture passive sono le fatture che ricevi dai tuoi fornitori. Il modulo le scarica automaticamente dal provider SDI e le mette a disposizione per l\'elaborazione.';
$lang['fe_doc_sincronizzazione'] = 'Sincronizzazione';
$lang['fe_doc_sincronizzazione_text'] = 'Clicca su "Sincronizza Passive" per scaricare le nuove fatture ricevute. Se hai configurato il webhook, le fatture verranno ricevute automaticamente in tempo reale.';
$lang['fe_doc_elaborazione'] = 'Elaborazione';
$lang['fe_doc_crea_spesa_desc'] = 'Crea automaticamente una spesa in Perfex con i dati della fattura ricevuta';
$lang['fe_doc_archivia_desc'] = 'Archivia la fattura senza creare una spesa (utile per fatture gi registrate manualmente)';

// Webhook
$lang['fe_doc_webhook_text'] = 'Il webhook consente di ricevere le notifiche SDI in tempo reale, senza dover effettuare la sincronizzazione manuale.';
$lang['fe_doc_webhook_config'] = 'Configurazione Webhook';
$lang['fe_doc_webhook_step_1'] = 'Copia l\'URL webhook mostrato qui sotto';
$lang['fe_doc_webhook_step_2'] = 'Accedi al pannello del tuo provider SDI';
$lang['fe_doc_webhook_step_3'] = 'Incolla l\'URL nella sezione configurazione webhook/notifiche del provider';

// Stati
$lang['fe_doc_stati_text'] = 'Ogni fattura elettronica passa attraverso diversi stati durante il suo ciclo di vita:';

// FAQ
$lang['fe_doc_faq_1_q'] = 'Quanto tempo impiega una fattura ad essere consegnata?';
$lang['fe_doc_faq_1_a'] = 'Normalmente lo SDI elabora e consegna le fatture entro pochi minuti. In alcuni casi possono essere necessarie fino a 5 giorni lavorativi, specialmente per le fatture verso la Pubblica Amministrazione.';
$lang['fe_doc_faq_2_q'] = 'Cosa fare se una fattura viene scartata?';
$lang['fe_doc_faq_2_a'] = 'Verifica l\'errore riportato nei dettagli della fattura, correggi il problema nella fattura Perfex originale, poi usa "Rigenera XML" e invia nuovamente. Gli errori piu comuni riguardano partita IVA errata, codice destinatario non valido o dati mancanti.';
$lang['fe_doc_faq_3_q'] = 'Posso modificare una fattura gi inviata?';
$lang['fe_doc_faq_3_a'] = 'No, una volta inviata una fattura allo SDI non puo essere modificata. Se hai bisogno di correggere una fattura gi consegnata, devi emettere una nota di credito.';
$lang['fe_doc_faq_4_q'] = 'Come posso testare il modulo prima di usarlo in produzione?';
$lang['fe_doc_faq_4_a'] = 'Seleziona il provider "Modalita Test" nelle impostazioni. In questa modalita le fatture vengono salvate localmente senza essere realmente inviate allo SDI.';
$lang['fe_doc_faq_5_q'] = 'Le fatture passive vengono scaricate automaticamente?';
$lang['fe_doc_faq_5_a'] = 'Se hai configurato il webhook, le fatture passive vengono ricevute automaticamente quando arrivano. Altrimenti, devi cliccare su "Sincronizza Passive" per scaricarle manualmente.';

// Supporto
$lang['fe_doc_supporto'] = 'Hai bisogno di aiuto?';
$lang['fe_doc_supporto_text'] = 'Se hai domande o problemi con il modulo, il nostro team di supporto e a tua disposizione per aiutarti.';
$lang['fe_doc_contatta_supporto'] = 'Contatta il Supporto';
$lang['fe_doc_email_supporto'] = 'Email Supporto';
$lang['fe_doc_sito_web'] = 'Sito Web';
$lang['fe_doc_sviluppato_da'] = 'Sviluppato con passione in Italia';
$lang['fe_doc_visita_sito'] = 'Visita il Sito';

// Documentazione - Vantaggi
$lang['fe_doc_vantaggi'] = 'Vantaggi';
$lang['fe_doc_vantaggio_1'] = 'Risparmio di tempo: automatizza la generazione e l\'invio delle fatture';
$lang['fe_doc_vantaggio_2'] = 'Conformita garantita: XML sempre validi secondo le specifiche SDI';
$lang['fe_doc_vantaggio_3'] = 'Monitoraggio in tempo reale: traccia lo stato di ogni fattura';
$lang['fe_doc_vantaggio_4'] = 'Integrazione nativa: si integra perfettamente con Perfex CRM';

// Documentazione - Quick Start
$lang['fe_doc_quickstart'] = 'Guida Rapida';
$lang['fe_doc_quickstart_text'] = 'Segui questi 4 semplici passaggi per iniziare ad utilizzare la fatturazione elettronica:';
$lang['fe_doc_qs_step_1_title'] = 'Scegli Provider';
$lang['fe_doc_qs_step_1_desc'] = 'Seleziona il servizio SDI che preferisci utilizzare';
$lang['fe_doc_qs_step_2_title'] = 'Configura';
$lang['fe_doc_qs_step_2_desc'] = 'Inserisci le credenziali e i dati aziendali';
$lang['fe_doc_qs_step_3_title'] = 'Importa';
$lang['fe_doc_qs_step_3_desc'] = 'Importa le fatture Perfex e genera gli XML';
$lang['fe_doc_qs_step_4_title'] = 'Invia';
$lang['fe_doc_qs_step_4_desc'] = 'Invia le fatture allo SDI con un click';

// Documentazione - Requisiti Note
$lang['fe_doc_requisiti_note'] = 'Note importanti';
$lang['fe_doc_requisiti_note_1'] = 'Assicurati che il server abbia accesso a internet per comunicare con lo SDI';
$lang['fe_doc_requisiti_note_2'] = 'I certificati SSL devono essere aggiornati per le connessioni sicure';
$lang['fe_doc_requisiti_note_3'] = 'Verifica che il firewall non blocchi le connessioni in uscita verso i provider';

// Documentazione - Clienti
$lang['fe_doc_clienti'] = 'Configurazione Clienti';
$lang['fe_doc_clienti_text'] = 'Per inviare correttamente le fatture elettroniche, e necessario configurare alcuni campi specifici per ogni cliente nella scheda cliente di Perfex CRM.';
$lang['fe_doc_clienti_campi'] = 'Campi Fatturazione Elettronica';
$lang['fe_doc_cliente_cod_dest'] = 'Codice SDI a 7 caratteri del cliente';
$lang['fe_doc_cliente_pec'] = 'PEC del cliente (alternativa al codice destinatario)';
$lang['fe_doc_cliente_split'] = 'Abilita per clienti PA soggetti a split payment';
$lang['fe_doc_clienti_nota'] = 'Nota';
$lang['fe_doc_clienti_nota_text'] = 'Se il cliente non ha ne codice destinatario ne PEC, usa il codice generico "0000000" e la fattura sara disponibile nel cassetto fiscale del cliente.';

// Documentazione - Azioni Fatture
$lang['fe_doc_azioni_disponibili'] = 'Azioni disponibili';
$lang['fe_doc_azione_download'] = 'Scarica il file XML';
$lang['fe_doc_azione_invia'] = 'Trasmetti allo SDI';
$lang['fe_doc_azione_rigenera'] = 'Ricrea l\'XML con i dati aggiornati';
$lang['fe_doc_azione_verifica'] = 'Controlla lo stato presso lo SDI';
$lang['fe_doc_azione_elimina'] = 'Rimuovi la fattura (solo se non inviata)';

// Documentazione - Stati
$lang['fe_doc_flusso_stati'] = 'Flusso degli stati';
$lang['fe_doc_azione_richiesta'] = 'Azione';
$lang['fe_doc_genera'] = 'Genera XML';
$lang['fe_doc_invia'] = 'Invia';
$lang['fe_doc_attendi'] = 'Attendi';
$lang['fe_doc_contatta_cliente'] = 'Contatta cliente';
$lang['fe_doc_correggi'] = 'Correggi e reinvia';

// Documentazione - Troubleshooting
$lang['fe_doc_troubleshooting'] = 'Risoluzione Problemi';
$lang['fe_doc_troubleshooting_text'] = 'Ecco le soluzioni ai problemi piu comuni che potresti incontrare:';
$lang['fe_doc_ts_1_title'] = 'La fattura viene scartata dallo SDI';
$lang['fe_doc_ts_1_text'] = 'Lo scarto puo avvenire per diversi motivi. Verifica il messaggio di errore nei dettagli della fattura.';
$lang['fe_doc_ts_1_sol_1'] = 'Controlla che la Partita IVA del cliente sia corretta e attiva';
$lang['fe_doc_ts_1_sol_2'] = 'Verifica che il Codice Destinatario sia valido (7 caratteri) o la PEC sia corretta';
$lang['fe_doc_ts_1_sol_3'] = 'Assicurati che tutti i campi obbligatori della fattura siano compilati';
$lang['fe_doc_ts_2_title'] = 'Errore di connessione al provider';
$lang['fe_doc_ts_2_text'] = 'Se non riesci a connetterti al provider SDI, potrebbe essere un problema di rete o di credenziali.';
$lang['fe_doc_ts_2_sol_1'] = 'Verifica le credenziali nelle impostazioni del provider';
$lang['fe_doc_ts_2_sol_2'] = 'Controlla che il server possa accedere a internet e che il firewall non blocchi le connessioni';
$lang['fe_doc_ts_3_title'] = 'Le fatture passive non si sincronizzano';
$lang['fe_doc_ts_3_text'] = 'Se le fatture passive non vengono scaricate, verifica la configurazione del webhook o esegui una sincronizzazione manuale.';
$lang['fe_doc_ts_3_sol_1'] = 'Clicca su "Sincronizza Passive" per scaricare manualmente le fatture';
$lang['fe_doc_ts_3_sol_2'] = 'Verifica che il webhook sia configurato correttamente nel pannello del provider';

// Documentazione - Extra
$lang['fe_doc_copia'] = 'Copia';

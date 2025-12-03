# Modulo Fatturazione Elettronica SDI per Perfex CRM

Modulo completo per la fatturazione elettronica italiana tramite Sistema di Interscambio (SDI) dell'Agenzia delle Entrate.

## Caratteristiche

- **Fatture Attive (Vendita)**
  - Generazione automatica XML FatturaPA conforme alle specifiche 1.2.2
  - Supporto per tutti i tipi documento (TD01-TD27)
  - Invio tramite provider SDI (Aruba, InfoCert, Fatture in Cloud, API custom)
  - Tracciamento stato fatture (inviata, consegnata, accettata, rifiutata, ecc.)
  - Gestione notifiche SDI (RC, NS, MC, EC, DT, AT)

- **Fatture Passive (Acquisto)**
  - Ricezione automatica fatture dai provider
  - Parsing e visualizzazione dettagliata
  - Creazione spese collegate
  - Archiviazione e gestione

- **Note di Credito**
  - Supporto completo per TD04
  - Collegamento alla fattura originale

- **Dashboard**
  - Statistiche in tempo reale
  - Log operazioni
  - Azioni rapide

## Requisiti

- Perfex CRM 3.2, 3.3 o 3.4
- PHP 8.3 o 8.4
- Estensione PHP: DOM, libxml, curl

## Installazione

1. Copiare la cartella `modules/fatturazione_elettronica` nella directory `modules` di Perfex CRM
2. Andare in **Setup → Modules** e attivare il modulo
3. Configurare le impostazioni in **Fatturazione Elettronica → Impostazioni**

## Configurazione

### Dati Azienda

Configurare i dati dell'azienda richiesti per la fatturazione elettronica:

- Denominazione
- Partita IVA (11 caratteri)
- Codice Fiscale (opzionale, 16 caratteri)
- Regime Fiscale (RF01-RF19)
- Sede legale (indirizzo, CAP, comune, provincia)
- Dati REA (opzionale)

### Provider SDI

Il modulo supporta diversi provider per l'invio delle fatture:

| Provider | Credenziali Richieste |
|----------|----------------------|
| **Aruba** | Username e Password del servizio Fatturazione Elettronica |
| **InfoCert** | API Key del servizio Legalinvoice |
| **Fatture in Cloud** | API Key e Secret dall'area sviluppatori |
| **API Custom** | Endpoint, Username, Password, API Key |
| **Test** | Nessuna (salva localmente senza invio) |

### Campi Cliente

Per ogni cliente è possibile configurare:

- **Codice Destinatario**: Codice SDI a 7 caratteri (B2B/B2C) o 6 caratteri (PA)
- **PEC**: Indirizzo PEC per la ricezione fatture
- **Codice Fiscale**: Se diverso dalla Partita IVA
- **Split Payment**: Per clienti PA soggetti a scissione dei pagamenti

## Utilizzo

### Generazione Fattura Elettronica

1. Andare in **Fatturazione Elettronica → Fatture Attive**
2. Cliccare **Importa Fattura**
3. Selezionare la fattura Perfex da importare
4. Selezionare il tipo documento
5. Cliccare **Genera XML**

### Invio allo SDI

1. Dalla lista fatture attive, cliccare l'icona invio (aeroplano)
2. Oppure dalla pagina dettaglio, cliccare **Invia allo SDI**
3. Lo stato verrà aggiornato automaticamente

### Fatture Passive

1. Andare in **Fatturazione Elettronica → Fatture Passive**
2. Cliccare **Sincronizza** per scaricare le nuove fatture
3. Visualizzare i dettagli cliccando sulla fattura
4. Creare una spesa collegata o archiviare

## Struttura File

```
modules/fatturazione_elettronica/
├── assets/
│   ├── css/
│   │   └── fatturazione_elettronica.css
│   └── js/
│       └── fatturazione_elettronica.js
├── controllers/
│   └── Fatturazione_elettronica.php
├── helpers/
│   └── fatturazione_elettronica_helper.php
├── language/
│   ├── english/
│   │   └── fatturazione_elettronica_lang.php
│   └── italian/
│       └── fatturazione_elettronica_lang.php
├── libraries/
│   ├── FatturaPA_Generator.php
│   ├── FatturaPA_Parser.php
│   └── Sdi_client.php
├── models/
│   └── Fatturazione_elettronica_model.php
├── views/
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── fatture_attive/
│   │   │   ├── index.php
│   │   │   └── view.php
│   │   ├── fatture_passive/
│   │   │   ├── index.php
│   │   │   └── view.php
│   │   └── impostazioni/
│   │       └── index.php
│   └── partials/
│       └── customer_fields.php
├── fatturazione_elettronica.php
├── install.php
└── uninstall.php
```

## Database

Il modulo crea le seguenti tabelle:

- `tblfe_fatture_attive`: Fatture di vendita
- `tblfe_fatture_passive`: Fatture di acquisto
- `tblfe_notifiche`: Storico notifiche SDI
- `tblfe_progressivo`: Contatore progressivo invio
- `tblfe_log`: Log operazioni

## Webhook

Per ricevere notifiche SDI in tempo reale, configurare il seguente URL nel pannello del provider:

```
https://tuodominio.com/fatturazione_elettronica/webhook
```

## Cron Job

Il modulo utilizza il cron job di Perfex CRM per:

- Controllare lo stato delle fatture inviate
- Scaricare le fatture passive
- Aggiornare le notifiche

## Tipi Documento Supportati

| Codice | Descrizione |
|--------|-------------|
| TD01 | Fattura |
| TD02 | Acconto/Anticipo su fattura |
| TD03 | Acconto/Anticipo su parcella |
| TD04 | Nota di Credito |
| TD05 | Nota di Debito |
| TD06 | Parcella |
| TD16 | Integrazione reverse charge interno |
| TD17-TD27 | Altri tipi documento |

## Regimi Fiscali

| Codice | Descrizione |
|--------|-------------|
| RF01 | Ordinario |
| RF02 | Contribuenti minimi |
| RF04 | Agricoltura |
| RF19 | Forfettario |
| ... | Altri regimi |

## Licenza

Questo modulo è distribuito sotto licenza proprietaria.

## Supporto

Per supporto tecnico, contattare:
- Email: support@gtechgroup.it
- GitHub Issues: https://github.com/gtechgroupit/GTG-Fatturazione-Elettronica/issues

## Changelog

### v1.0.0
- Prima release pubblica
- Supporto completo fatturazione elettronica B2B/B2C/PA
- Integrazione provider: Aruba, InfoCert, Fatture in Cloud
- Gestione fatture attive e passive
- Dashboard con statistiche
- Compatibilità Perfex CRM 3.2, 3.3, 3.4
- Compatibilità PHP 8.3, 8.4

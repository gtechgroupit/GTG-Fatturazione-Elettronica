/**
 * Fatturazione Elettronica SDI - JavaScript
 * GTech Group IT
 */

(function($) {
    'use strict';

    // Namespace globale
    window.FE = window.FE || {};

    /**
     * Inizializzazione
     */
    FE.init = function() {
        FE.initTooltips();
        FE.initTables();
        FE.initForms();
        FE.initCustomerFields();
    };

    /**
     * Inizializza i tooltip
     */
    FE.initTooltips = function() {
        $('[data-toggle="tooltip"]').tooltip();
    };

    /**
     * Inizializza le tabelle DataTable
     */
    FE.initTables = function() {
        // Le tabelle sono gestite da Perfex CRM
    };

    /**
     * Inizializza i form
     */
    FE.initForms = function() {
        // Validazione codice fiscale
        $('input[name="fe_codice_fiscale"]').on('blur', function() {
            var value = $(this).val().toUpperCase();
            $(this).val(value);

            if (value.length > 0 && !FE.validateCodiceFiscale(value)) {
                $(this).closest('.form-group').addClass('has-error');
            } else {
                $(this).closest('.form-group').removeClass('has-error');
            }
        });

        // Validazione partita IVA
        $('input[name="fe_partita_iva"]').on('blur', function() {
            var value = $(this).val().replace(/\D/g, '');
            $(this).val(value);

            if (value.length > 0 && !FE.validatePartitaIVA(value)) {
                $(this).closest('.form-group').addClass('has-error');
            } else {
                $(this).closest('.form-group').removeClass('has-error');
            }
        });

        // Uppercase per codice destinatario
        $('input[name="fe_codice_destinatario"]').on('blur', function() {
            var value = $(this).val().toUpperCase();
            $(this).val(value);
        });
    };

    /**
     * Inizializza i campi cliente
     */
    FE.initCustomerFields = function() {
        // Salva i campi FE prima del submit del form cliente
        $('form#client-form, form[action*="clients"]').on('submit', function() {
            // I campi vengono gestiti dal controller
        });
    };

    /**
     * Valida il codice fiscale italiano
     */
    FE.validateCodiceFiscale = function(cf) {
        cf = cf.toUpperCase().trim();

        if (cf.length === 11) {
            // Potrebbe essere una P.IVA usata come CF
            return FE.validatePartitaIVA(cf);
        }

        if (cf.length !== 16) {
            return false;
        }

        var pattern = /^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/;
        if (!pattern.test(cf)) {
            return false;
        }

        var set1 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var set2 = 'ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var even = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var odd = 'BAKPLCQDREVOSFTGUHMINJWZYX';
        var sum = 0;

        for (var i = 0; i < 15; i++) {
            var c = cf.charAt(i);
            var pos = set1.indexOf(c);
            if (pos >= 0) {
                c = set2.charAt(pos);
            }
            if (i % 2 === 0) {
                pos = even.indexOf(c);
                sum += odd.indexOf(even.charAt(pos));
            } else {
                sum += even.indexOf(c);
            }
        }

        var check = even.charAt(sum % 26);
        return cf.charAt(15) === check;
    };

    /**
     * Valida la partita IVA italiana
     */
    FE.validatePartitaIVA = function(piva) {
        piva = piva.replace(/\D/g, '');

        if (piva.length !== 11) {
            return false;
        }

        var sum = 0;
        for (var i = 0; i < 11; i++) {
            var digit = parseInt(piva.charAt(i), 10);
            if (i % 2 === 0) {
                sum += digit;
            } else {
                var double = digit * 2;
                sum += (double > 9) ? double - 9 : double;
            }
        }

        return sum % 10 === 0;
    };

    /**
     * Mostra un messaggio di caricamento
     */
    FE.showLoading = function(container) {
        var $container = $(container);
        $container.html('<div class="text-center"><div class="fe-loading"></div><p class="text-muted mtop10">Caricamento...</p></div>');
    };

    /**
     * Invia una fattura allo SDI
     */
    FE.sendInvoice = function(id, callback) {
        if (!confirm('Sei sicuro di voler inviare questa fattura allo SDI?')) {
            return;
        }

        $.ajax({
            url: admin_url + 'fatturazione_elettronica/invia_fattura/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message || 'Fattura inviata con successo');
                    if (typeof callback === 'function') {
                        callback(response);
                    }
                } else {
                    alert_float('danger', response.message || 'Errore durante l\'invio');
                }
            },
            error: function() {
                alert_float('danger', 'Errore di comunicazione con il server');
            }
        });
    };

    /**
     * Controlla lo stato di una fattura
     */
    FE.checkStatus = function(id, callback) {
        $.ajax({
            url: admin_url + 'fatturazione_elettronica/ajax_check_status/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function() {
                alert_float('danger', 'Errore durante il controllo dello stato');
            }
        });
    };

    /**
     * Scarica un file XML
     */
    FE.downloadXML = function(id, type) {
        type = type || 'attiva';
        var url = admin_url + 'fatturazione_elettronica/download_xml';
        if (type === 'passiva') {
            url = admin_url + 'fatturazione_elettronica/download_xml_passiva';
        }
        window.location.href = url + '/' + id;
    };

    /**
     * Copia testo negli appunti
     */
    FE.copyToClipboard = function(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                alert_float('success', 'Copiato negli appunti');
            }).catch(function() {
                FE.fallbackCopyToClipboard(text);
            });
        } else {
            FE.fallbackCopyToClipboard(text);
        }
    };

    FE.fallbackCopyToClipboard = function(text) {
        var textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert_float('success', 'Copiato negli appunti');
        } catch (err) {
            alert_float('danger', 'Impossibile copiare');
        }
        document.body.removeChild(textArea);
    };

    /**
     * Formatta un importo in valuta
     */
    FE.formatCurrency = function(amount, currency) {
        currency = currency || 'EUR';
        return new Intl.NumberFormat('it-IT', {
            style: 'currency',
            currency: currency
        }).format(amount);
    };

    /**
     * Formatta una data
     */
    FE.formatDate = function(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('it-IT');
    };

    /**
     * Formatta data e ora
     */
    FE.formatDateTime = function(dateString) {
        var date = new Date(dateString);
        return date.toLocaleString('it-IT');
    };

    // =========================================================================
    // INTEGRAZIONE PAGINA PAGAMENTI
    // =========================================================================

    /**
     * Inizializza l'integrazione con la pagina pagamenti
     */
    FE.initPaymentsPage = function() {
        // Verifica se siamo nella pagina pagamenti
        if (window.location.href.indexOf('admin/payments') === -1) {
            return;
        }

        // Aspetta che la tabella sia caricata
        FE.waitForPaymentsTable();
    };

    /**
     * Attende che la tabella pagamenti sia caricata
     */
    FE.waitForPaymentsTable = function() {
        var $table = $('.table-payments, table.dataTable');

        if ($table.length === 0) {
            setTimeout(FE.waitForPaymentsTable, 500);
            return;
        }

        // La tabella esiste, aggiungi la colonna FE
        FE.enhancePaymentsTable($table);

        // Ascolta eventi di ridisegno della tabella (paginazione, filtri)
        $table.on('draw.dt', function() {
            FE.updatePaymentsTableFEStatus();
        });
    };

    /**
     * Migliora la tabella pagamenti con info FE
     */
    FE.enhancePaymentsTable = function($table) {
        // Aggiungi header colonna FE se non esiste
        var $thead = $table.find('thead tr');
        if ($thead.find('.fe-header').length === 0) {
            // Trova la colonna delle azioni (ultima)
            var $lastTh = $thead.find('th').last();
            $('<th class="fe-header text-center" style="width: 100px;">E-Fattura</th>').insertBefore($lastTh);
        }

        // Aggiorna lo stato FE per tutte le righe
        FE.updatePaymentsTableFEStatus();
    };

    /**
     * Aggiorna lo stato FE per tutte le righe della tabella pagamenti
     */
    FE.updatePaymentsTableFEStatus = function() {
        var $table = $('.table-payments, table.dataTable');
        var $rows = $table.find('tbody tr');
        var invoiceIds = [];
        var rowMap = {};

        // Raccogli tutti gli invoice_id dalle righe
        $rows.each(function() {
            var $row = $(this);

            // Salta se la cella FE esiste già
            if ($row.find('.fe-status-cell').length > 0) {
                return;
            }

            // Cerca il link alla fattura nella riga
            var $invoiceLink = $row.find('a[href*="invoices/list_invoices/"]');
            if ($invoiceLink.length === 0) {
                // Aggiungi cella vuota
                var $lastTd = $row.find('td').last();
                $('<td class="fe-status-cell text-center">-</td>').insertBefore($lastTd);
                return;
            }

            // Estrai l'invoice_id dal link
            var href = $invoiceLink.attr('href');
            var match = href.match(/invoices\/list_invoices\/(\d+)/);
            if (!match) {
                var $lastTd = $row.find('td').last();
                $('<td class="fe-status-cell text-center">-</td>').insertBefore($lastTd);
                return;
            }

            var invoiceId = match[1];
            invoiceIds.push(invoiceId);
            rowMap[invoiceId] = $row;

            // Aggiungi cella con loading
            var $lastTd = $row.find('td').last();
            $('<td class="fe-status-cell text-center" data-invoice-id="' + invoiceId + '"><i class="fa fa-spinner fa-spin"></i></td>').insertBefore($lastTd);
        });

        // Se non ci sono invoice da verificare, esci
        if (invoiceIds.length === 0) {
            return;
        }

        // Chiamata bulk per ottenere lo stato FE di tutte le fatture
        $.ajax({
            url: admin_url + 'fatturazione_elettronica/ajax_get_fe_status_bulk',
            type: 'POST',
            data: {
                invoice_ids: invoiceIds,
                csrf_token_name: typeof csrfData !== 'undefined' ? csrfData.token : ''
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('FE Error:', response.error);
                    return;
                }

                // Aggiorna ogni cella con lo stato
                $.each(response.results, function(invoiceId, data) {
                    var $cell = $('td.fe-status-cell[data-invoice-id="' + invoiceId + '"]');
                    if ($cell.length === 0) return;

                    var html = FE.buildFEStatusHtml(data, invoiceId);
                    $cell.html(html);
                });

                // Reinizializza i tooltip
                $('[data-toggle="tooltip"]').tooltip();
            },
            error: function() {
                // In caso di errore, mostra icona di errore
                $('td.fe-status-cell').each(function() {
                    if ($(this).find('.fa-spinner').length > 0) {
                        $(this).html('<i class="fa fa-exclamation-circle text-danger" data-toggle="tooltip" title="Errore caricamento"></i>');
                    }
                });
            }
        });
    };

    /**
     * Costruisce l'HTML per lo stato FE
     */
    FE.buildFEStatusHtml = function(data, invoiceId) {
        if (!data.exists) {
            // Fattura elettronica non generata - mostra pulsante per generare
            return '<a href="' + data.generate_url + '" class="btn btn-xs btn-info" data-toggle="tooltip" title="Genera fattura elettronica">' +
                   '<i class="fa fa-file-code"></i>' +
                   '</a>';
        }

        var html = '';

        // Mostra lo stato
        html += '<span class="label label-' + data.class + '" data-toggle="tooltip" title="' + data.label + '">';
        html += data.label;
        html += '</span> ';

        // Pulsanti azione
        html += '<div class="btn-group btn-group-xs mtop5">';

        // Pulsante visualizza
        html += '<a href="' + data.view_url + '" class="btn btn-default" data-toggle="tooltip" title="Visualizza dettagli">';
        html += '<i class="fa fa-eye"></i>';
        html += '</a>';

        // Se può essere inviata, mostra pulsante invio
        if (data.can_send) {
            html += '<a href="' + data.send_url + '" class="btn btn-primary" data-toggle="tooltip" title="Invia al SDI" onclick="return confirm(\'Inviare la fattura al SDI?\');">';
            html += '<i class="fa fa-paper-plane"></i>';
            html += '</a>';
        }

        html += '</div>';

        return html;
    };

    // Inizializza quando il documento è pronto
    $(document).ready(function() {
        FE.init();
        FE.initPaymentsPage();
    });

})(jQuery);

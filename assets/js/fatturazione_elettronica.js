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
    // INTEGRAZIONE PAGINE FATTURE E PAGAMENTI
    // =========================================================================

    /**
     * Rileva la pagina corrente
     */
    FE.getCurrentPage = function() {
        var url = window.location.href;
        if (url.indexOf('admin/invoices') !== -1 && url.indexOf('list_invoices') === -1) {
            return 'invoices';
        }
        if (url.indexOf('admin/payments') !== -1) {
            return 'payments';
        }
        return null;
    };

    /**
     * Inizializza l'integrazione FE nelle pagine fatture e pagamenti
     */
    FE.initTableIntegration = function() {
        var page = FE.getCurrentPage();
        if (!page) {
            return;
        }

        console.log('FE: Inizializzazione integrazione per pagina:', page);

        // Attendi che DataTables sia pronto
        FE.waitForDataTable(page);
    };

    /**
     * Attende che DataTables sia inizializzato
     */
    FE.waitForDataTable = function(page) {
        var checkInterval = setInterval(function() {
            var $tables = $('table.dataTable, .table-invoices, .table-payments, table.dt-table');

            if ($tables.length > 0 && $tables.find('tbody tr').length > 0) {
                clearInterval(checkInterval);
                console.log('FE: Tabella trovata, aggiungo pulsanti FE');

                // Prima esecuzione
                FE.addFEButtons(page);

                // Ascolta eventi DataTables per paginazione/filtri
                $(document).on('draw.dt', function() {
                    setTimeout(function() {
                        FE.addFEButtons(page);
                    }, 100);
                });

                // Ascolta anche MutationObserver per tabelle dinamiche
                FE.observeTableChanges(page);
            }
        }, 500);

        // Timeout dopo 10 secondi
        setTimeout(function() {
            clearInterval(checkInterval);
        }, 10000);
    };

    /**
     * Osserva cambiamenti nella tabella (per AJAX loading)
     */
    FE.observeTableChanges = function(page) {
        var $tbody = $('table.dataTable tbody, .table-invoices tbody, .table-payments tbody');
        if ($tbody.length === 0) return;

        var observer = new MutationObserver(function(mutations) {
            FE.addFEButtons(page);
        });

        observer.observe($tbody[0], {
            childList: true,
            subtree: true
        });
    };

    /**
     * Aggiunge i pulsanti FE alle righe della tabella
     */
    FE.addFEButtons = function(page) {
        var $rows = $('table.dataTable tbody tr, .table-invoices tbody tr, .table-payments tbody tr');
        var invoiceIds = [];
        var rowsToProcess = [];

        $rows.each(function() {
            var $row = $(this);

            // Salta se già processata
            if ($row.data('fe-processed')) {
                return;
            }

            // Estrai invoice_id
            var invoiceId = FE.extractInvoiceId($row, page);
            if (!invoiceId) {
                return;
            }

            invoiceIds.push(invoiceId);
            rowsToProcess.push({
                $row: $row,
                invoiceId: invoiceId
            });

            // Marca come in elaborazione
            $row.data('fe-processed', true);
        });

        if (invoiceIds.length === 0) {
            return;
        }

        console.log('FE: Carico stato per', invoiceIds.length, 'fatture');

        // Chiamata AJAX bulk
        $.ajax({
            url: admin_url + 'fatturazione_elettronica/ajax_get_fe_status_bulk',
            type: 'POST',
            data: {
                invoice_ids: invoiceIds
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('FE Error:', response.error);
                    return;
                }

                // Aggiorna ogni riga
                $.each(rowsToProcess, function(i, item) {
                    var data = response.results[item.invoiceId];
                    if (data) {
                        FE.insertFEButton(item.$row, data, item.invoiceId, page);
                    }
                });

                // Reinizializza tooltip
                $('[data-toggle="tooltip"]').tooltip();
            },
            error: function(xhr, status, error) {
                console.error('FE AJAX Error:', error);
                // Rimuovi flag per permettere retry
                $.each(rowsToProcess, function(i, item) {
                    item.$row.data('fe-processed', false);
                });
            }
        });
    };

    /**
     * Estrae l'invoice_id da una riga della tabella
     */
    FE.extractInvoiceId = function($row, page) {
        var invoiceId = null;

        // Metodo 1: Cerca data attribute
        invoiceId = $row.data('invoice-id') || $row.data('id');
        if (invoiceId) return invoiceId;

        // Metodo 2: Cerca nel link alla fattura
        var $links = $row.find('a[href*="invoices"]');
        $links.each(function() {
            var href = $(this).attr('href');

            // Pattern: invoices/list_invoices/ID o invoice/ID
            var match = href.match(/invoices?\/(?:list_invoices\/)?(\d+)/);
            if (match) {
                invoiceId = match[1];
                return false; // break
            }
        });

        if (invoiceId) return invoiceId;

        // Metodo 3: Per pagina fatture, cerca nella prima colonna (checkbox o ID)
        if (page === 'invoices') {
            var $checkbox = $row.find('input[type="checkbox"][name*="ids"]');
            if ($checkbox.length) {
                invoiceId = $checkbox.val();
            }
        }

        // Metodo 4: Cerca qualsiasi link con numero
        if (!invoiceId) {
            $row.find('a').each(function() {
                var href = $(this).attr('href') || '';
                var match = href.match(/\/(\d+)(?:\?|$|#)/);
                if (match && parseInt(match[1]) > 0) {
                    // Verifica che sia un link a fattura
                    if (href.indexOf('invoice') !== -1) {
                        invoiceId = match[1];
                        return false;
                    }
                }
            });
        }

        return invoiceId;
    };

    /**
     * Inserisce il pulsante FE nella riga
     */
    FE.insertFEButton = function($row, data, invoiceId, page) {
        // Trova la cella delle azioni (di solito l'ultima con pulsanti)
        var $actionsCell = $row.find('td').filter(function() {
            return $(this).find('.btn, a.btn').length > 0;
        }).last();

        // Se non trova una cella con azioni, usa l'ultima
        if ($actionsCell.length === 0) {
            $actionsCell = $row.find('td').last();
        }

        // Verifica che non sia già stato aggiunto
        if ($actionsCell.find('.fe-btn-group').length > 0) {
            return;
        }

        // Costruisci il pulsante
        var $btnGroup = $('<div class="fe-btn-group btn-group btn-group-xs" style="margin-left: 5px; display: inline-block;"></div>');

        if (!data.exists) {
            // FE non generata - pulsante genera
            $btnGroup.append(
                '<a href="' + data.generate_url + '" class="btn btn-info" data-toggle="tooltip" title="Genera Fattura Elettronica">' +
                '<i class="fa fa-file-invoice"></i>' +
                '</a>'
            );
        } else {
            // FE esiste - mostra stato e azioni
            if (data.can_send) {
                // Può essere inviata
                $btnGroup.append(
                    '<a href="' + data.send_url + '" class="btn btn-success" data-toggle="tooltip" title="Invia al SDI - Stato: ' + data.label + '" onclick="return confirm(\'Inviare la fattura elettronica al SDI?\');">' +
                    '<i class="fa fa-paper-plane"></i>' +
                    '</a>'
                );
            } else {
                // Già inviata - mostra solo visualizza
                var btnClass = 'btn-default';
                if (data.stato === 'consegnata' || data.stato === 'accettata') {
                    btnClass = 'btn-success';
                } else if (data.stato === 'scartata' || data.stato === 'rifiutata') {
                    btnClass = 'btn-danger';
                } else if (data.stato === 'inviata') {
                    btnClass = 'btn-warning';
                }

                $btnGroup.append(
                    '<a href="' + data.view_url + '" class="btn ' + btnClass + '" data-toggle="tooltip" title="FE: ' + data.label + (data.id_sdi ? ' (SDI: ' + data.id_sdi + ')' : '') + '">' +
                    '<i class="fa fa-file-invoice"></i>' +
                    '</a>'
                );
            }
        }

        // Inserisci il pulsante
        var $existingBtnGroup = $actionsCell.find('.btn-group').last();
        if ($existingBtnGroup.length > 0) {
            $existingBtnGroup.after($btnGroup);
        } else {
            $actionsCell.append($btnGroup);
        }
    };

    // Inizializza quando il documento è pronto
    $(document).ready(function() {
        FE.init();
        FE.initTableIntegration();
    });

})(jQuery);

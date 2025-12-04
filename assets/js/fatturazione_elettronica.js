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
    // INTEGRAZIONE PAGINE FATTURE E PAGAMENTI PERFEX CRM
    // =========================================================================

    /**
     * Rileva la pagina corrente di Perfex CRM
     */
    FE.getCurrentPage = function() {
        var url = window.location.href;
        var path = window.location.pathname;

        // Debug
        console.log('FE: URL corrente:', url);
        console.log('FE: Path corrente:', path);

        // Pagina lista fatture: admin/invoices (ma non la vista singola admin/invoices/list_invoices/ID)
        if ((path.indexOf('/invoices') !== -1 || url.indexOf('admin/invoices') !== -1) &&
            url.indexOf('list_invoices/') === -1 &&
            url.indexOf('invoice/') === -1) {
            console.log('FE: Rilevata pagina INVOICES');
            return 'invoices';
        }

        // Pagina pagamenti
        if (path.indexOf('/payments') !== -1 || url.indexOf('admin/payments') !== -1) {
            console.log('FE: Rilevata pagina PAYMENTS');
            return 'payments';
        }

        console.log('FE: Pagina non rilevata per integrazione');
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

        // Attendi che la tabella sia pronta
        FE.waitForTable(page);
    };

    /**
     * Attende che la tabella sia caricata
     */
    FE.waitForTable = function(page, attempts) {
        attempts = attempts || 0;
        var maxAttempts = 30; // 15 secondi max

        // Cerca le tabelle in vari modi
        var $table = $('table.dataTable').first();
        if ($table.length === 0) {
            $table = $('.table-invoices, .table-payments, .dt-table, table[id*="DataTables"]').first();
        }
        if ($table.length === 0) {
            $table = $('table').filter(function() {
                return $(this).find('tbody tr').length > 0;
            }).first();
        }

        var hasRows = $table.length > 0 && $table.find('tbody tr').length > 0;

        console.log('FE: Tentativo', attempts + 1, '- Tabella trovata:', $table.length > 0, '- Righe:', $table.find('tbody tr').length);

        if (hasRows) {
            console.log('FE: Tabella pronta, aggiungo pulsanti FE');
            FE.processTable($table, page);
            return;
        }

        if (attempts < maxAttempts) {
            setTimeout(function() {
                FE.waitForTable(page, attempts + 1);
            }, 500);
        } else {
            console.log('FE: Timeout - tabella non trovata');
        }
    };

    /**
     * Processa la tabella e aggiunge i listener
     */
    FE.processTable = function($table, page) {
        // Prima esecuzione
        FE.addFEButtonsToTable($table, page);

        // Listener per DataTables (paginazione, ordinamento, filtri)
        $table.on('draw.dt', function() {
            console.log('FE: Evento draw.dt rilevato');
            setTimeout(function() {
                FE.addFEButtonsToTable($table, page);
            }, 200);
        });

        // Listener globale per DataTables
        $(document).on('draw.dt', function(e, settings) {
            console.log('FE: Evento draw.dt globale rilevato');
            setTimeout(function() {
                FE.addFEButtonsToTable($table, page);
            }, 200);
        });

        // MutationObserver come fallback
        var $tbody = $table.find('tbody');
        if ($tbody.length > 0) {
            var observer = new MutationObserver(function(mutations) {
                console.log('FE: Mutation observer triggered');
                FE.addFEButtonsToTable($table, page);
            });

            observer.observe($tbody[0], {
                childList: true,
                subtree: false
            });
        }
    };

    /**
     * Aggiunge i pulsanti FE alle righe della tabella
     */
    FE.addFEButtonsToTable = function($table, page) {
        var $rows = $table.find('tbody tr');
        var invoiceIds = [];
        var rowsToProcess = [];

        console.log('FE: Analisi', $rows.length, 'righe');

        $rows.each(function() {
            var $row = $(this);

            // Salta righe vuote o già processate
            if ($row.find('td').length === 0) return;
            if ($row.hasClass('fe-processed')) return;
            if ($row.find('.fe-btn-group').length > 0) return;

            // Estrai invoice_id
            var invoiceId = FE.extractInvoiceIdFromRow($row, page);

            if (invoiceId) {
                invoiceIds.push(invoiceId);
                rowsToProcess.push({
                    $row: $row,
                    invoiceId: invoiceId
                });
                $row.addClass('fe-processed');
            }
        });

        if (invoiceIds.length === 0) {
            console.log('FE: Nessuna nuova fattura da processare');
            return;
        }

        console.log('FE: Caricamento stato per', invoiceIds.length, 'fatture:', invoiceIds);

        // Prepara dati AJAX con CSRF token per Perfex CRM
        var ajaxData = {
            invoice_ids: invoiceIds
        };

        // Aggiungi CSRF token se disponibile
        if (typeof csrfData !== 'undefined' && csrfData.token_name && csrfData.token) {
            ajaxData[csrfData.token_name] = csrfData.token;
        }

        // Chiamata AJAX
        $.ajax({
            url: admin_url + 'fatturazione_elettronica/ajax_get_fe_status_bulk',
            type: 'POST',
            data: ajaxData,
            dataType: 'json',
            success: function(response) {
                console.log('FE: Risposta AJAX:', response);

                if (response.error) {
                    console.error('FE: Errore API:', response.error);
                    return;
                }

                if (!response.results) {
                    console.error('FE: Risposta senza results');
                    return;
                }

                // Inserisci pulsanti per ogni riga
                $.each(rowsToProcess, function(i, item) {
                    var data = response.results[item.invoiceId];
                    if (data) {
                        FE.insertFEButtonInRow(item.$row, data, item.invoiceId);
                    }
                });

                // Reinizializza tooltip Bootstrap
                if (typeof $.fn.tooltip !== 'undefined') {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            },
            error: function(xhr, status, error) {
                console.error('FE: Errore AJAX:', status, error);
                console.error('FE: Response:', xhr.responseText);

                // Rimuovi flag per permettere retry
                $.each(rowsToProcess, function(i, item) {
                    item.$row.removeClass('fe-processed');
                });
            }
        });
    };

    /**
     * Estrae l'invoice_id da una riga della tabella
     */
    FE.extractInvoiceIdFromRow = function($row, page) {
        var invoiceId = null;

        // Metodo 1: Data attribute sulla riga
        invoiceId = $row.attr('data-id') || $row.data('id') || $row.data('invoice-id');
        if (invoiceId) {
            console.log('FE: ID trovato da data attribute:', invoiceId);
            return invoiceId;
        }

        // Metodo 2: Checkbox di selezione (comune in Perfex)
        var $checkbox = $row.find('input[type="checkbox"]').first();
        if ($checkbox.length) {
            invoiceId = $checkbox.val();
            if (invoiceId && parseInt(invoiceId) > 0) {
                console.log('FE: ID trovato da checkbox:', invoiceId);
                return invoiceId;
            }
        }

        // Metodo 3: Link alla fattura
        $row.find('a').each(function() {
            var href = $(this).attr('href') || '';

            // Pattern Perfex: invoices/list_invoices/ID
            var match = href.match(/invoices\/list_invoices\/(\d+)/);
            if (match) {
                invoiceId = match[1];
                console.log('FE: ID trovato da link list_invoices:', invoiceId);
                return false;
            }

            // Pattern alternativo: invoice/ID
            match = href.match(/invoice\/(\d+)/);
            if (match) {
                invoiceId = match[1];
                console.log('FE: ID trovato da link invoice:', invoiceId);
                return false;
            }
        });

        if (invoiceId) return invoiceId;

        // Metodo 4: Prima cella con numero
        var firstCellText = $row.find('td').first().text().trim();
        if (/^\d+$/.test(firstCellText)) {
            invoiceId = firstCellText;
            console.log('FE: ID trovato da prima cella:', invoiceId);
            return invoiceId;
        }

        return null;
    };

    /**
     * Inserisce il pulsante FE nella riga
     */
    FE.insertFEButtonInRow = function($row, data, invoiceId) {
        // Trova la cella delle azioni (ultima con pulsanti o ultima cella)
        var $actionsCell = null;

        // Cerca cella con pulsanti
        $row.find('td').each(function() {
            if ($(this).find('a.btn, button.btn, .btn-group').length > 0) {
                $actionsCell = $(this);
            }
        });

        // Fallback: ultima cella
        if (!$actionsCell || $actionsCell.length === 0) {
            $actionsCell = $row.find('td').last();
        }

        if ($actionsCell.length === 0) {
            console.log('FE: Nessuna cella azioni trovata per invoice', invoiceId);
            return;
        }

        // Verifica che non sia già presente
        if ($actionsCell.find('.fe-btn-group').length > 0) {
            return;
        }

        // Costruisci HTML del pulsante
        var btnHtml = '<div class="fe-btn-group btn-group btn-group-xs" style="margin-left:3px;display:inline-block;">';

        if (!data.exists) {
            // FE non generata - pulsante per generare
            btnHtml += '<a href="' + data.generate_url + '" class="btn btn-info btn-xs" data-toggle="tooltip" title="Genera Fattura Elettronica">';
            btnHtml += '<i class="fa fa-file-invoice"></i>';
            btnHtml += '</a>';
        } else if (data.can_send) {
            // FE generata/scartata - pulsante per inviare
            btnHtml += '<a href="' + data.send_url + '" class="btn btn-success btn-xs" data-toggle="tooltip" title="Invia al SDI (' + data.label + ')" onclick="return confirm(\'Inviare la fattura elettronica al SDI?\');">';
            btnHtml += '<i class="fa fa-paper-plane"></i>';
            btnHtml += '</a>';
        } else {
            // FE già inviata - mostra stato
            var btnClass = 'btn-default';
            if (data.stato === 'consegnata' || data.stato === 'accettata') {
                btnClass = 'btn-success';
            } else if (data.stato === 'scartata' || data.stato === 'rifiutata') {
                btnClass = 'btn-danger';
            } else if (data.stato === 'inviata' || data.stato === 'pending') {
                btnClass = 'btn-warning';
            }

            var tooltip = 'FE: ' + data.label;
            if (data.id_sdi) {
                tooltip += ' (SDI: ' + data.id_sdi + ')';
            }

            btnHtml += '<a href="' + data.view_url + '" class="btn ' + btnClass + ' btn-xs" data-toggle="tooltip" title="' + tooltip + '">';
            btnHtml += '<i class="fa fa-file-invoice"></i>';
            btnHtml += '</a>';
        }

        btnHtml += '</div>';

        // Inserisci il pulsante
        $actionsCell.append(btnHtml);

        console.log('FE: Pulsante aggiunto per invoice', invoiceId, '- Stato:', data.exists ? data.stato : 'non generata');
    };

    // Inizializza quando il documento è pronto
    $(document).ready(function() {
        FE.init();

        // Inizializza integrazione tabelle dopo un breve delay
        // per assicurarsi che Perfex CRM abbia caricato tutto
        setTimeout(function() {
            FE.initTableIntegration();
        }, 1000);
    });

})(jQuery);

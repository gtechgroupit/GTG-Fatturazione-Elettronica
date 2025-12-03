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

    // Inizializza quando il documento è pronto
    $(document).ready(function() {
        FE.init();
    });

})(jQuery);

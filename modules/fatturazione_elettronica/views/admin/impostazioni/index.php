<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-bold tw-text-xl tw-mb-6">
                    <i class="fa-solid fa-cog tw-mr-2"></i>
                    <?php echo _l('fe_impostazioni'); ?>
                </h4>
            </div>
        </div>

        <form method="post">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <!-- Tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab-azienda" role="tab" data-toggle="tab">
                        <i class="fa fa-building tw-mr-1"></i>
                        <?php echo _l('fe_dati_azienda'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-provider" role="tab" data-toggle="tab">
                        <i class="fa fa-plug tw-mr-1"></i>
                        <?php echo _l('fe_provider_sdi'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-opzioni" role="tab" data-toggle="tab">
                        <i class="fa fa-sliders tw-mr-1"></i>
                        <?php echo _l('fe_opzioni'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-webhook" role="tab" data-toggle="tab">
                        <i class="fa fa-link tw-mr-1"></i>
                        Webhook
                    </a>
                </li>
            </ul>

            <div class="tab-content tw-mt-4">
                <!-- Tab Dati Azienda -->
                <div role="tabpanel" class="tab-pane active" id="tab-azienda">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_denominazione'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="fe_denominazione" class="form-control" value="<?php echo $settings['fe_denominazione']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_partita_iva'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="fe_partita_iva" class="form-control" value="<?php echo $settings['fe_partita_iva']; ?>" required maxlength="11">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_codice_fiscale'); ?></label>
                                        <input type="text" name="fe_codice_fiscale" class="form-control" value="<?php echo $settings['fe_codice_fiscale']; ?>" maxlength="16">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_regime_fiscale'); ?> <span class="text-danger">*</span></label>
                                        <select name="fe_regime_fiscale" class="form-control selectpicker" data-live-search="true" required>
                                            <?php foreach ($regimi_fiscali as $code => $label): ?>
                                                <option value="<?php echo $code; ?>" <?php echo $settings['fe_regime_fiscale'] == $code ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_codice_destinatario'); ?></label>
                                        <input type="text" name="fe_codice_destinatario" class="form-control" value="<?php echo $settings['fe_codice_destinatario']; ?>" maxlength="7">
                                        <small class="text-muted"><?php echo _l('fe_codice_destinatario_help'); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_pec'); ?></label>
                                        <input type="email" name="fe_pec" class="form-control" value="<?php echo $settings['fe_pec']; ?>">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold"><?php echo _l('fe_sede_legale'); ?></h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_indirizzo'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="fe_indirizzo" class="form-control" value="<?php echo $settings['fe_indirizzo']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_cap'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="fe_cap" class="form-control" value="<?php echo $settings['fe_cap']; ?>" required maxlength="5">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_comune'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="fe_comune" class="form-control" value="<?php echo $settings['fe_comune']; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_provincia'); ?></label>
                                        <input type="text" name="fe_provincia" class="form-control" value="<?php echo $settings['fe_provincia']; ?>" maxlength="2" placeholder="RM">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_nazione'); ?></label>
                                        <input type="text" name="fe_nazione" class="form-control" value="<?php echo $settings['fe_nazione'] ?: 'IT'; ?>" maxlength="2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_telefono'); ?></label>
                                        <input type="text" name="fe_telefono" class="form-control" value="<?php echo $settings['fe_telefono']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_email'); ?></label>
                                        <input type="email" name="fe_email" class="form-control" value="<?php echo $settings['fe_email']; ?>">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold"><?php echo _l('fe_dati_rea'); ?></h5>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_rea_ufficio'); ?></label>
                                        <input type="text" name="fe_rea_ufficio" class="form-control" value="<?php echo $settings['fe_rea_ufficio']; ?>" maxlength="2" placeholder="RM">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_rea_numero'); ?></label>
                                        <input type="text" name="fe_rea_numero" class="form-control" value="<?php echo $settings['fe_rea_numero']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_capitale_sociale'); ?></label>
                                        <input type="text" name="fe_capitale_sociale" class="form-control" value="<?php echo $settings['fe_capitale_sociale']; ?>" placeholder="10000.00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_socio_unico'); ?></label>
                                        <select name="fe_socio_unico" class="form-control">
                                            <option value="">-</option>
                                            <option value="SU" <?php echo $settings['fe_socio_unico'] == 'SU' ? 'selected' : ''; ?>>SU - Socio unico</option>
                                            <option value="SM" <?php echo $settings['fe_socio_unico'] == 'SM' ? 'selected' : ''; ?>>SM - Più soci</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_stato_liquidazione'); ?></label>
                                        <select name="fe_stato_liquidazione" class="form-control">
                                            <option value="LN" <?php echo $settings['fe_stato_liquidazione'] == 'LN' ? 'selected' : ''; ?>>LN - Non in liquidazione</option>
                                            <option value="LS" <?php echo $settings['fe_stato_liquidazione'] == 'LS' ? 'selected' : ''; ?>>LS - In liquidazione</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Provider SDI -->
                <div role="tabpanel" class="tab-pane" id="tab-provider">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_provider'); ?> <span class="text-danger">*</span></label>
                                        <select name="fe_provider" id="fe_provider" class="form-control" required>
                                            <?php foreach ($providers as $code => $label): ?>
                                                <option value="<?php echo $code; ?>" <?php echo $settings['fe_provider'] == $code ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_ambiente'); ?></label>
                                        <select name="fe_ambiente" class="form-control">
                                            <option value="test" <?php echo $settings['fe_ambiente'] == 'test' ? 'selected' : ''; ?>><?php echo _l('fe_ambiente_test'); ?></option>
                                            <option value="produzione" <?php echo $settings['fe_ambiente'] == 'produzione' ? 'selected' : ''; ?>><?php echo _l('fe_ambiente_produzione'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-default btn-block" id="btn-test-connection">
                                            <i class="fa fa-plug tw-mr-1"></i>
                                            <?php echo _l('fe_test_connessione'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="provider-custom-fields">
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" id="field-endpoint">
                                            <label><?php echo _l('fe_api_endpoint'); ?></label>
                                            <input type="url" name="fe_api_endpoint" class="form-control" value="<?php echo $settings['fe_api_endpoint']; ?>" placeholder="https://api.example.com/sdi">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group" id="field-username">
                                            <label><?php echo _l('fe_api_username'); ?></label>
                                            <input type="text" name="fe_api_username" class="form-control" value="<?php echo $settings['fe_api_username']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" id="field-password">
                                            <label><?php echo _l('fe_api_password'); ?></label>
                                            <input type="password" name="fe_api_password" class="form-control" value="<?php echo $settings['fe_api_password']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" id="field-api-key">
                                            <label><?php echo _l('fe_api_key'); ?></label>
                                            <input type="text" name="fe_api_key" class="form-control" value="<?php echo $settings['fe_api_key']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" id="field-api-secret">
                                            <label><?php echo _l('fe_api_secret'); ?></label>
                                            <input type="password" name="fe_api_secret" class="form-control" value="<?php echo $settings['fe_api_secret']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info tw-mt-4" id="provider-help">
                                <i class="fa fa-info-circle"></i>
                                <span id="provider-help-text"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Opzioni -->
                <div role="tabpanel" class="tab-pane" id="tab-opzioni">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold"><?php echo _l('fe_opzioni_generazione'); ?></h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="hidden" name="fe_auto_generate_xml" value="0">
                                            <input type="checkbox" name="fe_auto_generate_xml" value="1" <?php echo $settings['fe_auto_generate_xml'] == '1' ? 'checked' : ''; ?>>
                                            <?php echo _l('fe_auto_generate_xml'); ?>
                                        </label>
                                        <p class="text-muted"><?php echo _l('fe_auto_generate_xml_help'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="hidden" name="fe_auto_send" value="0">
                                            <input type="checkbox" name="fe_auto_send" value="1" <?php echo $settings['fe_auto_send'] == '1' ? 'checked' : ''; ?>>
                                            <?php echo _l('fe_auto_send'); ?>
                                        </label>
                                        <p class="text-muted"><?php echo _l('fe_auto_send_help'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold"><?php echo _l('fe_bollo_virtuale'); ?></h5>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_bollo_soglia'); ?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input type="text" name="fe_bollo_virtuale_soglia" class="form-control" value="<?php echo $settings['fe_bollo_virtuale_soglia']; ?>">
                                        </div>
                                        <small class="text-muted"><?php echo _l('fe_bollo_soglia_help'); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_bollo_importo'); ?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input type="text" name="fe_bollo_virtuale_importo" class="form-control" value="<?php echo $settings['fe_bollo_virtuale_importo']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold"><?php echo _l('fe_default_pagamento'); ?></h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_modalita_pagamento'); ?></label>
                                        <select name="fe_default_modalita_pagamento" class="form-control selectpicker" data-live-search="true">
                                            <?php foreach ($modalita_pagamento as $code => $label): ?>
                                                <option value="<?php echo $code; ?>" <?php echo $settings['fe_default_modalita_pagamento'] == $code ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _l('fe_condizioni_pagamento'); ?></label>
                                        <select name="fe_default_condizioni_pagamento" class="form-control">
                                            <?php foreach ($condizioni_pagamento as $code => $label): ?>
                                                <option value="<?php echo $code; ?>" <?php echo $settings['fe_default_condizioni_pagamento'] == $code ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold"><?php echo _l('fe_notifiche'); ?></h5>

                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="fe_email_notifiche" value="0">
                                    <input type="checkbox" name="fe_email_notifiche" value="1" <?php echo $settings['fe_email_notifiche'] == '1' ? 'checked' : ''; ?>>
                                    <?php echo _l('fe_email_notifiche'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Webhook -->
                <div role="tabpanel" class="tab-pane" id="tab-webhook">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold"><?php echo _l('fe_webhook_config'); ?></h5>
                            <p class="text-muted"><?php echo _l('fe_webhook_help'); ?></p>

                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="fe_webhook_enabled" value="0">
                                    <input type="checkbox" name="fe_webhook_enabled" value="1" <?php echo $settings['fe_webhook_enabled'] == '1' ? 'checked' : ''; ?>>
                                    <?php echo _l('fe_webhook_enabled'); ?>
                                </label>
                            </div>

                            <div class="form-group tw-mt-4">
                                <label><?php echo _l('fe_webhook_url'); ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="<?php echo site_url('fatturazione_elettronica/webhook'); ?>" readonly>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" onclick="copyToClipboard(this.parentElement.previousElementSibling.value)">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><?php echo _l('fe_webhook_secret'); ?></label>
                                <div class="input-group">
                                    <input type="text" name="fe_webhook_secret" class="form-control" value="<?php echo $settings['fe_webhook_secret']; ?>" readonly>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" onclick="copyToClipboard(this.parentElement.previousElementSibling.value)">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </span>
                                </div>
                                <small class="text-muted"><?php echo _l('fe_webhook_secret_help'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel_s tw-mt-4">
                <div class="panel-body">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save tw-mr-1"></i>
                        <?php echo _l('save'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php init_tail(); ?>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert_float('success', '<?php echo _l("fe_copied"); ?>');
    });
}

$(function() {
    var providerHelp = {
        'test': '<?php echo _l("fe_provider_test_help"); ?>',
        'aruba': '<?php echo _l("fe_provider_aruba_help"); ?>',
        'infocert': '<?php echo _l("fe_provider_infocert_help"); ?>',
        'fattureincloud': '<?php echo _l("fe_provider_fattureincloud_help"); ?>',
        'custom': '<?php echo _l("fe_provider_custom_help"); ?>'
    };

    function updateProviderFields() {
        var provider = $('#fe_provider').val();

        $('#provider-help-text').text(providerHelp[provider] || '');

        // Nascondi/mostra campi in base al provider
        if (provider === 'test') {
            $('#provider-custom-fields').hide();
        } else {
            $('#provider-custom-fields').show();
        }

        // Mostra solo i campi rilevanti
        if (provider === 'aruba') {
            $('#field-endpoint').hide();
            $('#field-username, #field-password').show();
            $('#field-api-key, #field-api-secret').hide();
        } else if (provider === 'infocert') {
            $('#field-endpoint').hide();
            $('#field-username, #field-password').hide();
            $('#field-api-key').show();
            $('#field-api-secret').hide();
        } else if (provider === 'fattureincloud') {
            $('#field-endpoint').hide();
            $('#field-username, #field-password').hide();
            $('#field-api-key, #field-api-secret').show();
        } else if (provider === 'custom') {
            $('#field-endpoint, #field-username, #field-password, #field-api-key, #field-api-secret').show();
        }
    }

    $('#fe_provider').on('change', updateProviderFields);
    updateProviderFields();

    $('#btn-test-connection').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l("fe_testing"); ?>');

        $.get('<?php echo admin_url("fatturazione_elettronica/test_connection"); ?>', function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                alert_float('success', data.message);
            } else {
                alert_float('danger', data.message + (data.error ? ': ' + data.error : ''));
            }
        }).always(function() {
            $btn.prop('disabled', false).html('<i class="fa fa-plug"></i> <?php echo _l("fe_test_connessione"); ?>');
        });
    });
});
</script>
</body>
</html>

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Helper per accesso sicuro alle impostazioni (definiti solo se non esistono)
if (!function_exists('fe_setting')) {
    function fe_setting($settings, $key, $default = '') {
        return isset($settings[$key]) && $settings[$key] !== '' ? $settings[$key] : $default;
    }
}
if (!function_exists('fe_is_checked')) {
    function fe_is_checked($settings, $key) {
        return isset($settings[$key]) && $settings[$key] === '1';
    }
}

// Inizializzazione sicura variabili
$settings = isset($settings) ? $settings : [];
$regimi_fiscali = isset($regimi_fiscali) ? $regimi_fiscali : [];
$provider_info = isset($provider_info) ? $provider_info : ['name' => '', 'description' => '', 'icon' => 'fa-plug', 'color' => '#333', 'fields' => []];
$current_provider = isset($current_provider) ? $current_provider : 'test';
$modalita_pagamento = isset($modalita_pagamento) ? $modalita_pagamento : [];
$condizioni_pagamento = isset($condizioni_pagamento) ? $condizioni_pagamento : [];
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-bold tw-text-xl tw-mb-6">
                    <i class="fa-solid fa-cog tw-mr-2" aria-hidden="true"></i>
                    <?php echo _l('fe_impostazioni'); ?>
                </h4>
            </div>
        </div>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <!-- Tabs -->
            <ul class="nav nav-tabs" role="tablist" aria-label="<?php echo _l('fe_impostazioni'); ?>">
                <li role="presentation" class="active">
                    <a href="#tab-azienda"
                       id="tab-link-azienda"
                       role="tab"
                       data-toggle="tab"
                       aria-controls="tab-azienda"
                       aria-selected="true">
                        <i class="fa fa-building tw-mr-1" aria-hidden="true"></i>
                        <span class="hidden-xs"><?php echo _l('fe_dati_azienda'); ?></span>
                        <span class="visible-xs-inline"><?php echo _l('fe_dati_azienda'); ?></span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-provider"
                       id="tab-link-provider"
                       role="tab"
                       data-toggle="tab"
                       aria-controls="tab-provider"
                       aria-selected="false">
                        <i class="fa fa-plug tw-mr-1" aria-hidden="true"></i>
                        <span class="hidden-xs"><?php echo _l('fe_provider_sdi'); ?></span>
                        <span class="visible-xs-inline">Provider</span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-opzioni"
                       id="tab-link-opzioni"
                       role="tab"
                       data-toggle="tab"
                       aria-controls="tab-opzioni"
                       aria-selected="false">
                        <i class="fa fa-sliders tw-mr-1" aria-hidden="true"></i>
                        <span class="hidden-xs"><?php echo _l('fe_opzioni'); ?></span>
                        <span class="visible-xs-inline"><?php echo _l('fe_opzioni'); ?></span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-webhook"
                       id="tab-link-webhook"
                       role="tab"
                       data-toggle="tab"
                       aria-controls="tab-webhook"
                       aria-selected="false">
                        <i class="fa fa-link tw-mr-1" aria-hidden="true"></i>
                        Webhook
                    </a>
                </li>
            </ul>

            <div class="tab-content tw-mt-4">
                <!-- Tab Dati Azienda -->
                <div role="tabpanel" class="tab-pane active" id="tab-azienda" aria-labelledby="tab-link-azienda">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="fe_denominazione" data-toggle="tooltip" title="<?php echo _l('fe_denominazione_tooltip'); ?>">
                                            <?php echo _l('fe_denominazione'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text"
                                               id="fe_denominazione"
                                               name="fe_denominazione"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_denominazione'); ?>"
                                               required
                                               aria-required="true">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_partita_iva" data-toggle="tooltip" title="<?php echo _l('fe_partita_iva_tooltip'); ?>">
                                            <?php echo _l('fe_partita_iva'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text"
                                               id="fe_partita_iva"
                                               name="fe_partita_iva"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_partita_iva'); ?>"
                                               required
                                               aria-required="true"
                                               maxlength="11"
                                               pattern="[0-9]{11}"
                                               title="11 cifre numeriche">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_codice_fiscale" data-toggle="tooltip" title="<?php echo _l('fe_codice_fiscale_tooltip'); ?>">
                                            <?php echo _l('fe_codice_fiscale'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text"
                                               id="fe_codice_fiscale"
                                               name="fe_codice_fiscale"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_codice_fiscale'); ?>"
                                               maxlength="16">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="fe_regime_fiscale" data-toggle="tooltip" title="<?php echo _l('fe_regime_fiscale_tooltip'); ?>">
                                            <?php echo _l('fe_regime_fiscale'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <select id="fe_regime_fiscale"
                                                name="fe_regime_fiscale"
                                                class="form-control selectpicker"
                                                data-live-search="true"
                                                required
                                                aria-required="true">
                                            <?php foreach ($regimi_fiscali as $code => $label): ?>
                                                <option value="<?php echo $code; ?>" <?php echo fe_setting($settings, 'fe_regime_fiscale', 'RF01') == $code ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_codice_destinatario" data-toggle="tooltip" title="<?php echo _l('fe_codice_destinatario_tooltip'); ?>">
                                            <?php echo _l('fe_codice_destinatario'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text"
                                               id="fe_codice_destinatario"
                                               name="fe_codice_destinatario"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_codice_destinatario'); ?>"
                                               maxlength="7"
                                               aria-describedby="fe_codice_destinatario_help">
                                        <small id="fe_codice_destinatario_help" class="text-muted"><?php echo _l('fe_codice_destinatario_help'); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_pec" data-toggle="tooltip" title="<?php echo _l('fe_pec_tooltip'); ?>">
                                            <?php echo _l('fe_pec'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="email"
                                               id="fe_pec"
                                               name="fe_pec"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_pec'); ?>">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold"><?php echo _l('fe_sede_legale'); ?></h5>

                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="fe_indirizzo">
                                            <?php echo _l('fe_indirizzo'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                        </label>
                                        <input type="text"
                                               id="fe_indirizzo"
                                               name="fe_indirizzo"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_indirizzo'); ?>"
                                               required
                                               aria-required="true">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 col-xs-4">
                                    <div class="form-group">
                                        <label for="fe_cap">
                                            <?php echo _l('fe_cap'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                        </label>
                                        <input type="text"
                                               id="fe_cap"
                                               name="fe_cap"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_cap'); ?>"
                                               required
                                               aria-required="true"
                                               maxlength="5"
                                               pattern="[0-9]{5}"
                                               title="5 cifre">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-8 col-xs-8">
                                    <div class="form-group">
                                        <label for="fe_comune">
                                            <?php echo _l('fe_comune'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                        </label>
                                        <input type="text"
                                               id="fe_comune"
                                               name="fe_comune"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_comune'); ?>"
                                               required
                                               aria-required="true">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_provincia"><?php echo _l('fe_provincia'); ?></label>
                                        <input type="text"
                                               id="fe_provincia"
                                               name="fe_provincia"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_provincia'); ?>"
                                               maxlength="2"
                                               placeholder="RM">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_nazione"><?php echo _l('fe_nazione'); ?></label>
                                        <input type="text"
                                               id="fe_nazione"
                                               name="fe_nazione"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_nazione', 'IT'); ?>"
                                               maxlength="2">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_telefono"><?php echo _l('fe_telefono'); ?></label>
                                        <input type="tel"
                                               id="fe_telefono"
                                               name="fe_telefono"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_telefono'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_email"><?php echo _l('fe_email'); ?></label>
                                        <input type="email"
                                               id="fe_email"
                                               name="fe_email"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_email'); ?>">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold" data-toggle="tooltip" title="<?php echo _l('fe_dati_rea_tooltip'); ?>">
                                <?php echo _l('fe_dati_rea'); ?>
                                <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                            </h5>

                            <div class="row">
                                <div class="col-md-2 col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_rea_ufficio"><?php echo _l('fe_rea_ufficio'); ?></label>
                                        <input type="text"
                                               id="fe_rea_ufficio"
                                               name="fe_rea_ufficio"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_rea_ufficio'); ?>"
                                               maxlength="2"
                                               placeholder="RM">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_rea_numero"><?php echo _l('fe_rea_numero'); ?></label>
                                        <input type="text"
                                               id="fe_rea_numero"
                                               name="fe_rea_numero"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_rea_numero'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_capitale_sociale"><?php echo _l('fe_capitale_sociale'); ?></label>
                                        <input type="text"
                                               id="fe_capitale_sociale"
                                               name="fe_capitale_sociale"
                                               class="form-control"
                                               value="<?php echo fe_setting($settings, 'fe_capitale_sociale'); ?>"
                                               placeholder="10000.00">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="fe_socio_unico"><?php echo _l('fe_socio_unico'); ?></label>
                                        <select id="fe_socio_unico" name="fe_socio_unico" class="form-control">
                                            <option value="">-</option>
                                            <option value="SU" <?php echo fe_setting($settings, 'fe_socio_unico') == 'SU' ? 'selected' : ''; ?>>SU - Socio unico</option>
                                            <option value="SM" <?php echo fe_setting($settings, 'fe_socio_unico') == 'SM' ? 'selected' : ''; ?>>SM - Più soci</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="fe_stato_liquidazione"><?php echo _l('fe_stato_liquidazione'); ?></label>
                                        <select id="fe_stato_liquidazione" name="fe_stato_liquidazione" class="form-control">
                                            <option value="LN" <?php echo fe_setting($settings, 'fe_stato_liquidazione', 'LN') == 'LN' ? 'selected' : ''; ?>>LN - Non in liquidazione</option>
                                            <option value="LS" <?php echo fe_setting($settings, 'fe_stato_liquidazione', 'LN') == 'LS' ? 'selected' : ''; ?>>LS - In liquidazione</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Provider SDI -->
                <div role="tabpanel" class="tab-pane" id="tab-provider" aria-labelledby="tab-link-provider">
                    <div class="panel_s">
                        <div class="panel-body">
                            <!-- Provider attivo -->
                            <div class="row">
                                <div class="col-md-8 col-sm-7 col-xs-12">
                                    <div class="tw-flex tw-items-center tw-mb-4">
                                        <div class="tw-p-3 tw-rounded-lg tw-mr-4" style="background-color: <?php echo isset($provider_info['color']) ? $provider_info['color'] : '#333'; ?>" aria-hidden="true">
                                            <i class="fa <?php echo isset($provider_info['icon']) ? $provider_info['icon'] : 'fa-plug'; ?> tw-text-2xl tw-text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="tw-font-bold tw-mb-1"><?php echo isset($provider_info['name']) ? $provider_info['name'] : ''; ?></h5>
                                            <p class="tw-text-gray-600 tw-mb-0"><?php echo isset($provider_info['description']) ? $provider_info['description'] : ''; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-5 col-xs-12 tw-text-right">
                                    <a href="<?php echo admin_url('fatturazione_elettronica/setup/1'); ?>"
                                       class="btn btn-default"
                                       data-toggle="tooltip"
                                       title="<?php echo _l('fe_change_provider_tooltip'); ?>">
                                        <i class="fa fa-exchange tw-mr-1" aria-hidden="true"></i>
                                        <span class="hidden-xs"><?php echo _l('fe_change_provider'); ?></span>
                                        <span class="visible-xs-inline"><?php echo _l('fe_change_provider'); ?></span>
                                    </a>
                                </div>
                            </div>

                            <input type="hidden" name="fe_provider" value="<?php echo $current_provider; ?>">

                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="fe_ambiente"><?php echo _l('fe_ambiente'); ?></label>
                                        <select id="fe_ambiente" name="fe_ambiente" class="form-control">
                                            <option value="test" <?php echo fe_setting($settings, 'fe_ambiente', 'test') == 'test' ? 'selected' : ''; ?>><?php echo _l('fe_ambiente_test'); ?></option>
                                            <option value="produzione" <?php echo fe_setting($settings, 'fe_ambiente', 'test') == 'produzione' ? 'selected' : ''; ?>><?php echo _l('fe_ambiente_produzione'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="hidden-xs">&nbsp;</label>
                                        <button type="button"
                                                class="btn btn-default btn-block"
                                                id="btn-test-connection"
                                                data-toggle="tooltip"
                                                title="<?php echo _l('fe_test_connessione_tooltip'); ?>">
                                            <i class="fa fa-plug tw-mr-1" aria-hidden="true"></i>
                                            <?php echo _l('fe_test_connessione'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($provider_info['fields'])): ?>
                            <hr>
                            <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_provider_credentials'); ?></h5>

                            <div class="row">
                                <?php foreach ($provider_info['fields'] as $field): ?>
                                <div class="col-md-<?php echo $field['col'] ?? 6; ?>">
                                    <div class="form-group">
                                        <label>
                                            <?php echo $field['label']; ?>
                                            <?php if (!empty($field['required'])): ?>
                                            <span class="text-danger">*</span>
                                            <?php endif; ?>
                                        </label>

                                        <?php if ($field['type'] == 'file'): ?>
                                        <input type="file" name="<?php echo $field['name']; ?>" class="form-control"
                                               accept="<?php echo $field['accept'] ?? ''; ?>">
                                        <?php if (!empty($settings[$field['name']])): ?>
                                        <small class="text-success">
                                            <i class="fa fa-check"></i> <?php echo _l('fe_file_uploaded'); ?>: <?php echo basename($settings[$field['name']]); ?>
                                        </small>
                                        <?php endif; ?>

                                        <?php elseif ($field['type'] == 'password'): ?>
                                        <input type="password" name="<?php echo $field['name']; ?>" class="form-control"
                                               value="<?php echo $settings[$field['name']] ?? ''; ?>"
                                               placeholder="<?php echo $field['placeholder'] ?? ''; ?>">

                                        <?php elseif ($field['type'] == 'select'): ?>
                                        <select name="<?php echo $field['name']; ?>" class="form-control">
                                            <?php foreach ($field['options'] as $val => $label): ?>
                                            <option value="<?php echo $val; ?>"
                                                    <?php echo ($settings[$field['name']] ?? '') == $val ? 'selected' : ''; ?>>
                                                <?php echo $label; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <?php else: ?>
                                        <input type="<?php echo $field['type']; ?>" name="<?php echo $field['name']; ?>"
                                               class="form-control"
                                               value="<?php echo $settings[$field['name']] ?? ''; ?>"
                                               placeholder="<?php echo $field['placeholder'] ?? ''; ?>">
                                        <?php endif; ?>

                                        <?php if (!empty($field['help'])): ?>
                                        <small class="text-muted"><?php echo $field['help']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($current_provider == 'fattureincloud'): ?>
                            <!-- Stato OAuth per FattureInCloud -->
                            <hr>
                            <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_oauth_status'); ?></h5>

                            <?php if (!empty($settings['fe_fic_access_token'])): ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <?php echo _l('fe_oauth_connected'); ?>
                                <?php if (!empty($settings['fe_fic_company_name'])): ?>
                                - <strong><?php echo $settings['fe_fic_company_name']; ?></strong>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo admin_url('fatturazione_elettronica/oauth_disconnect'); ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('<?php echo _l('fe_confirm_disconnect'); ?>');">
                                <i class="fa fa-unlink tw-mr-1"></i>
                                <?php echo _l('fe_disconnect_account'); ?>
                            </a>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                <?php echo _l('fe_oauth_not_connected'); ?>
                            </div>
                            <a href="<?php echo admin_url('fatturazione_elettronica/setup/2?provider=fattureincloud'); ?>"
                               class="btn btn-primary">
                                <i class="fa fa-link tw-mr-1"></i>
                                <?php echo _l('fe_connect_account'); ?>
                            </a>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab Opzioni -->
                <div role="tabpanel" class="tab-pane" id="tab-opzioni" aria-labelledby="tab-link-opzioni">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold" data-toggle="tooltip" title="<?php echo _l('fe_opzioni_generazione_tooltip'); ?>">
                                <?php echo _l('fe_opzioni_generazione'); ?>
                                <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                            </h5>

                            <div class="row tw-mt-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fe_auto_generate_xml" class="control-label" data-toggle="tooltip" title="<?php echo _l('fe_auto_generate_xml_tooltip'); ?>">
                                            <?php echo _l('fe_auto_generate_xml'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <div class="onoffswitch">
                                            <input type="hidden" name="fe_auto_generate_xml" value="0">
                                            <input type="checkbox"
                                                   id="fe_auto_generate_xml"
                                                   name="fe_auto_generate_xml"
                                                   value="1"
                                                   class="onoffswitch-checkbox"
                                                   <?php echo fe_is_checked($settings, 'fe_auto_generate_xml') ? 'checked' : ''; ?>>
                                            <label class="onoffswitch-label" for="fe_auto_generate_xml"></label>
                                        </div>
                                        <p class="text-muted tw-mt-1"><?php echo _l('fe_auto_generate_xml_help'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fe_auto_send" class="control-label" data-toggle="tooltip" title="<?php echo _l('fe_auto_send_tooltip'); ?>">
                                            <?php echo _l('fe_auto_send'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <div class="onoffswitch">
                                            <input type="hidden" name="fe_auto_send" value="0">
                                            <input type="checkbox"
                                                   id="fe_auto_send"
                                                   name="fe_auto_send"
                                                   value="1"
                                                   class="onoffswitch-checkbox"
                                                   <?php echo fe_is_checked($settings, 'fe_auto_send') ? 'checked' : ''; ?>>
                                            <label class="onoffswitch-label" for="fe_auto_send"></label>
                                        </div>
                                        <p class="text-muted tw-mt-1"><?php echo _l('fe_auto_send_help'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold" data-toggle="tooltip" title="<?php echo _l('fe_bollo_virtuale_tooltip'); ?>">
                                <?php echo _l('fe_bollo_virtuale'); ?>
                                <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                            </h5>

                            <div class="row tw-mt-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fe_bollo_virtuale_soglia" data-toggle="tooltip" title="<?php echo _l('fe_bollo_soglia_tooltip'); ?>">
                                            <?php echo _l('fe_bollo_soglia'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input type="text"
                                                   id="fe_bollo_virtuale_soglia"
                                                   name="fe_bollo_virtuale_soglia"
                                                   class="form-control"
                                                   value="<?php echo fe_setting($settings, 'fe_bollo_virtuale_soglia', '77.47'); ?>">
                                        </div>
                                        <small class="text-muted"><?php echo _l('fe_bollo_soglia_help'); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fe_bollo_virtuale_importo" data-toggle="tooltip" title="<?php echo _l('fe_bollo_importo_tooltip'); ?>">
                                            <?php echo _l('fe_bollo_importo'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input type="text"
                                                   id="fe_bollo_virtuale_importo"
                                                   name="fe_bollo_virtuale_importo"
                                                   class="form-control"
                                                   value="<?php echo fe_setting($settings, 'fe_bollo_virtuale_importo', '2.00'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold" data-toggle="tooltip" title="<?php echo _l('fe_default_pagamento_tooltip'); ?>">
                                <?php echo _l('fe_default_pagamento'); ?>
                                <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                            </h5>

                            <div class="row tw-mt-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fe_default_modalita_pagamento" data-toggle="tooltip" title="<?php echo _l('fe_modalita_pagamento_tooltip'); ?>">
                                            <?php echo _l('fe_modalita_pagamento'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <select id="fe_default_modalita_pagamento"
                                                name="fe_default_modalita_pagamento"
                                                class="form-control selectpicker"
                                                data-live-search="true">
                                            <?php foreach ($modalita_pagamento as $code => $label): ?>
                                                <option value="<?php echo $code; ?>" <?php echo fe_setting($settings, 'fe_default_modalita_pagamento', 'MP05') == $code ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fe_default_condizioni_pagamento" data-toggle="tooltip" title="<?php echo _l('fe_condizioni_pagamento_tooltip'); ?>">
                                            <?php echo _l('fe_condizioni_pagamento'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <select id="fe_default_condizioni_pagamento"
                                                name="fe_default_condizioni_pagamento"
                                                class="form-control">
                                            <?php foreach ($condizioni_pagamento as $code => $label): ?>
                                                <option value="<?php echo $code; ?>" <?php echo fe_setting($settings, 'fe_default_condizioni_pagamento', 'TP02') == $code ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold" data-toggle="tooltip" title="<?php echo _l('fe_notifiche_tooltip'); ?>">
                                <?php echo _l('fe_notifiche'); ?>
                                <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                            </h5>

                            <div class="form-group tw-mt-4">
                                <label for="fe_email_notifiche" class="control-label" data-toggle="tooltip" title="<?php echo _l('fe_email_notifiche_tooltip'); ?>">
                                    <?php echo _l('fe_email_notifiche'); ?>
                                    <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                </label>
                                <div class="onoffswitch">
                                    <input type="hidden" name="fe_email_notifiche" value="0">
                                    <input type="checkbox"
                                           id="fe_email_notifiche"
                                           name="fe_email_notifiche"
                                           value="1"
                                           class="onoffswitch-checkbox"
                                           <?php echo fe_is_checked($settings, 'fe_email_notifiche') ? 'checked' : ''; ?>>
                                    <label class="onoffswitch-label" for="fe_email_notifiche"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Webhook -->
                <div role="tabpanel" class="tab-pane" id="tab-webhook" aria-labelledby="tab-link-webhook">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold" data-toggle="tooltip" title="<?php echo _l('fe_webhook_config_tooltip'); ?>">
                                <?php echo _l('fe_webhook_config'); ?>
                                <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                            </h5>
                            <p class="text-muted"><?php echo _l('fe_webhook_help'); ?></p>

                            <div class="form-group tw-mt-4">
                                <label for="fe_webhook_enabled" class="control-label" data-toggle="tooltip" title="<?php echo _l('fe_webhook_enabled_tooltip'); ?>">
                                    <?php echo _l('fe_webhook_enabled'); ?>
                                    <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                </label>
                                <div class="onoffswitch">
                                    <input type="hidden" name="fe_webhook_enabled" value="0">
                                    <input type="checkbox"
                                           id="fe_webhook_enabled"
                                           name="fe_webhook_enabled"
                                           value="1"
                                           class="onoffswitch-checkbox"
                                           <?php echo fe_is_checked($settings, 'fe_webhook_enabled') ? 'checked' : ''; ?>>
                                    <label class="onoffswitch-label" for="fe_webhook_enabled"></label>
                                </div>
                            </div>

                            <div class="form-group tw-mt-4">
                                <label for="fe_webhook_url_display" data-toggle="tooltip" title="<?php echo _l('fe_webhook_url_tooltip'); ?>">
                                    <?php echo _l('fe_webhook_url'); ?>
                                    <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                           id="fe_webhook_url_display"
                                           class="form-control"
                                           value="<?php echo site_url('fatturazione_elettronica/webhook'); ?>"
                                           readonly>
                                    <span class="input-group-btn">
                                        <button type="button"
                                                class="btn btn-default"
                                                onclick="copyToClipboard(this.parentElement.previousElementSibling.value)"
                                                data-toggle="tooltip"
                                                title="<?php echo _l('fe_copia_url'); ?>">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </span>
                                </div>
                                <small class="text-muted"><?php echo _l('fe_webhook_url_help'); ?></small>
                            </div>

                            <div class="form-group">
                                <label for="fe_webhook_secret" data-toggle="tooltip" title="<?php echo _l('fe_webhook_secret_tooltip'); ?>">
                                    <?php echo _l('fe_webhook_secret'); ?>
                                    <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                           id="fe_webhook_secret"
                                           name="fe_webhook_secret"
                                           class="form-control"
                                           value="<?php echo fe_setting($settings, 'fe_webhook_secret'); ?>"
                                           readonly>
                                    <span class="input-group-btn">
                                        <button type="button"
                                                class="btn btn-default"
                                                onclick="copyToClipboard(this.parentElement.previousElementSibling.value)"
                                                data-toggle="tooltip"
                                                title="<?php echo _l('fe_copia_secret'); ?>">
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
                    <button type="submit"
                            class="btn btn-primary"
                            data-toggle="tooltip"
                            title="<?php echo _l('fe_salva_impostazioni_tooltip'); ?>">
                        <i class="fa fa-save tw-mr-1" aria-hidden="true"></i>
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
    }).catch(function() {
        // Fallback per browser più vecchi
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert_float('success', '<?php echo _l("fe_copied"); ?>');
        } catch (err) {
            alert_float('danger', 'Copia non riuscita');
        }
        document.body.removeChild(textArea);
    });
}

$(function() {
    // Inizializza tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Aggiorna aria-selected sui tab
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $(e.relatedTarget).attr('aria-selected', 'false');
        $(e.target).attr('aria-selected', 'true');
    });

    // Test connessione
    $('#btn-test-connection').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> <?php echo _l("fe_testing"); ?>');

        $.get('<?php echo admin_url("fatturazione_elettronica/test_connection"); ?>', function(response) {
            var data = typeof response === 'string' ? JSON.parse(response) : response;
            if (data.success) {
                alert_float('success', data.message);
            } else {
                alert_float('danger', data.message + (data.error ? ': ' + data.error : ''));
            }
        }).fail(function() {
            alert_float('danger', '<?php echo _l("fe_connection_failed"); ?>');
        }).always(function() {
            $btn.prop('disabled', false).html('<i class="fa fa-plug" aria-hidden="true"></i> <?php echo _l("fe_test_connessione"); ?>');
        });
    });
});
</script>
</body>
</html>

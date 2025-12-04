<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-text-center tw-mb-8">
                    <h2 class="tw-font-bold tw-text-2xl"
                        data-toggle="tooltip"
                        title="<?php echo _l('fe_setup_wizard_title_tooltip'); ?>">
                        <i class="fa-solid fa-file-invoice tw-mr-2 tw-text-primary" aria-hidden="true"></i>
                        <?php echo _l('fe_setup_wizard_title'); ?>
                    </h2>
                    <p class="tw-text-gray-600 tw-mt-2">
                        <?php echo _l('fe_setup_wizard_subtitle'); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Wizard Steps -->
        <div class="row tw-mb-6">
            <div class="col-md-8 col-md-offset-2">
                <div class="tw-flex tw-justify-between tw-items-center">
                    <div class="wizard-step <?php echo $step == 1 ? 'active' : ($step > 1 ? 'completed' : ''); ?>"
                         data-toggle="tooltip"
                         title="<?php echo _l('fe_step_provider_tooltip'); ?>">
                        <div class="step-number">1</div>
                        <div class="step-label"><?php echo _l('fe_step_provider'); ?></div>
                    </div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step <?php echo $step == 2 ? 'active' : ($step > 2 ? 'completed' : ''); ?>"
                         data-toggle="tooltip"
                         title="<?php echo _l('fe_step_credentials_tooltip'); ?>">
                        <div class="step-number">2</div>
                        <div class="step-label"><?php echo _l('fe_step_credentials'); ?></div>
                    </div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step <?php echo $step == 3 ? 'active' : ''; ?>"
                         data-toggle="tooltip"
                         title="<?php echo _l('fe_step_company_tooltip'); ?>">
                        <div class="step-number">3</div>
                        <div class="step-label"><?php echo _l('fe_step_company'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($step == 1): ?>
        <!-- Step 1: Selezione Provider -->
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h4 class="tw-font-semibold tw-mb-4 tw-text-center"
                    data-toggle="tooltip"
                    title="<?php echo _l('fe_select_provider_tooltip'); ?>">
                    <?php echo _l('fe_select_provider'); ?>
                </h4>

                <div class="row provider-cards">
                    <?php foreach ($providers as $provider_id => $provider): ?>
                    <div class="col-md-6 col-lg-3 tw-mb-4">
                        <div class="provider-card <?php echo $current_provider == $provider_id ? 'selected' : ''; ?>"
                             data-provider="<?php echo $provider_id; ?>">
                            <div class="provider-header" style="background-color: <?php echo $provider['color'] ?? '#333'; ?>">
                                <i class="fa <?php echo $provider['icon'] ?? 'fa-plug'; ?> tw-text-3xl tw-text-white"></i>
                            </div>
                            <div class="provider-body">
                                <h5 class="tw-font-bold"><?php echo $provider['name']; ?></h5>
                                <p class="tw-text-sm tw-text-gray-600 tw-mb-3">
                                    <?php echo $provider['description']; ?>
                                </p>

                                <?php if (!empty($provider['pricing'])): ?>
                                <div class="tw-mb-2">
                                    <?php if (is_array($provider['pricing'])): ?>
                                        <?php foreach ($provider['pricing'] as $price): ?>
                                        <span class="label label-info tw-mr-1"><?php echo $price; ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="label label-info"><?php echo $provider['pricing']; ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($provider['features'])): ?>
                                <ul class="tw-text-xs tw-text-left tw-pl-4 tw-mb-0">
                                    <?php foreach (array_slice($provider['features'], 0, 3) as $feature): ?>
                                    <li><?php echo $feature; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                            <div class="provider-footer">
                                <button type="button" class="btn btn-primary btn-block btn-select-provider"
                                        data-provider="<?php echo $provider_id; ?>">
                                    <?php echo $current_provider == $provider_id ? _l('fe_selected') : _l('fe_select'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="tw-text-center tw-mt-4">
                    <a href="<?php echo admin_url('fatturazione_elettronica'); ?>"
                       class="btn btn-default"
                       data-toggle="tooltip"
                       title="<?php echo _l('fe_skip_setup_tooltip'); ?>">
                        <i class="fa fa-arrow-left tw-mr-1" aria-hidden="true"></i>
                        <?php echo _l('fe_skip_setup'); ?>
                    </a>
                </div>
            </div>
        </div>

        <?php elseif ($step == 2): ?>
        <!-- Step 2: Configurazione Credenziali -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-text-center tw-mb-4">
                            <div class="tw-inline-block tw-p-4 tw-rounded-full tw-mb-3"
                                 style="background-color: <?php echo $provider_info['color'] ?? '#333'; ?>">
                                <i class="fa <?php echo $provider_info['icon'] ?? 'fa-plug'; ?> tw-text-3xl tw-text-white"></i>
                            </div>
                            <h4 class="tw-font-semibold"><?php echo $provider_info['name']; ?></h4>
                            <p class="tw-text-gray-600"><?php echo _l('fe_configure_credentials'); ?></p>
                        </div>

                        <?php if (!empty($provider_info['requirements'])): ?>
                        <div class="alert alert-info tw-mb-4">
                            <strong><i class="fa fa-info-circle"></i> <?php echo _l('fe_requirements'); ?>:</strong>
                            <ul class="tw-mb-0 tw-mt-2">
                                <?php foreach ($provider_info['requirements'] as $req): ?>
                                <li><?php echo $req; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <?php if ($selected_provider == 'fattureincloud'): ?>
                        <!-- OAuth Flow per FattureInCloud -->
                        <div class="tw-text-center tw-py-4">
                            <?php if (!empty($settings['fe_fic_access_token'])): ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <?php echo _l('fe_oauth_connected'); ?>
                                <?php if (!empty($settings['fe_fic_company_name'])): ?>
                                <br><strong><?php echo $settings['fe_fic_company_name']; ?></strong>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo admin_url('fatturazione_elettronica/setup/3'); ?>"
                               class="btn btn-primary btn-lg"
                               data-toggle="tooltip"
                               title="<?php echo _l('fe_continue_tooltip'); ?>">
                                <?php echo _l('fe_continue'); ?>
                                <i class="fa fa-arrow-right tw-ml-1" aria-hidden="true"></i>
                            </a>
                            <br><br>
                            <a href="<?php echo admin_url('fatturazione_elettronica/oauth_disconnect'); ?>"
                               class="btn btn-danger btn-sm"
                               data-toggle="tooltip"
                               title="<?php echo _l('fe_disconnect_account_tooltip'); ?>"
                               onclick="return confirm('<?php echo _l('fe_confirm_disconnect'); ?>');">
                                <?php echo _l('fe_disconnect_account'); ?>
                            </a>
                            <?php else: ?>
                            <p class="tw-mb-4"><?php echo _l('fe_oauth_instructions'); ?></p>

                            <form method="post" action="<?php echo admin_url('fatturazione_elettronica/save_oauth_credentials'); ?>">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                       value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label data-toggle="tooltip" title="<?php echo _l('fe_client_id_tooltip'); ?>">
                                                Client ID <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                                <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                            </label>
                                            <input type="text" name="fe_fic_client_id" class="form-control"
                                                   value="<?php echo $settings['fe_fic_client_id'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label data-toggle="tooltip" title="<?php echo _l('fe_client_secret_tooltip'); ?>">
                                                Client Secret <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                                <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                            </label>
                                            <input type="password" name="fe_fic_client_secret" class="form-control"
                                                   value="<?php echo $settings['fe_fic_client_secret'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit"
                                        class="btn btn-primary btn-lg"
                                        data-toggle="tooltip"
                                        title="<?php echo _l('fe_connect_account_tooltip'); ?>">
                                    <i class="fa fa-link tw-mr-1" aria-hidden="true"></i>
                                    <?php echo _l('fe_connect_account'); ?>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>

                        <?php else: ?>
                        <!-- Form standard per altri provider -->
                        <form method="post" action="<?php echo admin_url('fatturazione_elettronica/save_credentials'); ?>"
                              enctype="multipart/form-data">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                   value="<?php echo $this->security->get_csrf_hash(); ?>">

                            <?php if (!empty($provider_info['fields'])): ?>
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
                                               <?php echo !empty($field['required']) ? 'required' : ''; ?>
                                               accept="<?php echo $field['accept'] ?? ''; ?>">
                                        <?php if (!empty($settings[$field['name']])): ?>
                                        <small class="text-success">
                                            <i class="fa fa-check"></i> <?php echo _l('fe_file_uploaded'); ?>
                                        </small>
                                        <?php endif; ?>

                                        <?php elseif ($field['type'] == 'password'): ?>
                                        <input type="password" name="<?php echo $field['name']; ?>" class="form-control"
                                               value="<?php echo $settings[$field['name']] ?? ''; ?>"
                                               <?php echo !empty($field['required']) ? 'required' : ''; ?>
                                               placeholder="<?php echo $field['placeholder'] ?? ''; ?>">

                                        <?php elseif ($field['type'] == 'select'): ?>
                                        <select name="<?php echo $field['name']; ?>" class="form-control"
                                                <?php echo !empty($field['required']) ? 'required' : ''; ?>>
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
                                               <?php echo !empty($field['required']) ? 'required' : ''; ?>
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

                            <div class="tw-flex tw-justify-between tw-mt-4">
                                <a href="<?php echo admin_url('fatturazione_elettronica/setup/1'); ?>"
                                   class="btn btn-default"
                                   data-toggle="tooltip"
                                   title="<?php echo _l('fe_back_tooltip'); ?>">
                                    <i class="fa fa-arrow-left tw-mr-1" aria-hidden="true"></i>
                                    <?php echo _l('fe_back'); ?>
                                </a>
                                <button type="submit"
                                        class="btn btn-primary"
                                        data-toggle="tooltip"
                                        title="<?php echo _l('fe_continue_tooltip'); ?>">
                                    <?php echo _l('fe_continue'); ?>
                                    <i class="fa fa-arrow-right tw-ml-1" aria-hidden="true"></i>
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php elseif ($step == 3): ?>
        <!-- Step 3: Dati Azienda -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4 tw-text-center">
                            <i class="fa fa-building tw-mr-2"></i>
                            <?php echo _l('fe_company_data'); ?>
                        </h4>

                        <form method="post" action="<?php echo admin_url('fatturazione_elettronica/save_company'); ?>">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                   value="<?php echo $this->security->get_csrf_hash(); ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_denominazione_tooltip'); ?>">
                                            <?php echo _l('fe_denominazione'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_denominazione" class="form-control"
                                               value="<?php echo $settings['fe_denominazione']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_partita_iva_tooltip'); ?>">
                                            <?php echo _l('fe_partita_iva'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_partita_iva" class="form-control"
                                               value="<?php echo $settings['fe_partita_iva']; ?>" required maxlength="11">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_codice_fiscale_tooltip'); ?>">
                                            <?php echo _l('fe_codice_fiscale'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_codice_fiscale" class="form-control"
                                               value="<?php echo $settings['fe_codice_fiscale']; ?>" maxlength="16">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_regime_fiscale_tooltip'); ?>">
                                            <?php echo _l('fe_regime_fiscale'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <select name="fe_regime_fiscale" class="form-control selectpicker"
                                                data-live-search="true" required>
                                            <?php foreach ($regimi_fiscali as $code => $label): ?>
                                            <option value="<?php echo $code; ?>"
                                                    <?php echo $settings['fe_regime_fiscale'] == $code ? 'selected' : ''; ?>>
                                                <?php echo $label; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_codice_destinatario_tooltip'); ?>">
                                            <?php echo _l('fe_codice_destinatario'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_codice_destinatario" class="form-control"
                                               value="<?php echo $settings['fe_codice_destinatario']; ?>" maxlength="7">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_pec_tooltip'); ?>">
                                            <?php echo _l('fe_pec'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="email" name="fe_pec" class="form-control"
                                               value="<?php echo $settings['fe_pec']; ?>">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="tw-font-semibold"><?php echo _l('fe_sede_legale'); ?></h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_indirizzo_tooltip'); ?>">
                                            <?php echo _l('fe_indirizzo'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_indirizzo" class="form-control"
                                               value="<?php echo $settings['fe_indirizzo']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_cap_tooltip'); ?>">
                                            <?php echo _l('fe_cap'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_cap" class="form-control"
                                               value="<?php echo $settings['fe_cap']; ?>" required maxlength="5">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_comune_tooltip'); ?>">
                                            <?php echo _l('fe_comune'); ?>
                                            <span class="text-danger" aria-label="campo obbligatorio">*</span>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_comune" class="form-control"
                                               value="<?php echo $settings['fe_comune']; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_provincia_tooltip'); ?>">
                                            <?php echo _l('fe_provincia'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_provincia" class="form-control"
                                               value="<?php echo $settings['fe_provincia']; ?>" maxlength="2" placeholder="RM">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_nazione_tooltip'); ?>">
                                            <?php echo _l('fe_nazione'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_nazione" class="form-control"
                                               value="<?php echo $settings['fe_nazione'] ?: 'IT'; ?>" maxlength="2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_telefono_tooltip'); ?>">
                                            <?php echo _l('fe_telefono'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="text" name="fe_telefono" class="form-control"
                                               value="<?php echo $settings['fe_telefono']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label data-toggle="tooltip" title="<?php echo _l('fe_email_tooltip'); ?>">
                                            <?php echo _l('fe_email'); ?>
                                            <i class="fa fa-question-circle text-muted" aria-hidden="true"></i>
                                        </label>
                                        <input type="email" name="fe_email" class="form-control"
                                               value="<?php echo $settings['fe_email']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="tw-flex tw-justify-between tw-mt-4">
                                <a href="<?php echo admin_url('fatturazione_elettronica/setup/2'); ?>"
                                   class="btn btn-default"
                                   data-toggle="tooltip"
                                   title="<?php echo _l('fe_back_tooltip'); ?>">
                                    <i class="fa fa-arrow-left tw-mr-1" aria-hidden="true"></i>
                                    <?php echo _l('fe_back'); ?>
                                </a>
                                <button type="submit"
                                        class="btn btn-success btn-lg"
                                        data-toggle="tooltip"
                                        title="<?php echo _l('fe_complete_setup_tooltip'); ?>">
                                    <i class="fa fa-check tw-mr-1" aria-hidden="true"></i>
                                    <?php echo _l('fe_complete_setup'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.wizard-step {
    text-align: center;
    position: relative;
}
.wizard-step .step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-weight: bold;
    font-size: 16px;
}
.wizard-step.active .step-number {
    background: #84c529;
    color: white;
}
.wizard-step.completed .step-number {
    background: #28a745;
    color: white;
}
.wizard-step.completed .step-number:after {
    content: '\f00c';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}
.wizard-step .step-label {
    font-size: 12px;
    color: #6c757d;
}
.wizard-step.active .step-label {
    color: #84c529;
    font-weight: bold;
}
.wizard-line {
    flex: 1;
    height: 2px;
    background: #e9ecef;
    margin: 0 15px;
    margin-top: -20px;
}

.provider-card {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    cursor: pointer;
}
.provider-card:hover {
    border-color: #84c529;
    box-shadow: 0 4px 15px rgba(132, 197, 41, 0.2);
    transform: translateY(-2px);
}
.provider-card.selected {
    border-color: #84c529;
    box-shadow: 0 4px 15px rgba(132, 197, 41, 0.3);
}
.provider-header {
    padding: 30px 20px;
    text-align: center;
}
.provider-body {
    padding: 20px;
    text-align: center;
    min-height: 180px;
}
.provider-footer {
    padding: 15px 20px;
    background: #f8f9fa;
}
.provider-card.selected .btn-select-provider {
    background: #28a745;
    border-color: #28a745;
}
</style>

<?php init_tail(); ?>
<script>
$(function() {
    // Inizializza tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Click sulla card seleziona il provider
    $('.provider-card').on('click', function() {
        var provider = $(this).data('provider');
        selectProvider(provider);
    });

    // Click sul bottone seleziona
    $('.btn-select-provider').on('click', function(e) {
        e.stopPropagation();
        var provider = $(this).data('provider');
        selectProvider(provider);
    });

    function selectProvider(provider) {
        // Rimuovi selezione precedente
        $('.provider-card').removeClass('selected');
        $('.btn-select-provider').text('<?php echo _l("fe_select"); ?>');

        // Seleziona nuovo
        $('.provider-card[data-provider="' + provider + '"]').addClass('selected');
        $('.btn-select-provider[data-provider="' + provider + '"]').text('<?php echo _l("fe_selected"); ?>');

        // Vai allo step 2
        window.location.href = '<?php echo admin_url("fatturazione_elettronica/setup/2?provider="); ?>' + provider;
    }
});
</script>
</body>
</html>

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <h4 class="tw-font-bold tw-m-0">
                        <i class="fa-solid fa-book tw-mr-2" aria-hidden="true"></i>
                        <?php echo _l('fe_documentazione'); ?>
                    </h4>
                    <div>
                        <span class="label label-primary tw-mr-2">v<?php echo $module_version; ?></span>
                        <a href="<?php echo admin_url('fatturazione_elettronica'); ?>" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left tw-mr-1"></i>
                            <?php echo _l('fe_dashboard'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Navigazione -->
            <div class="col-md-3">
                <div class="panel_s" id="sidebar-nav" style="position: sticky; top: 70px;">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-list tw-mr-2"></i>
                            <?php echo _l('fe_doc_indice'); ?>
                        </h5>
                        <ul class="nav nav-pills nav-stacked" id="doc-nav">
                            <li class="active"><a href="#intro"><?php echo _l('fe_doc_introduzione'); ?></a></li>
                            <li><a href="#quickstart"><?php echo _l('fe_doc_quickstart'); ?></a></li>
                            <li><a href="#requisiti"><?php echo _l('fe_doc_requisiti'); ?></a></li>
                            <li><a href="#configurazione"><?php echo _l('fe_doc_configurazione'); ?></a></li>
                            <li><a href="#providers"><?php echo _l('fe_doc_providers'); ?></a></li>
                            <li><a href="#fatture-attive"><?php echo _l('fe_doc_fatture_attive'); ?></a></li>
                            <li><a href="#fatture-passive"><?php echo _l('fe_doc_fatture_passive'); ?></a></li>
                            <li><a href="#clienti"><?php echo _l('fe_doc_clienti'); ?></a></li>
                            <li><a href="#webhook"><?php echo _l('fe_doc_webhook'); ?></a></li>
                            <li><a href="#stati"><?php echo _l('fe_doc_stati'); ?></a></li>
                            <li><a href="#troubleshooting"><?php echo _l('fe_doc_troubleshooting'); ?></a></li>
                            <li><a href="#faq"><?php echo _l('fe_doc_faq'); ?></a></li>
                        </ul>

                        <!-- Quick Links -->
                        <hr>
                        <h6 class="tw-font-semibold tw-mb-2 tw-text-gray-600">
                            <i class="fa fa-external-link tw-mr-1"></i>
                            Link Utili
                        </h6>
                        <ul class="tw-list-none tw-pl-0 tw-text-sm">
                            <li class="tw-mb-1">
                                <a href="<?php echo admin_url('fatturazione_elettronica/impostazioni'); ?>">
                                    <i class="fa fa-cog tw-mr-1"></i> Impostazioni
                                </a>
                            </li>
                            <li class="tw-mb-1">
                                <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive'); ?>">
                                    <i class="fa fa-file-invoice tw-mr-1"></i> Fatture Attive
                                </a>
                            </li>
                            <li class="tw-mb-1">
                                <a href="<?php echo admin_url('fatturazione_elettronica/fatture_passive'); ?>">
                                    <i class="fa fa-file-invoice-dollar tw-mr-1"></i> Fatture Passive
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contenuto Documentazione -->
            <div class="col-md-9">
                <!-- Introduzione -->
                <div class="panel_s" id="intro">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-info-circle tw-mr-2 tw-text-primary"></i>
                            <?php echo _l('fe_doc_introduzione'); ?>
                        </h4>
                        <p class="tw-text-lg"><?php echo _l('fe_doc_intro_text'); ?></p>

                        <div class="row tw-mt-4">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <strong><i class="fa fa-lightbulb"></i> <?php echo _l('fe_doc_funzionalita'); ?>:</strong>
                                    <ul class="tw-mb-0 tw-mt-2">
                                        <li><?php echo _l('fe_doc_func_1'); ?></li>
                                        <li><?php echo _l('fe_doc_func_2'); ?></li>
                                        <li><?php echo _l('fe_doc_func_3'); ?></li>
                                        <li><?php echo _l('fe_doc_func_4'); ?></li>
                                        <li><?php echo _l('fe_doc_func_5'); ?></li>
                                        <li><?php echo _l('fe_doc_func_6'); ?></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <strong><i class="fa fa-check-circle"></i> <?php echo _l('fe_doc_vantaggi'); ?>:</strong>
                                    <ul class="tw-mb-0 tw-mt-2">
                                        <li><?php echo _l('fe_doc_vantaggio_1'); ?></li>
                                        <li><?php echo _l('fe_doc_vantaggio_2'); ?></li>
                                        <li><?php echo _l('fe_doc_vantaggio_3'); ?></li>
                                        <li><?php echo _l('fe_doc_vantaggio_4'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Start -->
                <div class="panel_s" id="quickstart">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-rocket tw-mr-2 tw-text-success"></i>
                            <?php echo _l('fe_doc_quickstart'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_quickstart_text'); ?></p>

                        <div class="row tw-mt-4">
                            <div class="col-md-3 col-sm-6 tw-mb-3">
                                <div class="tw-text-center tw-p-4 tw-border tw-rounded">
                                    <div class="tw-text-3xl tw-text-primary tw-mb-2">1</div>
                                    <h5 class="tw-font-semibold"><?php echo _l('fe_doc_qs_step_1_title'); ?></h5>
                                    <p class="tw-text-sm tw-text-gray-600"><?php echo _l('fe_doc_qs_step_1_desc'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 tw-mb-3">
                                <div class="tw-text-center tw-p-4 tw-border tw-rounded">
                                    <div class="tw-text-3xl tw-text-primary tw-mb-2">2</div>
                                    <h5 class="tw-font-semibold"><?php echo _l('fe_doc_qs_step_2_title'); ?></h5>
                                    <p class="tw-text-sm tw-text-gray-600"><?php echo _l('fe_doc_qs_step_2_desc'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 tw-mb-3">
                                <div class="tw-text-center tw-p-4 tw-border tw-rounded">
                                    <div class="tw-text-3xl tw-text-primary tw-mb-2">3</div>
                                    <h5 class="tw-font-semibold"><?php echo _l('fe_doc_qs_step_3_title'); ?></h5>
                                    <p class="tw-text-sm tw-text-gray-600"><?php echo _l('fe_doc_qs_step_3_desc'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 tw-mb-3">
                                <div class="tw-text-center tw-p-4 tw-border tw-rounded">
                                    <div class="tw-text-3xl tw-text-primary tw-mb-2">4</div>
                                    <h5 class="tw-font-semibold"><?php echo _l('fe_doc_qs_step_4_title'); ?></h5>
                                    <p class="tw-text-sm tw-text-gray-600"><?php echo _l('fe_doc_qs_step_4_desc'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="tw-text-center tw-mt-4">
                            <a href="<?php echo admin_url('fatturazione_elettronica/setup/1'); ?>" class="btn btn-primary btn-lg">
                                <i class="fa fa-play tw-mr-2"></i>
                                <?php echo _l('fe_start_setup'); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Requisiti -->
                <div class="panel_s" id="requisiti">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-check-circle tw-mr-2 tw-text-success"></i>
                            <?php echo _l('fe_doc_requisiti'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_requisiti_text'); ?></p>

                        <div class="row tw-mt-3">
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('fe_doc_requisito'); ?></th>
                                            <th><?php echo _l('fe_doc_valore'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><i class="fa fa-server tw-mr-2 tw-text-info"></i> Perfex CRM</td>
                                            <td><span class="label label-success">3.0+</span></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fa fa-code tw-mr-2 tw-text-info"></i> PHP</td>
                                            <td><span class="label label-success">7.4+</span></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fa fa-puzzle-piece tw-mr-2 tw-text-info"></i> Estensioni PHP</td>
                                            <td>SimpleXML, DOM, cURL, OpenSSL</td>
                                        </tr>
                                        <tr>
                                            <td><i class="fa fa-plug tw-mr-2 tw-text-info"></i> <?php echo _l('fe_doc_provider_account'); ?></td>
                                            <td><?php echo _l('fe_doc_provider_account_desc'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning">
                                    <strong><i class="fa fa-exclamation-triangle"></i> <?php echo _l('fe_doc_requisiti_note'); ?>:</strong>
                                    <ul class="tw-mb-0 tw-mt-2">
                                        <li><?php echo _l('fe_doc_requisiti_note_1'); ?></li>
                                        <li><?php echo _l('fe_doc_requisiti_note_2'); ?></li>
                                        <li><?php echo _l('fe_doc_requisiti_note_3'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurazione -->
                <div class="panel_s" id="configurazione">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-cog tw-mr-2 tw-text-info"></i>
                            <?php echo _l('fe_doc_configurazione'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_config_text'); ?></p>

                        <div class="tw-mt-4">
                            <div class="tw-flex tw-items-start tw-mb-4">
                                <div class="tw-flex-shrink-0 tw-w-10 tw-h-10 tw-rounded-full tw-bg-primary tw-text-white tw-flex tw-items-center tw-justify-center tw-mr-3">
                                    <span class="tw-font-bold">1</span>
                                </div>
                                <div>
                                    <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('fe_doc_step_1'); ?></h5>
                                    <p class="tw-text-gray-600"><?php echo _l('fe_doc_step_1_text'); ?></p>
                                </div>
                            </div>

                            <div class="tw-flex tw-items-start tw-mb-4">
                                <div class="tw-flex-shrink-0 tw-w-10 tw-h-10 tw-rounded-full tw-bg-primary tw-text-white tw-flex tw-items-center tw-justify-center tw-mr-3">
                                    <span class="tw-font-bold">2</span>
                                </div>
                                <div>
                                    <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('fe_doc_step_2'); ?></h5>
                                    <p class="tw-text-gray-600"><?php echo _l('fe_doc_step_2_text'); ?></p>
                                </div>
                            </div>

                            <div class="tw-flex tw-items-start tw-mb-4">
                                <div class="tw-flex-shrink-0 tw-w-10 tw-h-10 tw-rounded-full tw-bg-primary tw-text-white tw-flex tw-items-center tw-justify-center tw-mr-3">
                                    <span class="tw-font-bold">3</span>
                                </div>
                                <div>
                                    <h5 class="tw-font-semibold tw-mb-1"><?php echo _l('fe_doc_step_3'); ?></h5>
                                    <p class="tw-text-gray-600"><?php echo _l('fe_doc_step_3_text'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning tw-mt-3">
                            <strong><i class="fa fa-exclamation-triangle"></i> <?php echo _l('fe_doc_importante'); ?>:</strong>
                            <p class="tw-mb-0"><?php echo _l('fe_doc_config_warning'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Provider -->
                <div class="panel_s" id="providers">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-plug tw-mr-2 tw-text-warning"></i>
                            <?php echo _l('fe_doc_providers'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_providers_text'); ?></p>

                        <div class="row tw-mt-4">
                            <?php foreach ($providers as $provider_id => $provider): ?>
                            <div class="col-md-6 tw-mb-3">
                                <div class="tw-border tw-rounded tw-p-3 tw-h-full <?php echo $current_provider == $provider_id ? 'tw-border-green-500 tw-bg-green-50' : ''; ?>">
                                    <div class="tw-flex tw-items-center tw-mb-2">
                                        <div class="tw-p-2 tw-rounded tw-mr-3" style="background-color: <?php echo $provider['color'] ?? '#333'; ?>">
                                            <i class="fa <?php echo $provider['icon'] ?? 'fa-plug'; ?> tw-text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="tw-font-semibold tw-mb-0">
                                                <?php echo $provider['name']; ?>
                                                <?php if ($current_provider == $provider_id): ?>
                                                <span class="label label-success tw-ml-2"><?php echo _l('fe_doc_attivo'); ?></span>
                                                <?php endif; ?>
                                            </h5>
                                        </div>
                                    </div>
                                    <p class="tw-text-sm tw-text-gray-600 tw-mb-2"><?php echo $provider['description']; ?></p>
                                    <?php if (!empty($provider['features'])): ?>
                                    <ul class="tw-text-xs tw-text-gray-500 tw-mb-0 tw-pl-4">
                                        <?php foreach (array_slice($provider['features'], 0, 4) as $feature): ?>
                                        <li><?php echo $feature; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Fatture Attive -->
                <div class="panel_s" id="fatture-attive">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-file-invoice tw-mr-2 tw-text-primary"></i>
                            <?php echo _l('fe_doc_fatture_attive'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_fatture_attive_text'); ?></p>

                        <div class="row tw-mt-4">
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold">
                                    <i class="fa fa-file-code tw-mr-2 tw-text-info"></i>
                                    <?php echo _l('fe_doc_generazione_xml'); ?>
                                </h5>
                                <ol class="tw-pl-4">
                                    <li class="tw-mb-2"><?php echo _l('fe_doc_gen_step_1'); ?></li>
                                    <li class="tw-mb-2"><?php echo _l('fe_doc_gen_step_2'); ?></li>
                                    <li class="tw-mb-2"><?php echo _l('fe_doc_gen_step_3'); ?></li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold">
                                    <i class="fa fa-paper-plane tw-mr-2 tw-text-success"></i>
                                    <?php echo _l('fe_doc_invio_sdi'); ?>
                                </h5>
                                <p><?php echo _l('fe_doc_invio_sdi_text'); ?></p>
                            </div>
                        </div>

                        <div class="alert alert-success tw-mt-3">
                            <strong><i class="fa fa-magic"></i> <?php echo _l('fe_doc_automazione'); ?>:</strong>
                            <p class="tw-mb-0"><?php echo _l('fe_doc_automazione_text'); ?></p>
                        </div>

                        <div class="alert alert-info tw-mt-3">
                            <strong><i class="fa fa-keyboard"></i> <?php echo _l('fe_doc_azioni_disponibili'); ?>:</strong>
                            <div class="row tw-mt-2">
                                <div class="col-md-4">
                                    <ul class="tw-mb-0">
                                        <li><strong><?php echo _l('fe_download_xml'); ?></strong> - <?php echo _l('fe_doc_azione_download'); ?></li>
                                        <li><strong><?php echo _l('fe_invia_sdi'); ?></strong> - <?php echo _l('fe_doc_azione_invia'); ?></li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <ul class="tw-mb-0">
                                        <li><strong><?php echo _l('fe_rigenera_xml'); ?></strong> - <?php echo _l('fe_doc_azione_rigenera'); ?></li>
                                        <li><strong><?php echo _l('fe_verifica_stato'); ?></strong> - <?php echo _l('fe_doc_azione_verifica'); ?></li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <ul class="tw-mb-0">
                                        <li><strong><?php echo _l('fe_elimina'); ?></strong> - <?php echo _l('fe_doc_azione_elimina'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fatture Passive -->
                <div class="panel_s" id="fatture-passive">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-file-invoice-dollar tw-mr-2 tw-text-danger"></i>
                            <?php echo _l('fe_doc_fatture_passive'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_fatture_passive_text'); ?></p>

                        <div class="row tw-mt-4">
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold">
                                    <i class="fa fa-sync tw-mr-2 tw-text-info"></i>
                                    <?php echo _l('fe_doc_sincronizzazione'); ?>
                                </h5>
                                <p><?php echo _l('fe_doc_sincronizzazione_text'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold">
                                    <i class="fa fa-tasks tw-mr-2 tw-text-warning"></i>
                                    <?php echo _l('fe_doc_elaborazione'); ?>
                                </h5>
                                <ul>
                                    <li><strong><?php echo _l('fe_crea_spesa'); ?>:</strong> <?php echo _l('fe_doc_crea_spesa_desc'); ?></li>
                                    <li><strong><?php echo _l('fe_archivia'); ?>:</strong> <?php echo _l('fe_doc_archivia_desc'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurazione Clienti -->
                <div class="panel_s" id="clienti">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-users tw-mr-2 tw-text-info"></i>
                            <?php echo _l('fe_doc_clienti'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_clienti_text'); ?></p>

                        <div class="row tw-mt-4">
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold"><?php echo _l('fe_doc_clienti_campi'); ?></h5>
                                <table class="table table-striped table-condensed">
                                    <tr>
                                        <td><strong><?php echo _l('fe_codice_destinatario'); ?></strong></td>
                                        <td><?php echo _l('fe_doc_cliente_cod_dest'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_pec'); ?></strong></td>
                                        <td><?php echo _l('fe_doc_cliente_pec'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_split_payment'); ?></strong></td>
                                        <td><?php echo _l('fe_doc_cliente_split'); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <strong><i class="fa fa-info-circle"></i> <?php echo _l('fe_doc_clienti_nota'); ?>:</strong>
                                    <p class="tw-mb-0"><?php echo _l('fe_doc_clienti_nota_text'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Webhook -->
                <div class="panel_s" id="webhook">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-bolt tw-mr-2 tw-text-warning"></i>
                            <?php echo _l('fe_doc_webhook'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_webhook_text'); ?></p>

                        <div class="form-group tw-mt-4">
                            <label><strong><?php echo _l('fe_webhook_url'); ?></strong></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="webhook-url" value="<?php echo $webhook_url; ?>" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" onclick="copyWebhookUrl()">
                                        <i class="fa fa-copy tw-mr-1"></i> <?php echo _l('fe_doc_copia'); ?>
                                    </button>
                                </span>
                            </div>
                        </div>

                        <div class="alert alert-info tw-mt-3">
                            <strong><i class="fa fa-info-circle"></i> <?php echo _l('fe_doc_webhook_config'); ?>:</strong>
                            <ol class="tw-mb-0 tw-mt-2">
                                <li><?php echo _l('fe_doc_webhook_step_1'); ?></li>
                                <li><?php echo _l('fe_doc_webhook_step_2'); ?></li>
                                <li><?php echo _l('fe_doc_webhook_step_3'); ?></li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Stati Fattura -->
                <div class="panel_s" id="stati">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-tags tw-mr-2 tw-text-info"></i>
                            <?php echo _l('fe_doc_stati'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_stati_text'); ?></p>

                        <!-- Flusso visivo degli stati -->
                        <div class="tw-mt-4 tw-p-4 tw-bg-gray-50 tw-rounded">
                            <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_doc_flusso_stati'); ?></h5>
                            <div class="tw-flex tw-items-center tw-flex-wrap tw-gap-2">
                                <span class="label label-default"><?php echo _l('fe_stato_bozza'); ?></span>
                                <i class="fa fa-arrow-right tw-text-gray-400"></i>
                                <span class="label label-info"><?php echo _l('fe_stato_generata'); ?></span>
                                <i class="fa fa-arrow-right tw-text-gray-400"></i>
                                <span class="label label-primary"><?php echo _l('fe_stato_inviata'); ?></span>
                                <i class="fa fa-arrow-right tw-text-gray-400"></i>
                                <span class="label label-success"><?php echo _l('fe_stato_consegnata'); ?></span>
                                <i class="fa fa-arrow-right tw-text-gray-400"></i>
                                <span class="label label-success"><?php echo _l('fe_stato_accettata'); ?></span>
                            </div>
                        </div>

                        <table class="table table-striped tw-mt-4">
                            <thead>
                                <tr>
                                    <th width="180"><?php echo _l('fe_stato'); ?></th>
                                    <th><?php echo _l('fe_descrizione'); ?></th>
                                    <th width="120"><?php echo _l('fe_doc_azione_richiesta'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="label label-default"><?php echo _l('fe_stato_bozza'); ?></span></td>
                                    <td><?php echo _l('fe_stato_bozza_tooltip'); ?></td>
                                    <td><span class="text-info"><?php echo _l('fe_doc_genera'); ?></span></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-info"><?php echo _l('fe_stato_generata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_generata_tooltip'); ?></td>
                                    <td><span class="text-success"><?php echo _l('fe_doc_invia'); ?></span></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-primary"><?php echo _l('fe_stato_inviata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_inviata_tooltip'); ?></td>
                                    <td><span class="text-muted"><?php echo _l('fe_doc_attendi'); ?></span></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-success"><?php echo _l('fe_stato_consegnata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_consegnata_tooltip'); ?></td>
                                    <td><span class="text-muted">-</span></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-warning"><?php echo _l('fe_stato_non_consegnata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_non_consegnata_tooltip'); ?></td>
                                    <td><span class="text-muted">-</span></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-success"><?php echo _l('fe_stato_accettata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_accettata_tooltip'); ?></td>
                                    <td><span class="text-muted">-</span></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-danger"><?php echo _l('fe_stato_rifiutata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_rifiutata_tooltip'); ?></td>
                                    <td><span class="text-warning"><?php echo _l('fe_doc_contatta_cliente'); ?></span></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-danger"><?php echo _l('fe_stato_scartata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_scartata_tooltip'); ?></td>
                                    <td><span class="text-danger"><?php echo _l('fe_doc_correggi'); ?></span></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-info"><?php echo _l('fe_stato_decorrenza_termini'); ?></span></td>
                                    <td><?php echo _l('fe_stato_decorrenza_termini_tooltip'); ?></td>
                                    <td><span class="text-muted">-</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Troubleshooting -->
                <div class="panel_s" id="troubleshooting">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-wrench tw-mr-2 tw-text-warning"></i>
                            <?php echo _l('fe_doc_troubleshooting'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_troubleshooting_text'); ?></p>

                        <div class="panel-group tw-mt-4" id="troubleshooting-accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#troubleshooting-accordion" href="#ts1">
                                            <i class="fa fa-exclamation-circle tw-text-danger tw-mr-2"></i>
                                            <?php echo _l('fe_doc_ts_1_title'); ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="ts1" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <p><?php echo _l('fe_doc_ts_1_text'); ?></p>
                                        <ul>
                                            <li><?php echo _l('fe_doc_ts_1_sol_1'); ?></li>
                                            <li><?php echo _l('fe_doc_ts_1_sol_2'); ?></li>
                                            <li><?php echo _l('fe_doc_ts_1_sol_3'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#troubleshooting-accordion" href="#ts2" class="collapsed">
                                            <i class="fa fa-exclamation-circle tw-text-warning tw-mr-2"></i>
                                            <?php echo _l('fe_doc_ts_2_title'); ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="ts2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <p><?php echo _l('fe_doc_ts_2_text'); ?></p>
                                        <ul>
                                            <li><?php echo _l('fe_doc_ts_2_sol_1'); ?></li>
                                            <li><?php echo _l('fe_doc_ts_2_sol_2'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#troubleshooting-accordion" href="#ts3" class="collapsed">
                                            <i class="fa fa-exclamation-circle tw-text-info tw-mr-2"></i>
                                            <?php echo _l('fe_doc_ts_3_title'); ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="ts3" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <p><?php echo _l('fe_doc_ts_3_text'); ?></p>
                                        <ul>
                                            <li><?php echo _l('fe_doc_ts_3_sol_1'); ?></li>
                                            <li><?php echo _l('fe_doc_ts_3_sol_2'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="panel_s" id="faq">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-question-circle tw-mr-2 tw-text-primary"></i>
                            <?php echo _l('fe_doc_faq'); ?>
                        </h4>

                        <div class="panel-group" id="accordion">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#faq<?php echo $i; ?>" <?php echo $i > 1 ? 'class="collapsed"' : ''; ?>>
                                            <?php echo _l('fe_doc_faq_' . $i . '_q'); ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="faq<?php echo $i; ?>" class="panel-collapse collapse <?php echo $i == 1 ? 'in' : ''; ?>">
                                    <div class="panel-body">
                                        <?php echo _l('fe_doc_faq_' . $i . '_a'); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <!-- Supporto -->
                <div class="panel_s" id="supporto">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="tw-font-semibold tw-mb-3">
                                    <i class="fa fa-life-ring tw-mr-2 tw-text-primary"></i>
                                    <?php echo _l('fe_doc_supporto'); ?>
                                </h4>
                                <p><?php echo _l('fe_doc_supporto_text'); ?></p>

                                <div class="row tw-mt-4">
                                    <div class="col-sm-6 tw-mb-3">
                                        <div class="tw-flex tw-items-center">
                                            <div class="tw-p-3 tw-bg-primary tw-rounded tw-mr-3">
                                                <i class="fa fa-envelope tw-text-white tw-text-xl"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo _l('fe_doc_email_supporto'); ?></strong><br>
                                                <a href="mailto:support@gtechgroup.it">support@gtechgroup.it</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 tw-mb-3">
                                        <div class="tw-flex tw-items-center">
                                            <div class="tw-p-3 tw-bg-info tw-rounded tw-mr-3">
                                                <i class="fa fa-globe tw-text-white tw-text-xl"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo _l('fe_doc_sito_web'); ?></strong><br>
                                                <a href="https://gtechgroup.it" target="_blank">gtechgroup.it</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 tw-text-center">
                                <div class="tw-p-4 tw-bg-gray-50 tw-rounded">
                                    <img src="https://gtechgroup.it/wp-content/uploads/2023/03/logo-gtech-group.png"
                                         alt="G Tech Group"
                                         style="max-width: 180px; height: auto;"
                                         onerror="this.style.display='none'">
                                    <h5 class="tw-font-bold tw-mt-3">G Tech Group</h5>
                                    <p class="tw-text-sm tw-text-gray-600 tw-mb-3"><?php echo _l('fe_doc_sviluppato_da'); ?></p>
                                    <a href="https://gtechgroup.it" target="_blank" class="btn btn-primary">
                                        <i class="fa fa-external-link tw-mr-1"></i>
                                        <?php echo _l('fe_doc_visita_sito'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
$(function() {
    // Inizializza tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Smooth scroll per la navigazione
    $('#doc-nav a').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 80
        }, 500);
        $('#doc-nav li').removeClass('active');
        $(this).parent().addClass('active');
    });

    // Highlight sezione corrente durante lo scroll
    $(window).on('scroll', function() {
        var scrollPos = $(window).scrollTop() + 100;
        $('#doc-nav a').each(function() {
            var target = $(this).attr('href');
            var section = $(target);
            if (section.length && section.offset().top <= scrollPos && section.offset().top + section.outerHeight() > scrollPos) {
                $('#doc-nav li').removeClass('active');
                $(this).parent().addClass('active');
            }
        });
    });
});

function copyWebhookUrl() {
    var input = document.getElementById('webhook-url');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(function() {
        alert_float('success', '<?php echo _l('fe_copied'); ?>');
    }).catch(function() {
        document.execCommand('copy');
        alert_float('success', '<?php echo _l('fe_copied'); ?>');
    });
}
</script>
</body>
</html>

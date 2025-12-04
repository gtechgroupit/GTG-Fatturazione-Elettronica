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
                    <span class="label label-default">v<?php echo $module_version; ?></span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Navigazione -->
            <div class="col-md-3">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-list tw-mr-2"></i>
                            <?php echo _l('fe_doc_indice'); ?>
                        </h5>
                        <ul class="nav nav-pills nav-stacked" id="doc-nav">
                            <li class="active"><a href="#intro"><?php echo _l('fe_doc_introduzione'); ?></a></li>
                            <li><a href="#requisiti"><?php echo _l('fe_doc_requisiti'); ?></a></li>
                            <li><a href="#configurazione"><?php echo _l('fe_doc_configurazione'); ?></a></li>
                            <li><a href="#providers"><?php echo _l('fe_doc_providers'); ?></a></li>
                            <li><a href="#fatture-attive"><?php echo _l('fe_doc_fatture_attive'); ?></a></li>
                            <li><a href="#fatture-passive"><?php echo _l('fe_doc_fatture_passive'); ?></a></li>
                            <li><a href="#webhook"><?php echo _l('fe_doc_webhook'); ?></a></li>
                            <li><a href="#stati"><?php echo _l('fe_doc_stati'); ?></a></li>
                            <li><a href="#faq"><?php echo _l('fe_doc_faq'); ?></a></li>
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
                        <p><?php echo _l('fe_doc_intro_text'); ?></p>

                        <div class="alert alert-info tw-mt-3">
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
                </div>

                <!-- Requisiti -->
                <div class="panel_s" id="requisiti">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-check-circle tw-mr-2 tw-text-success"></i>
                            <?php echo _l('fe_doc_requisiti'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_requisiti_text'); ?></p>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo _l('fe_doc_requisito'); ?></th>
                                    <th><?php echo _l('fe_doc_valore'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Perfex CRM</td>
                                    <td>3.0+</td>
                                </tr>
                                <tr>
                                    <td>PHP</td>
                                    <td>7.4+</td>
                                </tr>
                                <tr>
                                    <td>Estensioni PHP</td>
                                    <td>SimpleXML, DOM, cURL, OpenSSL</td>
                                </tr>
                                <tr>
                                    <td><?php echo _l('fe_doc_provider_account'); ?></td>
                                    <td><?php echo _l('fe_doc_provider_account_desc'); ?></td>
                                </tr>
                            </tbody>
                        </table>
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

                        <h5 class="tw-font-semibold tw-mt-4"><?php echo _l('fe_doc_step_1'); ?></h5>
                        <p><?php echo _l('fe_doc_step_1_text'); ?></p>

                        <h5 class="tw-font-semibold tw-mt-4"><?php echo _l('fe_doc_step_2'); ?></h5>
                        <p><?php echo _l('fe_doc_step_2_text'); ?></p>

                        <h5 class="tw-font-semibold tw-mt-4"><?php echo _l('fe_doc_step_3'); ?></h5>
                        <p><?php echo _l('fe_doc_step_3_text'); ?></p>

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
                                <div class="tw-border tw-rounded tw-p-3 <?php echo $current_provider == $provider_id ? 'tw-border-green-500 tw-bg-green-50' : ''; ?>">
                                    <h5 class="tw-font-semibold">
                                        <i class="fa <?php echo $provider['icon'] ?? 'fa-plug'; ?> tw-mr-2"></i>
                                        <?php echo $provider['name']; ?>
                                        <?php if ($current_provider == $provider_id): ?>
                                        <span class="label label-success"><?php echo _l('fe_doc_attivo'); ?></span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="tw-text-sm tw-text-gray-600"><?php echo $provider['description']; ?></p>
                                    <?php if (!empty($provider['features'])): ?>
                                    <ul class="tw-text-xs tw-text-gray-500 tw-mb-0">
                                        <?php foreach (array_slice($provider['features'], 0, 3) as $feature): ?>
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

                        <h5 class="tw-font-semibold tw-mt-4"><?php echo _l('fe_doc_generazione_xml'); ?></h5>
                        <ol>
                            <li><?php echo _l('fe_doc_gen_step_1'); ?></li>
                            <li><?php echo _l('fe_doc_gen_step_2'); ?></li>
                            <li><?php echo _l('fe_doc_gen_step_3'); ?></li>
                        </ol>

                        <h5 class="tw-font-semibold tw-mt-4"><?php echo _l('fe_doc_invio_sdi'); ?></h5>
                        <p><?php echo _l('fe_doc_invio_sdi_text'); ?></p>

                        <div class="alert alert-success tw-mt-3">
                            <strong><i class="fa fa-magic"></i> <?php echo _l('fe_doc_automazione'); ?>:</strong>
                            <p class="tw-mb-0"><?php echo _l('fe_doc_automazione_text'); ?></p>
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

                        <h5 class="tw-font-semibold tw-mt-4"><?php echo _l('fe_doc_sincronizzazione'); ?></h5>
                        <p><?php echo _l('fe_doc_sincronizzazione_text'); ?></p>

                        <h5 class="tw-font-semibold tw-mt-4"><?php echo _l('fe_doc_elaborazione'); ?></h5>
                        <ul>
                            <li><strong><?php echo _l('fe_crea_spesa'); ?>:</strong> <?php echo _l('fe_doc_crea_spesa_desc'); ?></li>
                            <li><strong><?php echo _l('fe_archivia'); ?>:</strong> <?php echo _l('fe_doc_archivia_desc'); ?></li>
                        </ul>
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

                        <div class="form-group">
                            <label><?php echo _l('fe_webhook_url'); ?></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="webhook-url" value="<?php echo $webhook_url; ?>" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" onclick="copyWebhookUrl()">
                                        <i class="fa fa-copy"></i>
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

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo _l('fe_stato'); ?></th>
                                    <th><?php echo _l('fe_descrizione'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="label label-default"><?php echo _l('fe_stato_bozza'); ?></span></td>
                                    <td><?php echo _l('fe_stato_bozza_tooltip'); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-info"><?php echo _l('fe_stato_generata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_generata_tooltip'); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-primary"><?php echo _l('fe_stato_inviata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_inviata_tooltip'); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-success"><?php echo _l('fe_stato_consegnata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_consegnata_tooltip'); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-warning"><?php echo _l('fe_stato_non_consegnata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_non_consegnata_tooltip'); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-success"><?php echo _l('fe_stato_accettata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_accettata_tooltip'); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-danger"><?php echo _l('fe_stato_rifiutata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_rifiutata_tooltip'); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-danger"><?php echo _l('fe_stato_scartata'); ?></span></td>
                                    <td><?php echo _l('fe_stato_scartata_tooltip'); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="label label-info"><?php echo _l('fe_stato_decorrenza_termini'); ?></span></td>
                                    <td><?php echo _l('fe_stato_decorrenza_termini_tooltip'); ?></td>
                                </tr>
                            </tbody>
                        </table>
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
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#faq1">
                                            <?php echo _l('fe_doc_faq_1_q'); ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="faq1" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <?php echo _l('fe_doc_faq_1_a'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#faq2">
                                            <?php echo _l('fe_doc_faq_2_q'); ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="faq2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?php echo _l('fe_doc_faq_2_a'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#faq3">
                                            <?php echo _l('fe_doc_faq_3_q'); ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="faq3" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?php echo _l('fe_doc_faq_3_a'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#faq4">
                                            <?php echo _l('fe_doc_faq_4_q'); ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="faq4" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?php echo _l('fe_doc_faq_4_a'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#faq5">
                                            <?php echo _l('fe_doc_faq_5_q'); ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="faq5" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?php echo _l('fe_doc_faq_5_a'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supporto -->
                <div class="panel_s">
                    <div class="panel-body tw-text-center">
                        <h4 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-life-ring tw-mr-2 tw-text-primary"></i>
                            <?php echo _l('fe_doc_supporto'); ?>
                        </h4>
                        <p><?php echo _l('fe_doc_supporto_text'); ?></p>
                        <a href="mailto:support@gtechgroupit.com" class="btn btn-primary">
                            <i class="fa fa-envelope tw-mr-2"></i>
                            <?php echo _l('fe_doc_contatta_supporto'); ?>
                        </a>
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
    document.execCommand('copy');
    alert_float('success', '<?php echo _l('fe_copied'); ?>');
}
</script>
</body>
</html>

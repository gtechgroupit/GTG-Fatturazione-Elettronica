<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Helper per accesso sicuro alle statistiche
$stats_attive = isset($statistics['attive']) ? $statistics['attive'] : [];
$stats_passive = isset($statistics['passive']) ? $statistics['passive'] : [];

// Valori di default per le statistiche
$totale_attive = isset($stats_attive['totale']) ? $stats_attive['totale'] : 0;
$consegnate = isset($stats_attive['consegnate']) ? $stats_attive['consegnate'] : 0;
$inviate = isset($stats_attive['inviate']) ? $stats_attive['inviate'] : 0;
$errori = isset($stats_attive['errori']) ? $stats_attive['errori'] : 0;

$totale_passive = isset($stats_passive['totale']) ? $stats_passive['totale'] : 0;
$non_lette = isset($stats_passive['non_lette']) ? $stats_passive['non_lette'] : 0;
$da_processare = isset($stats_passive['da_processare']) ? $stats_passive['da_processare'] : 0;

// Array sicuri
$fatture_da_inviare = isset($fatture_da_inviare) ? $fatture_da_inviare : [];
$recent_logs = isset($recent_logs) ? $recent_logs : [];
$fatture_passive_recenti = isset($fatture_passive_recenti) ? $fatture_passive_recenti : [];
$invoices_to_import = isset($invoices_to_import) ? $invoices_to_import : [];
$chart_labels = isset($chart_labels) ? $chart_labels : ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
$chart_data = isset($chart_data) ? $chart_data : ['inviate' => array_fill(0, 12, 0), 'ricevute' => array_fill(0, 12, 0)];
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <h4 class="tw-font-bold tw-text-xl tw-mb-6">
                    <i class="fa-solid fa-file-invoice tw-mr-2"></i>
                    <?php echo _l('fe_dashboard'); ?>
                </h4>
            </div>
            <div class="col-md-4 tw-text-right">
                <span class="text-muted"><?php echo _l('fe_last_update'); ?>: <?php echo _dt(date('Y-m-d H:i:s')); ?></span>
            </div>
        </div>

        <?php if (empty(get_option('fe_setup_completed'))): ?>
        <!-- Banner Setup -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info tw-flex tw-items-center tw-justify-between">
                    <div>
                        <h5 class="tw-font-bold tw-mb-2">
                            <i class="fa fa-rocket tw-mr-2"></i>
                            <?php echo _l('fe_welcome_title'); ?>
                        </h5>
                        <p class="tw-mb-0"><?php echo _l('fe_welcome_message'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/setup/1'); ?>" class="btn btn-primary btn-lg">
                        <i class="fa fa-play tw-mr-2"></i>
                        <?php echo _l('fe_start_setup'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Provider Status Card -->
        <div class="row tw-mb-4">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8 col-sm-12">
                                <div class="tw-flex tw-items-center">
                                    <div class="tw-p-4 tw-rounded-lg tw-mr-4 hidden-xs" style="background-color: <?php echo isset($provider_info['color']) ? $provider_info['color'] : '#6c757d'; ?>">
                                        <i class="fa <?php echo isset($provider_info['icon']) ? $provider_info['icon'] : 'fa-plug'; ?> tw-text-3xl tw-text-white" aria-hidden="true"></i>
                                    </div>
                                    <div>
                                        <h4 class="tw-font-bold tw-mb-1"><?php echo isset($provider_info['name']) ? $provider_info['name'] : _l('fe_provider_test'); ?></h4>
                                        <p class="tw-text-gray-600 tw-mb-1 hidden-xs"><?php echo isset($provider_info['description']) ? $provider_info['description'] : ''; ?></p>
                                        <div class="tw-mt-2">
                                            <span class="label label-<?php echo !empty($provider_configured) ? 'success' : 'warning'; ?>">
                                                <i class="fa <?php echo !empty($provider_configured) ? 'fa-check' : 'fa-exclamation-triangle'; ?> tw-mr-1" aria-hidden="true"></i>
                                                <?php echo !empty($provider_configured) ? _l('fe_provider_configured') : _l('fe_provider_not_configured'); ?>
                                            </span>
                                            <?php if (get_option('fe_ambiente') == 'test'): ?>
                                            <span class="label label-info tw-ml-2" data-toggle="tooltip" title="<?php echo _l('fe_ambiente_test_tooltip'); ?>">
                                                <i class="fa fa-flask tw-mr-1" aria-hidden="true"></i>
                                                <?php echo _l('fe_ambiente_test'); ?>
                                            </span>
                                            <?php else: ?>
                                            <span class="label label-success tw-ml-2">
                                                <i class="fa fa-check-circle tw-mr-1" aria-hidden="true"></i>
                                                <?php echo _l('fe_ambiente_produzione'); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 tw-mt-3 md:tw-mt-0">
                                <div class="tw-flex tw-flex-wrap tw-gap-2 tw-justify-end">
                                    <button type="button" class="btn btn-default" id="btn-test-connection" title="<?php echo _l('fe_test_connessione_tooltip'); ?>">
                                        <i class="fa fa-plug tw-mr-1" aria-hidden="true"></i>
                                        <span class="hidden-xs"><?php echo _l('fe_test_connessione'); ?></span>
                                    </button>
                                    <a href="<?php echo admin_url('fatturazione_elettronica/setup/1'); ?>" class="btn btn-default" title="<?php echo _l('fe_change_provider_tooltip'); ?>">
                                        <i class="fa fa-cog tw-mr-1" aria-hidden="true"></i>
                                        <span class="hidden-xs"><?php echo _l('fe_change_provider'); ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiche Principali -->
        <div class="row">
            <!-- Fatture Attive -->
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="panel panel-primary" data-toggle="tooltip" data-placement="top" title="<?php echo _l('fe_totale_fatture_attive_tooltip'); ?>">
                    <div class="panel-body tw-text-center">
                        <i class="fa fa-file-invoice tw-text-4xl tw-text-primary tw-mb-2" aria-hidden="true"></i>
                        <h3 class="tw-font-bold"><?php echo $totale_attive; ?></h3>
                        <p class="text-muted tw-mb-0"><?php echo _l('fe_totale_fatture_attive'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive'); ?>" class="panel-footer tw-text-center" aria-label="<?php echo _l('fe_vedi_tutte'); ?> - <?php echo _l('fe_totale_fatture_attive'); ?>">
                        <?php echo _l('fe_vedi_tutte'); ?>
                        <i class="fa fa-arrow-right tw-ml-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="panel panel-success" data-toggle="tooltip" data-placement="top" title="<?php echo _l('fe_consegnate_tooltip'); ?>">
                    <div class="panel-body tw-text-center">
                        <i class="fa fa-check-circle tw-text-4xl tw-text-success tw-mb-2" aria-hidden="true"></i>
                        <h3 class="tw-font-bold"><?php echo $consegnate; ?></h3>
                        <p class="text-muted tw-mb-0"><?php echo _l('fe_consegnate'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive?stato=consegnata'); ?>" class="panel-footer tw-text-center" aria-label="<?php echo _l('fe_vedi_dettaglio'); ?> - <?php echo _l('fe_consegnate'); ?>">
                        <?php echo _l('fe_vedi_dettaglio'); ?>
                        <i class="fa fa-arrow-right tw-ml-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="panel panel-warning" data-toggle="tooltip" data-placement="top" title="<?php echo _l('fe_in_attesa_tooltip'); ?>">
                    <div class="panel-body tw-text-center">
                        <i class="fa fa-clock tw-text-4xl tw-text-warning tw-mb-2" aria-hidden="true"></i>
                        <h3 class="tw-font-bold"><?php echo $inviate; ?></h3>
                        <p class="text-muted tw-mb-0"><?php echo _l('fe_in_attesa'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive?stato=inviata'); ?>" class="panel-footer tw-text-center" aria-label="<?php echo _l('fe_vedi_dettaglio'); ?> - <?php echo _l('fe_in_attesa'); ?>">
                        <?php echo _l('fe_vedi_dettaglio'); ?>
                        <i class="fa fa-arrow-right tw-ml-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="panel panel-danger" data-toggle="tooltip" data-placement="top" title="<?php echo _l('fe_con_errori_tooltip'); ?>">
                    <div class="panel-body tw-text-center">
                        <i class="fa fa-exclamation-circle tw-text-4xl tw-text-danger tw-mb-2" aria-hidden="true"></i>
                        <h3 class="tw-font-bold"><?php echo $errori; ?></h3>
                        <p class="text-muted tw-mb-0"><?php echo _l('fe_con_errori'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive?stato=scartata'); ?>" class="panel-footer tw-text-center" aria-label="<?php echo _l('fe_vedi_dettaglio'); ?> - <?php echo _l('fe_con_errori'); ?>">
                        <?php echo _l('fe_vedi_dettaglio'); ?>
                        <i class="fa fa-arrow-right tw-ml-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Grafico Andamento Mensile -->
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4">
                            <i class="fa fa-chart-line tw-mr-2"></i>
                            <?php echo _l('fe_andamento_mensile'); ?>
                        </h4>
                        <canvas id="chartAndamento" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Riepilogo Fatture Passive -->
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4">
                            <i class="fa fa-inbox tw-mr-2"></i>
                            <?php echo _l('fe_fatture_passive'); ?>
                        </h4>

                        <div class="tw-flex tw-justify-between tw-items-center tw-py-3 tw-border-b">
                            <span><?php echo _l('fe_totale'); ?></span>
                            <span class="tw-font-bold tw-text-xl"><?php echo $totale_passive; ?></span>
                        </div>

                        <div class="tw-flex tw-justify-between tw-items-center tw-py-3 tw-border-b">
                            <span>
                                <span class="tw-inline-block tw-w-3 tw-h-3 tw-rounded-full tw-bg-warning tw-mr-2" aria-hidden="true"></span>
                                <?php echo _l('fe_non_lette'); ?>
                            </span>
                            <span class="tw-font-bold"><?php echo $non_lette; ?></span>
                        </div>

                        <div class="tw-flex tw-justify-between tw-items-center tw-py-3 tw-border-b">
                            <span>
                                <span class="tw-inline-block tw-w-3 tw-h-3 tw-rounded-full tw-bg-info tw-mr-2" aria-hidden="true"></span>
                                <?php echo _l('fe_da_processare'); ?>
                            </span>
                            <span class="tw-font-bold"><?php echo $da_processare; ?></span>
                        </div>

                        <div class="tw-mt-4">
                            <a href="<?php echo admin_url('fatturazione_elettronica/fatture_passive'); ?>" class="btn btn-info btn-block">
                                <?php echo _l('fe_vedi_tutte'); ?>
                            </a>
                            <a href="<?php echo admin_url('fatturazione_elettronica/sync_passive'); ?>" class="btn btn-default btn-block">
                                <i class="fa fa-sync tw-mr-1"></i>
                                <?php echo _l('fe_sincronizza'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Azioni Rapide -->
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4">
                            <i class="fa fa-bolt tw-mr-2"></i>
                            <?php echo _l('fe_azioni_rapide'); ?>
                        </h4>
                        <div class="tw-space-y-2">
                            <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal-genera-fattura">
                                <i class="fa fa-plus tw-mr-2"></i>
                                <?php echo _l('fe_genera_fattura'); ?>
                            </a>
                            <?php if (!empty($fatture_da_inviare)): ?>
                            <a href="<?php echo admin_url('fatturazione_elettronica/invia_tutte_generate'); ?>"
                               class="btn btn-success btn-block"
                               onclick="return confirm('<?php echo _l('fe_confirm_bulk_send'); ?>');">
                                <i class="fa fa-paper-plane tw-mr-2"></i>
                                <?php echo _l('fe_invia_tutte'); ?> (<?php echo count($fatture_da_inviare); ?>)
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo admin_url('fatturazione_elettronica/verifica_stati'); ?>" class="btn btn-default btn-block">
                                <i class="fa fa-refresh tw-mr-2"></i>
                                <?php echo _l('fe_verifica_stati'); ?>
                            </a>
                            <a href="<?php echo admin_url('fatturazione_elettronica/impostazioni'); ?>" class="btn btn-default btn-block">
                                <i class="fa fa-cog tw-mr-2"></i>
                                <?php echo _l('fe_impostazioni'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fatture da Inviare -->
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4">
                            <i class="fa fa-paper-plane tw-mr-2 tw-text-warning"></i>
                            <?php echo _l('fe_da_inviare'); ?>
                            <?php if (!empty($fatture_da_inviare)): ?>
                            <span class="badge badge-warning"><?php echo count($fatture_da_inviare); ?></span>
                            <?php endif; ?>
                        </h4>

                        <?php if (!empty($fatture_da_inviare)): ?>
                            <ul class="list-group">
                                <?php foreach (array_slice($fatture_da_inviare, 0, 5) as $fattura): ?>
                                    <li class="list-group-item tw-flex tw-justify-between tw-items-center">
                                        <div>
                                            <a href="<?php echo admin_url('fatturazione_elettronica/fattura_attiva/' . $fattura->id); ?>">
                                                <?php echo $fattura->nome_file; ?>
                                            </a>
                                            <br>
                                            <small class="text-muted"><?php echo _d($fattura->created_at); ?></small>
                                        </div>
                                        <a href="<?php echo admin_url('fatturazione_elettronica/invia_fattura/' . $fattura->id); ?>"
                                           class="btn btn-primary btn-xs"
                                           onclick="return confirm('<?php echo _l('fe_confirm_send'); ?>');">
                                            <i class="fa fa-paper-plane"></i>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (count($fatture_da_inviare) > 5): ?>
                            <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive?stato=generata'); ?>" class="btn btn-link btn-block">
                                <?php echo _l('fe_vedi_tutte'); ?> (+<?php echo count($fatture_da_inviare) - 5; ?>)
                            </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted tw-text-center tw-py-4">
                                <i class="fa fa-check-circle tw-text-success tw-text-3xl tw-mb-2"></i><br>
                                <?php echo _l('fe_no_fatture_da_inviare'); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Log Recenti -->
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4">
                            <i class="fa fa-history tw-mr-2"></i>
                            <?php echo _l('fe_log_recenti'); ?>
                        </h4>
                        <?php if (!empty($recent_logs)): ?>
                            <ul class="list-group">
                                <?php foreach (array_slice($recent_logs, 0, 5) as $log): ?>
                                    <li class="list-group-item">
                                        <div class="tw-flex tw-items-start">
                                            <span class="tw-inline-block tw-w-2 tw-h-2 tw-rounded-full tw-mt-2 tw-mr-2 <?php echo fe_get_log_color($log->azione); ?>"></span>
                                            <div>
                                                <strong><?php echo fe_get_log_label($log->azione); ?></strong>
                                                <?php if ($log->descrizione): ?>
                                                    <br><small class="text-muted"><?php echo $log->descrizione; ?></small>
                                                <?php endif; ?>
                                                <br><small class="text-muted"><?php echo _dt($log->created_at); ?></small>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted tw-text-center tw-py-4"><?php echo _l('fe_no_logs'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fatture Passive Non Lette -->
        <?php if (!empty($fatture_passive_recenti) && count(array_filter($fatture_passive_recenti, function($f) { return !$f->letto; })) > 0): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4">
                            <i class="fa fa-envelope tw-mr-2 tw-text-info"></i>
                            <?php echo _l('fe_passive_non_lette'); ?>
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('fe_fornitore'); ?></th>
                                        <th><?php echo _l('fe_numero'); ?></th>
                                        <th><?php echo _l('fe_data'); ?></th>
                                        <th><?php echo _l('fe_totale'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fatture_passive_recenti as $fattura): ?>
                                        <?php if (!$fattura->letto): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $fattura->denominazione_fornitore; ?></strong><br>
                                                <small class="text-muted"><?php echo $fattura->partita_iva_fornitore; ?></small>
                                            </td>
                                            <td><?php echo $fattura->numero_documento; ?></td>
                                            <td><?php echo _d($fattura->data_documento); ?></td>
                                            <td><strong>&euro; <?php echo number_format($fattura->importo_totale, 2, ',', '.'); ?></strong></td>
                                            <td>
                                                <a href="<?php echo admin_url('fatturazione_elettronica/fattura_passiva/' . $fattura->id); ?>"
                                                   class="btn btn-info btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                    <?php echo _l('fe_visualizza'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Configurazione Incompleta -->
        <?php if (empty(get_option('fe_partita_iva'))): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <h4><i class="fa fa-exclamation-triangle"></i> <?php echo _l('fe_configurazione_incompleta'); ?></h4>
                    <p><?php echo _l('fe_configurazione_incompleta_msg'); ?></p>
                    <a href="<?php echo admin_url('fatturazione_elettronica/setup/1'); ?>" class="btn btn-warning">
                        <i class="fa fa-play tw-mr-1"></i>
                        <?php echo _l('fe_start_setup'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Genera Fattura -->
<div class="modal fade" id="modal-genera-fattura" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('fe_genera_fattura'); ?></h4>
            </div>
            <form method="post" action="<?php echo admin_url('fatturazione_elettronica/genera_xml'); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo _l('fe_seleziona_fattura'); ?></label>
                        <select name="invoice_id" class="form-control selectpicker" data-live-search="true" required>
                            <option value=""><?php echo _l('fe_seleziona'); ?>...</option>
                            <?php if (!empty($invoices_to_import)): ?>
                                <?php foreach ($invoices_to_import as $inv): ?>
                                <option value="<?php echo $inv->id; ?>">
                                    #<?php echo $inv->number; ?> - <?php echo get_company_name($inv->clientid); ?>
                                    (&euro; <?php echo number_format($inv->total, 2, ',', '.'); ?>)
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('fe_tipo_documento'); ?></label>
                        <select name="tipo_documento" class="form-control">
                            <option value="TD01">TD01 - Fattura</option>
                            <option value="TD24">TD24 - Fattura Differita</option>
                            <option value="TD06">TD06 - Parcella</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-file-code tw-mr-1"></i>
                        <?php echo _l('fe_genera_xml'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    // Inizializza tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Test connessione
    $('#btn-test-connection').on('click', function() {
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <span class="hidden-xs"><?php echo _l("fe_testing"); ?></span>');

        $.get('<?php echo admin_url("fatturazione_elettronica/test_connection"); ?>', function(response) {
            try {
                var data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.success) {
                    alert_float('success', data.message);
                } else {
                    alert_float('danger', data.message + (data.error ? ': ' + data.error : ''));
                }
            } catch(e) {
                alert_float('danger', '<?php echo _l("fe_connection_failed"); ?>');
            }
        }).fail(function() {
            alert_float('danger', '<?php echo _l("fe_connection_failed"); ?>');
        }).always(function() {
            $btn.prop('disabled', false).html(originalHtml);
        });
    });

    // Grafico andamento mensile - usa Chart.js incluso in Perfex (v2.x)
    var ctx = document.getElementById('chartAndamento');
    if (ctx && typeof Chart !== 'undefined') {
        try {
            var chartLabels = <?php echo json_encode($chart_labels); ?>;
            var chartDataInviate = <?php echo json_encode(isset($chart_data['inviate']) ? $chart_data['inviate'] : array_fill(0, 12, 0)); ?>;
            var chartDataRicevute = <?php echo json_encode(isset($chart_data['ricevute']) ? $chart_data['ricevute'] : array_fill(0, 12, 0)); ?>;

            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: '<?php echo _l("fe_fatture_inviate"); ?>',
                        data: chartDataInviate,
                        borderColor: 'rgb(132, 197, 41)',
                        backgroundColor: 'rgba(132, 197, 41, 0.1)',
                        lineTension: 0.3,
                        fill: true
                    }, {
                        label: '<?php echo _l("fe_fatture_ricevute"); ?>',
                        data: chartDataRicevute,
                        borderColor: 'rgb(23, 162, 184)',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        lineTension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    legend: {
                        position: 'bottom'
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }]
                    }
                }
            });
        } catch(e) {
            console.log('Chart error:', e);
            // Mostra messaggio di fallback
            $(ctx).parent().html('<p class="text-muted tw-text-center tw-py-4"><?php echo _l("fe_chart_not_available"); ?></p>');
        }
    }
});
</script>
</body>
</html>

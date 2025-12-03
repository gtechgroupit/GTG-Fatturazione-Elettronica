<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-bold tw-text-xl tw-mb-6">
                    <i class="fa-solid fa-file-invoice tw-mr-2"></i>
                    <?php echo _l('fe_dashboard'); ?>
                </h4>
            </div>
        </div>

        <!-- Statistiche -->
        <div class="row">
            <!-- Fatture Attive -->
            <div class="col-md-3 col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h3 class="tw-font-bold"><?php echo $statistics['attive']['totale']; ?></h3>
                        <p class="text-muted"><?php echo _l('fe_totale_fatture_attive'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive'); ?>" class="panel-footer">
                        <?php echo _l('fe_vedi_tutte'); ?>
                        <i class="fa fa-arrow-right pull-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <h3 class="tw-font-bold"><?php echo $statistics['attive']['consegnate']; ?></h3>
                        <p class="text-muted"><?php echo _l('fe_consegnate'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive?stato=consegnata'); ?>" class="panel-footer">
                        <?php echo _l('fe_vedi_dettaglio'); ?>
                        <i class="fa fa-arrow-right pull-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel panel-warning">
                    <div class="panel-body">
                        <h3 class="tw-font-bold"><?php echo $statistics['attive']['inviate']; ?></h3>
                        <p class="text-muted"><?php echo _l('fe_in_attesa'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive?stato=inviata'); ?>" class="panel-footer">
                        <?php echo _l('fe_vedi_dettaglio'); ?>
                        <i class="fa fa-arrow-right pull-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel panel-danger">
                    <div class="panel-body">
                        <h3 class="tw-font-bold"><?php echo $statistics['attive']['errori']; ?></h3>
                        <p class="text-muted"><?php echo _l('fe_con_errori'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive?stato=scartata'); ?>" class="panel-footer">
                        <?php echo _l('fe_vedi_dettaglio'); ?>
                        <i class="fa fa-arrow-right pull-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Fatture Passive -->
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <h3 class="tw-font-bold"><?php echo $statistics['passive']['totale']; ?></h3>
                        <p class="text-muted"><?php echo _l('fe_fatture_passive_totali'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_passive'); ?>" class="panel-footer">
                        <?php echo _l('fe_vedi_tutte'); ?>
                        <i class="fa fa-arrow-right pull-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="panel panel-warning">
                    <div class="panel-body">
                        <h3 class="tw-font-bold"><?php echo $statistics['passive']['non_lette']; ?></h3>
                        <p class="text-muted"><?php echo _l('fe_non_lette'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_passive?non_lette=1'); ?>" class="panel-footer">
                        <?php echo _l('fe_vedi_dettaglio'); ?>
                        <i class="fa fa-arrow-right pull-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3 class="tw-font-bold"><?php echo $statistics['passive']['da_processare']; ?></h3>
                        <p class="text-muted"><?php echo _l('fe_da_processare'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_passive'); ?>" class="panel-footer">
                        <?php echo _l('fe_vedi_dettaglio'); ?>
                        <i class="fa fa-arrow-right pull-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Azioni Rapide -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4">
                            <i class="fa fa-bolt tw-mr-2"></i>
                            <?php echo _l('fe_azioni_rapide'); ?>
                        </h4>
                        <div class="row">
                            <div class="col-md-6 tw-mb-3">
                                <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive'); ?>" class="btn btn-primary btn-block">
                                    <i class="fa fa-plus tw-mr-2"></i>
                                    <?php echo _l('fe_genera_fattura'); ?>
                                </a>
                            </div>
                            <div class="col-md-6 tw-mb-3">
                                <a href="<?php echo admin_url('fatturazione_elettronica/sync_passive'); ?>" class="btn btn-info btn-block">
                                    <i class="fa fa-sync tw-mr-2"></i>
                                    <?php echo _l('fe_sincronizza_passive'); ?>
                                </a>
                            </div>
                            <div class="col-md-6 tw-mb-3">
                                <a href="<?php echo admin_url('fatturazione_elettronica/impostazioni'); ?>" class="btn btn-default btn-block">
                                    <i class="fa fa-cog tw-mr-2"></i>
                                    <?php echo _l('fe_impostazioni'); ?>
                                </a>
                            </div>
                            <div class="col-md-6 tw-mb-3">
                                <button type="button" class="btn btn-default btn-block" id="btn-test-connection">
                                    <i class="fa fa-plug tw-mr-2"></i>
                                    <?php echo _l('fe_test_connessione'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Recenti -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4">
                            <i class="fa fa-history tw-mr-2"></i>
                            <?php echo _l('fe_log_recenti'); ?>
                        </h4>
                        <?php if (!empty($recent_logs)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <tbody>
                                        <?php foreach ($recent_logs as $log): ?>
                                            <tr>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo _d($log->created_at); ?>
                                                    </small>
                                                    <br>
                                                    <strong><?php echo $log->azione; ?></strong>
                                                    <?php if ($log->descrizione): ?>
                                                        <br><small><?php echo $log->descrizione; ?></small>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted"><?php echo _l('fe_no_logs'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fatture da Inviare -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4">
                            <i class="fa fa-paper-plane tw-mr-2"></i>
                            <?php echo _l('fe_da_inviare'); ?>
                        </h4>

                        <?php
                        $da_inviare = array_filter($fatture_attive_recenti ?? [], function($f) {
                            return $f->stato == FE_STATO_GENERATA;
                        });
                        ?>

                        <?php if (!empty($da_inviare)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('fe_nome_file'); ?></th>
                                            <th><?php echo _l('fe_tipo'); ?></th>
                                            <th><?php echo _l('fe_stato'); ?></th>
                                            <th><?php echo _l('fe_data'); ?></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($da_inviare as $fattura): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo admin_url('fatturazione_elettronica/fattura_attiva/' . $fattura->id); ?>">
                                                        <?php echo $fattura->nome_file; ?>
                                                    </a>
                                                </td>
                                                <td><?php echo fe_get_tipo_documento_label($fattura->tipo_documento); ?></td>
                                                <td>
                                                    <span class="label label-<?php echo fe_get_stato_class($fattura->stato); ?>">
                                                        <?php echo fe_get_stato_label($fattura->stato); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo _d($fattura->created_at); ?></td>
                                                <td>
                                                    <a href="<?php echo admin_url('fatturazione_elettronica/invia_fattura/' . $fattura->id); ?>"
                                                       class="btn btn-primary btn-xs"
                                                       onclick="return confirm('<?php echo _l('fe_confirm_send'); ?>');">
                                                        <i class="fa fa-paper-plane"></i>
                                                        <?php echo _l('fe_invia'); ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted"><?php echo _l('fe_no_fatture_da_inviare'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurazione -->
        <?php if (empty(get_option('fe_partita_iva'))): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <h4><i class="fa fa-exclamation-triangle"></i> <?php echo _l('fe_configurazione_incompleta'); ?></h4>
                        <p><?php echo _l('fe_configurazione_incompleta_msg'); ?></p>
                        <a href="<?php echo admin_url('fatturazione_elettronica/impostazioni'); ?>" class="btn btn-warning">
                            <?php echo _l('fe_vai_impostazioni'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php init_tail(); ?>

<script>
$(function() {
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

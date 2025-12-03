<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <h4 class="tw-font-bold tw-m-0">
                        <i class="fa-solid fa-file-invoice tw-mr-2"></i>
                        <?php echo _l('fe_fattura_dettaglio'); ?>
                    </h4>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i>
                        <?php echo _l('fe_torna_lista'); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Info Principale -->
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold"><?php echo _l('fe_info_documento'); ?></h5>
                                <table class="table table-striped table-condensed">
                                    <tr>
                                        <td><strong><?php echo _l('fe_nome_file'); ?></strong></td>
                                        <td><?php echo $fattura->nome_file; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_tipo'); ?></strong></td>
                                        <td><?php echo fe_get_tipo_documento_label($fattura->tipo_documento); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_progressivo'); ?></strong></td>
                                        <td><code><?php echo $fattura->progressivo_invio; ?></code></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_stato'); ?></strong></td>
                                        <td>
                                            <span class="label label-<?php echo fe_get_stato_class($fattura->stato); ?>">
                                                <?php echo fe_get_stato_label($fattura->stato); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if ($fattura->identificativo_sdi): ?>
                                        <tr>
                                            <td><strong><?php echo _l('fe_id_sdi'); ?></strong></td>
                                            <td><code><?php echo $fattura->identificativo_sdi; ?></code></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold"><?php echo _l('fe_date'); ?></h5>
                                <table class="table table-striped table-condensed">
                                    <tr>
                                        <td><strong><?php echo _l('fe_data_creazione'); ?></strong></td>
                                        <td><?php echo _dt($fattura->created_at); ?></td>
                                    </tr>
                                    <?php if ($fattura->data_invio): ?>
                                        <tr>
                                            <td><strong><?php echo _l('fe_data_invio'); ?></strong></td>
                                            <td><?php echo _dt($fattura->data_invio); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($fattura->data_ricezione_esito): ?>
                                        <tr>
                                            <td><strong><?php echo _l('fe_data_esito'); ?></strong></td>
                                            <td><?php echo _dt($fattura->data_ricezione_esito); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td><strong><?php echo _l('fe_tentativi_invio'); ?></strong></td>
                                        <td><?php echo $fattura->tentativi_invio; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <?php if ($fattura->ultimo_errore): ?>
                            <div class="alert alert-danger tw-mt-4">
                                <strong><i class="fa fa-exclamation-triangle"></i> <?php echo _l('fe_ultimo_errore'); ?>:</strong>
                                <p><?php echo nl2br(htmlspecialchars($fattura->ultimo_errore)); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($fattura->descrizione_esito): ?>
                            <div class="alert alert-info tw-mt-4">
                                <strong><i class="fa fa-info-circle"></i> <?php echo _l('fe_esito_sdi'); ?>:</strong>
                                <p><?php echo nl2br(htmlspecialchars($fattura->descrizione_esito)); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Link Documento Perfex -->
                        <?php if (isset($invoice)): ?>
                            <div class="tw-mt-4 tw-p-4 tw-bg-gray-50 tw-rounded">
                                <h5 class="tw-font-semibold"><?php echo _l('fe_fattura_collegata'); ?></h5>
                                <p>
                                    <a href="<?php echo admin_url('invoices/list_invoices/' . $invoice->id); ?>" target="_blank" class="tw-text-lg">
                                        <i class="fa fa-file-invoice tw-mr-1"></i>
                                        <?php echo format_invoice_number($invoice->id); ?>
                                    </a>
                                </p>
                                <p class="tw-text-gray-600">
                                    <?php echo _l('invoice_total'); ?>: <strong><?php echo app_format_money($invoice->total, $invoice->currency_name); ?></strong>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($credit_note)): ?>
                            <div class="tw-mt-4 tw-p-4 tw-bg-gray-50 tw-rounded">
                                <h5 class="tw-font-semibold"><?php echo _l('fe_nota_credito_collegata'); ?></h5>
                                <p>
                                    <a href="<?php echo admin_url('credit_notes/list_credit_notes/' . $credit_note->id); ?>" target="_blank" class="tw-text-lg">
                                        <i class="fa fa-file-invoice tw-mr-1"></i>
                                        <?php echo format_credit_note_number($credit_note->id); ?>
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Anteprima XML -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-mb-3">
                            <i class="fa fa-code tw-mr-2"></i>
                            <?php echo _l('fe_anteprima_xml'); ?>
                        </h5>
                        <pre class="tw-bg-gray-100 tw-p-4 tw-rounded tw-overflow-auto" style="max-height: 400px;"><code class="language-xml"><?php echo htmlspecialchars($fattura->xml_content); ?></code></pre>
                    </div>
                </div>

                <!-- Notifiche SDI -->
                <?php if (!empty($notifiche)): ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold tw-mb-3">
                                <i class="fa fa-bell tw-mr-2"></i>
                                <?php echo _l('fe_notifiche_sdi'); ?>
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('fe_tipo'); ?></th>
                                            <th><?php echo _l('fe_descrizione'); ?></th>
                                            <th><?php echo _l('fe_data'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notifiche as $notifica): ?>
                                            <tr>
                                                <td><code><?php echo $notifica->tipo_notifica; ?></code></td>
                                                <td><?php echo $notifica->descrizione ?: '-'; ?></td>
                                                <td><?php echo _dt($notifica->data_ricezione); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar Azioni -->
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_azioni'); ?></h5>

                        <div class="btn-group-vertical tw-w-full">
                            <a href="<?php echo admin_url('fatturazione_elettronica/download_xml/' . $fattura->id); ?>" class="btn btn-default tw-mb-2">
                                <i class="fa fa-download tw-mr-2"></i>
                                <?php echo _l('fe_download_xml'); ?>
                            </a>

                            <?php if (in_array($fattura->stato, [FE_STATO_GENERATA, FE_STATO_SCARTATA])): ?>
                                <a href="<?php echo admin_url('fatturazione_elettronica/invia_fattura/' . $fattura->id); ?>"
                                   class="btn btn-primary tw-mb-2"
                                   onclick="return confirm('<?php echo _l('fe_confirm_send'); ?>');">
                                    <i class="fa fa-paper-plane tw-mr-2"></i>
                                    <?php echo _l('fe_invia_sdi'); ?>
                                </a>
                            <?php endif; ?>

                            <?php if (in_array($fattura->stato, [FE_STATO_BOZZA, FE_STATO_GENERATA])): ?>
                                <a href="<?php echo admin_url('fatturazione_elettronica/rigenera_xml/' . $fattura->id); ?>"
                                   class="btn btn-info tw-mb-2"
                                   onclick="return confirm('<?php echo _l('fe_confirm_regenerate'); ?>');">
                                    <i class="fa fa-sync tw-mr-2"></i>
                                    <?php echo _l('fe_rigenera_xml'); ?>
                                </a>
                            <?php endif; ?>

                            <?php if ($fattura->identificativo_sdi): ?>
                                <button type="button" class="btn btn-default tw-mb-2" id="btn-check-status">
                                    <i class="fa fa-refresh tw-mr-2"></i>
                                    <?php echo _l('fe_verifica_stato'); ?>
                                </button>
                            <?php endif; ?>

                            <?php if (in_array($fattura->stato, [FE_STATO_BOZZA, FE_STATO_GENERATA])): ?>
                                <a href="<?php echo admin_url('fatturazione_elettronica/delete_fattura_attiva/' . $fattura->id); ?>"
                                   class="btn btn-danger _delete tw-mb-2">
                                    <i class="fa fa-trash tw-mr-2"></i>
                                    <?php echo _l('fe_elimina'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Log -->
                <?php if (!empty($logs)): ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold tw-mb-3">
                                <i class="fa fa-history tw-mr-2"></i>
                                <?php echo _l('fe_storico'); ?>
                            </h5>
                            <ul class="list-unstyled">
                                <?php foreach ($logs as $log): ?>
                                    <li class="tw-mb-2 tw-pb-2 tw-border-b">
                                        <small class="text-muted"><?php echo _dt($log->created_at); ?></small>
                                        <br>
                                        <strong><?php echo $log->azione; ?></strong>
                                        <?php if ($log->descrizione): ?>
                                            <br><small><?php echo $log->descrizione; ?></small>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
$(function() {
    $('#btn-check-status').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l("fe_verificando"); ?>');

        $.get('<?php echo admin_url("fatturazione_elettronica/ajax_check_status/" . $fattura->id); ?>', function(response) {
            var data = JSON.parse(response);
            if (data.error) {
                alert_float('danger', data.error);
            } else {
                alert_float('success', '<?php echo _l("fe_stato"); ?>: ' + data.label);
                setTimeout(function() { location.reload(); }, 1500);
            }
        }).always(function() {
            $btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> <?php echo _l("fe_verifica_stato"); ?>');
        });
    });
});
</script>
</body>
</html>

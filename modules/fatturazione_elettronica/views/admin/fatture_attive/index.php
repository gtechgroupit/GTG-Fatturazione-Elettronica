<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <h4 class="tw-font-bold tw-m-0">
                                <i class="fa-solid fa-file-invoice tw-mr-2"></i>
                                <?php echo _l('fe_fatture_attive'); ?>
                            </h4>
                            <div>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-import">
                                    <i class="fa fa-plus tw-mr-1"></i>
                                    <?php echo _l('fe_importa_fattura'); ?>
                                </button>
                            </div>
                        </div>

                        <!-- Filtri -->
                        <form method="get" class="tw-mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="stato" class="form-control selectpicker" data-live-search="true" data-none-selected-text="<?php echo _l('fe_tutti_stati'); ?>">
                                        <option value=""><?php echo _l('fe_tutti_stati'); ?></option>
                                        <option value="bozza" <?php echo ($filters['stato'] ?? '') == 'bozza' ? 'selected' : ''; ?>><?php echo _l('fe_stato_bozza'); ?></option>
                                        <option value="generata" <?php echo ($filters['stato'] ?? '') == 'generata' ? 'selected' : ''; ?>><?php echo _l('fe_stato_generata'); ?></option>
                                        <option value="inviata" <?php echo ($filters['stato'] ?? '') == 'inviata' ? 'selected' : ''; ?>><?php echo _l('fe_stato_inviata'); ?></option>
                                        <option value="consegnata" <?php echo ($filters['stato'] ?? '') == 'consegnata' ? 'selected' : ''; ?>><?php echo _l('fe_stato_consegnata'); ?></option>
                                        <option value="accettata" <?php echo ($filters['stato'] ?? '') == 'accettata' ? 'selected' : ''; ?>><?php echo _l('fe_stato_accettata'); ?></option>
                                        <option value="rifiutata" <?php echo ($filters['stato'] ?? '') == 'rifiutata' ? 'selected' : ''; ?>><?php echo _l('fe_stato_rifiutata'); ?></option>
                                        <option value="scartata" <?php echo ($filters['stato'] ?? '') == 'scartata' ? 'selected' : ''; ?>><?php echo _l('fe_stato_scartata'); ?></option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="from_date" class="form-control" placeholder="<?php echo _l('fe_da_data'); ?>" value="<?php echo $filters['from_date'] ?? ''; ?>">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="to_date" class="form-control" placeholder="<?php echo _l('fe_a_data'); ?>" value="<?php echo $filters['to_date'] ?? ''; ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fa fa-filter"></i>
                                        <?php echo _l('fe_filtra'); ?>
                                    </button>
                                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_attive'); ?>" class="btn btn-default">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Tabella -->
                        <div class="table-responsive">
                            <table class="table table-striped dt-table" data-order-col="4" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="30">
                                            <input type="checkbox" id="check-all">
                                        </th>
                                        <th><?php echo _l('fe_nome_file'); ?></th>
                                        <th><?php echo _l('fe_tipo'); ?></th>
                                        <th><?php echo _l('fe_id_sdi'); ?></th>
                                        <th><?php echo _l('fe_stato'); ?></th>
                                        <th><?php echo _l('fe_data_creazione'); ?></th>
                                        <th><?php echo _l('fe_data_invio'); ?></th>
                                        <th width="150"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fatture as $fattura): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="ids[]" value="<?php echo $fattura->id; ?>" class="check-item">
                                            </td>
                                            <td>
                                                <a href="<?php echo admin_url('fatturazione_elettronica/fattura_attiva/' . $fattura->id); ?>">
                                                    <?php echo $fattura->nome_file; ?>
                                                </a>
                                                <?php if ($fattura->invoice_id): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <a href="<?php echo admin_url('invoices/list_invoices/' . $fattura->invoice_id); ?>" target="_blank">
                                                            <?php echo _l('fe_fattura_perfex'); ?> #<?php echo $fattura->invoice_id; ?>
                                                        </a>
                                                    </small>
                                                <?php endif; ?>
                                                <?php if ($fattura->credit_note_id): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <a href="<?php echo admin_url('credit_notes/list_credit_notes/' . $fattura->credit_note_id); ?>" target="_blank">
                                                            <?php echo _l('fe_nota_credito'); ?> #<?php echo $fattura->credit_note_id; ?>
                                                        </a>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo fe_get_tipo_documento_label($fattura->tipo_documento); ?></td>
                                            <td>
                                                <?php if ($fattura->identificativo_sdi): ?>
                                                    <code><?php echo $fattura->identificativo_sdi; ?></code>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo fe_get_stato_class($fattura->stato); ?>">
                                                    <?php echo fe_get_stato_label($fattura->stato); ?>
                                                </span>
                                                <?php if ($fattura->ultimo_errore): ?>
                                                    <i class="fa fa-exclamation-circle text-danger" data-toggle="tooltip" title="<?php echo htmlspecialchars($fattura->ultimo_errore); ?>"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td data-order="<?php echo strtotime($fattura->created_at); ?>">
                                                <?php echo _dt($fattura->created_at); ?>
                                            </td>
                                            <td>
                                                <?php if ($fattura->data_invio): ?>
                                                    <?php echo _dt($fattura->data_invio); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo admin_url('fatturazione_elettronica/fattura_attiva/' . $fattura->id); ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo _l('fe_visualizza'); ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo admin_url('fatturazione_elettronica/download_xml/' . $fattura->id); ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo _l('fe_download'); ?>">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <?php if ($fattura->stato == FE_STATO_GENERATA || $fattura->stato == FE_STATO_SCARTATA): ?>
                                                        <a href="<?php echo admin_url('fatturazione_elettronica/invia_fattura/' . $fattura->id); ?>" class="btn btn-primary btn-xs" data-toggle="tooltip" title="<?php echo _l('fe_invia'); ?>" onclick="return confirm('<?php echo _l('fe_confirm_send'); ?>');">
                                                            <i class="fa fa-paper-plane"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (in_array($fattura->stato, [FE_STATO_BOZZA, FE_STATO_GENERATA])): ?>
                                                        <a href="<?php echo admin_url('fatturazione_elettronica/delete_fattura_attiva/' . $fattura->id); ?>" class="btn btn-danger btn-xs _delete" data-toggle="tooltip" title="<?php echo _l('fe_elimina'); ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Azioni Bulk -->
                        <div class="tw-mt-4" id="bulk-actions" style="display: none;">
                            <form method="post" action="<?php echo admin_url('fatturazione_elettronica/invia_multiple'); ?>">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <div id="selected-ids"></div>
                                <button type="submit" class="btn btn-primary" onclick="return confirm('<?php echo _l('fe_confirm_bulk_send'); ?>');">
                                    <i class="fa fa-paper-plane"></i>
                                    <?php echo _l('fe_invia_selezionate'); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modal-import" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('fe_importa_fattura'); ?></h4>
            </div>
            <form method="post" action="<?php echo admin_url('fatturazione_elettronica/genera_xml'); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo _l('fe_seleziona_fattura'); ?></label>
                        <select name="invoice_id" class="form-control selectpicker" data-live-search="true" required>
                            <option value=""><?php echo _l('fe_seleziona'); ?></option>
                        </select>
                        <small class="text-muted"><?php echo _l('fe_fatture_non_importate'); ?></small>
                    </div>
                    <div class="form-group">
                        <label><?php echo _l('fe_tipo_documento'); ?></label>
                        <select name="tipo_documento" class="form-control">
                            <option value="TD01">TD01 - Fattura</option>
                            <option value="TD02">TD02 - Acconto/Anticipo su fattura</option>
                            <option value="TD06">TD06 - Parcella</option>
                            <option value="TD24">TD24 - Fattura differita</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('fe_genera_xml'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    // Carica le fatture non ancora importate
    $('#modal-import').on('show.bs.modal', function() {
        var $select = $(this).find('select[name="invoice_id"]');
        $select.html('<option value=""><?php echo _l("fe_caricamento"); ?></option>');

        $.get('<?php echo admin_url("fatturazione_elettronica/ajax_get_invoices_to_import"); ?>', function(response) {
            var data = JSON.parse(response);
            var options = '<option value=""><?php echo _l("fe_seleziona"); ?></option>';

            if (data.invoices && data.invoices.length > 0) {
                data.invoices.forEach(function(inv) {
                    options += '<option value="' + inv.id + '">#' + (inv.prefix || '') + inv.number + ' - ' + inv.date + '</option>';
                });
            } else {
                options = '<option value=""><?php echo _l("fe_no_fatture_importabili"); ?></option>';
            }

            $select.html(options).selectpicker('refresh');
        });
    });

    // Check all
    $('#check-all').on('change', function() {
        $('.check-item').prop('checked', $(this).is(':checked'));
        updateBulkActions();
    });

    $('.check-item').on('change', function() {
        updateBulkActions();
    });

    function updateBulkActions() {
        var checked = $('.check-item:checked');
        if (checked.length > 0) {
            $('#bulk-actions').show();
            var html = '';
            checked.each(function() {
                html += '<input type="hidden" name="ids[]" value="' + $(this).val() + '">';
            });
            $('#selected-ids').html(html);
        } else {
            $('#bulk-actions').hide();
        }
    }
});
</script>
</body>
</html>

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
                                <i class="fa-solid fa-file-invoice-dollar tw-mr-2"></i>
                                <?php echo _l('fe_fatture_passive'); ?>
                            </h4>
                            <div>
                                <a href="<?php echo admin_url('fatturazione_elettronica/sync_passive'); ?>" class="btn btn-info">
                                    <i class="fa fa-sync tw-mr-1"></i>
                                    <?php echo _l('fe_sincronizza'); ?>
                                </a>
                            </div>
                        </div>

                        <!-- Filtri -->
                        <form method="get" class="tw-mb-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <input type="date" name="from_date" class="form-control" placeholder="<?php echo _l('fe_da_data'); ?>" value="<?php echo $filters['from_date'] ?? ''; ?>">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="to_date" class="form-control" placeholder="<?php echo _l('fe_a_data'); ?>" value="<?php echo $filters['to_date'] ?? ''; ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="non_lette" value="1" <?php echo isset($filters['letto']) && $filters['letto'] == 0 ? 'checked' : ''; ?>>
                                        <?php echo _l('fe_solo_non_lette'); ?>
                                    </label>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fa fa-filter"></i>
                                        <?php echo _l('fe_filtra'); ?>
                                    </button>
                                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_passive'); ?>" class="btn btn-default">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Tabella -->
                        <div class="table-responsive">
                            <table class="table table-striped dt-table" data-order-col="5" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th width="30"></th>
                                        <th><?php echo _l('fe_fornitore'); ?></th>
                                        <th><?php echo _l('fe_numero'); ?></th>
                                        <th><?php echo _l('fe_data'); ?></th>
                                        <th class="text-right"><?php echo _l('fe_totale'); ?></th>
                                        <th><?php echo _l('fe_data_ricezione'); ?></th>
                                        <th><?php echo _l('fe_stato'); ?></th>
                                        <th width="120"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fatture as $fattura): ?>
                                        <tr class="<?php echo $fattura->letto == 0 ? 'tw-font-bold' : ''; ?>">
                                            <td>
                                                <?php if ($fattura->letto == 0): ?>
                                                    <i class="fa fa-circle text-info" title="<?php echo _l('fe_non_letta'); ?>"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo admin_url('fatturazione_elettronica/fattura_passiva/' . $fattura->id); ?>">
                                                    <?php echo $fattura->fornitore_denominazione; ?>
                                                </a>
                                                <br>
                                                <small class="text-muted">P.IVA: <?php echo $fattura->fornitore_partita_iva; ?></small>
                                            </td>
                                            <td>
                                                <code><?php echo $fattura->numero_documento; ?></code>
                                                <br>
                                                <small class="text-muted"><?php echo fe_get_tipo_documento_label($fattura->tipo_documento); ?></small>
                                            </td>
                                            <td data-order="<?php echo strtotime($fattura->data_documento); ?>">
                                                <?php echo _d($fattura->data_documento); ?>
                                            </td>
                                            <td class="text-right">
                                                <strong><?php echo app_format_money($fattura->totale, 'EUR'); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    IVA: <?php echo app_format_money($fattura->iva, 'EUR'); ?>
                                                </small>
                                            </td>
                                            <td data-order="<?php echo strtotime($fattura->data_ricezione); ?>">
                                                <?php echo _dt($fattura->data_ricezione); ?>
                                            </td>
                                            <td>
                                                <?php if ($fattura->expense_id): ?>
                                                    <span class="label label-success">
                                                        <i class="fa fa-check"></i>
                                                        <?php echo _l('fe_collegata'); ?>
                                                    </span>
                                                <?php elseif ($fattura->archiviato): ?>
                                                    <span class="label label-default">
                                                        <?php echo _l('fe_archiviata'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="label label-warning">
                                                        <?php echo _l('fe_da_processare'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo admin_url('fatturazione_elettronica/fattura_passiva/' . $fattura->id); ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo _l('fe_visualizza'); ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo admin_url('fatturazione_elettronica/download_xml_passiva/' . $fattura->id); ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo _l('fe_download'); ?>">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <?php if (!$fattura->expense_id && !$fattura->archiviato): ?>
                                                        <a href="<?php echo admin_url('fatturazione_elettronica/crea_spesa/' . $fattura->id); ?>" class="btn btn-info btn-xs" data-toggle="tooltip" title="<?php echo _l('fe_crea_spesa'); ?>">
                                                            <i class="fa fa-receipt"></i>
                                                        </a>
                                                        <a href="<?php echo admin_url('fatturazione_elettronica/archivia_passiva/' . $fattura->id); ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo _l('fe_archivia'); ?>">
                                                            <i class="fa fa-archive"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>

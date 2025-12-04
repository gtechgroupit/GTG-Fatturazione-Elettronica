<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Inizializzazione sicura variabili
$fatture = isset($fatture) ? $fatture : [];
$filters = isset($filters) ? $filters : [];
?>
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
                                <a href="<?php echo admin_url('fatturazione_elettronica/sync_passive'); ?>"
                                   class="btn btn-info"
                                   data-toggle="tooltip"
                                   title="<?php echo _l('fe_sincronizza_passive_tooltip'); ?>"
                                   aria-label="<?php echo _l('fe_sincronizza'); ?>">
                                    <i class="fa fa-sync tw-mr-1" aria-hidden="true"></i>
                                    <span class="hidden-xs"><?php echo _l('fe_sincronizza'); ?></span>
                                </a>
                            </div>
                        </div>

                        <!-- Filtri -->
                        <form method="get" class="tw-mb-4" role="search" aria-label="<?php echo _l('fe_filtra'); ?>">
                            <div class="row">
                                <div class="col-md-2 col-sm-3 col-xs-6 mbot10">
                                    <label for="filter_from_date" class="sr-only"><?php echo _l('fe_da_data'); ?></label>
                                    <input type="date"
                                           id="filter_from_date"
                                           name="from_date"
                                           class="form-control"
                                           placeholder="<?php echo _l('fe_da_data'); ?>"
                                           title="<?php echo _l('fe_da_data'); ?>"
                                           value="<?php echo isset($filters['from_date']) ? $filters['from_date'] : ''; ?>">
                                </div>
                                <div class="col-md-2 col-sm-3 col-xs-6 mbot10">
                                    <label for="filter_to_date" class="sr-only"><?php echo _l('fe_a_data'); ?></label>
                                    <input type="date"
                                           id="filter_to_date"
                                           name="to_date"
                                           class="form-control"
                                           placeholder="<?php echo _l('fe_a_data'); ?>"
                                           title="<?php echo _l('fe_a_data'); ?>"
                                           value="<?php echo isset($filters['to_date']) ? $filters['to_date'] : ''; ?>">
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-6 mbot10">
                                    <div class="checkbox" style="margin-top: 8px;"
                                         data-toggle="tooltip"
                                         title="<?php echo _l('fe_solo_non_lette_tooltip'); ?>">
                                        <input type="checkbox"
                                               id="filter_non_lette"
                                               name="non_lette"
                                               value="1"
                                               <?php echo isset($filters['letto']) && $filters['letto'] == 0 ? 'checked' : ''; ?>>
                                        <label for="filter_non_lette"><?php echo _l('fe_solo_non_lette'); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-6 mbot10">
                                    <button type="submit"
                                            class="btn btn-default"
                                            data-toggle="tooltip"
                                            title="<?php echo _l('fe_applica_filtri'); ?>">
                                        <i class="fa fa-filter" aria-hidden="true"></i>
                                        <span class="hidden-xs"><?php echo _l('fe_filtra'); ?></span>
                                    </button>
                                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_passive'); ?>"
                                       class="btn btn-default"
                                       data-toggle="tooltip"
                                       title="<?php echo _l('fe_reset_filtri'); ?>"
                                       aria-label="<?php echo _l('fe_reset_filtri'); ?>">
                                        <i class="fa fa-times" aria-hidden="true"></i>
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
                                        <th data-toggle="tooltip" title="<?php echo _l('fe_tipo_tooltip'); ?>">
                                            <?php echo _l('fe_numero'); ?>
                                        </th>
                                        <th><?php echo _l('fe_data'); ?></th>
                                        <th class="text-right"><?php echo _l('fe_totale'); ?></th>
                                        <th><?php echo _l('fe_data_ricezione'); ?></th>
                                        <th data-toggle="tooltip" title="<?php echo _l('fe_stato_tooltip'); ?>">
                                            <?php echo _l('fe_stato'); ?>
                                        </th>
                                        <th width="120"><?php echo _l('fe_azioni'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fatture as $fattura):
                                        $is_non_letta = isset($fattura->letto) && $fattura->letto == 0;
                                        $has_expense = isset($fattura->expense_id) && $fattura->expense_id;
                                        $is_archived = isset($fattura->archiviato) && $fattura->archiviato;
                                    ?>
                                        <tr class="<?php echo $is_non_letta ? 'tw-font-bold' : ''; ?>">
                                            <td>
                                                <?php if ($is_non_letta): ?>
                                                    <i class="fa fa-circle text-info"
                                                       data-toggle="tooltip"
                                                       title="<?php echo _l('fe_non_letta'); ?>"
                                                       aria-hidden="true"></i>
                                                    <span class="sr-only"><?php echo _l('fe_non_letta'); ?></span>
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
                                                <?php if ($has_expense): ?>
                                                    <span class="label label-success"
                                                          data-toggle="tooltip"
                                                          title="<?php echo _l('fe_collegata_tooltip'); ?>">
                                                        <i class="fa fa-check" aria-hidden="true"></i>
                                                        <?php echo _l('fe_collegata'); ?>
                                                    </span>
                                                <?php elseif ($is_archived): ?>
                                                    <span class="label label-default"
                                                          data-toggle="tooltip"
                                                          title="<?php echo _l('fe_archiviata_tooltip'); ?>">
                                                        <?php echo _l('fe_archiviata'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="label label-warning"
                                                          data-toggle="tooltip"
                                                          title="<?php echo _l('fe_da_processare_tooltip'); ?>">
                                                        <?php echo _l('fe_da_processare'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="<?php echo _l('fe_azioni'); ?>">
                                                    <a href="<?php echo admin_url('fatturazione_elettronica/fattura_passiva/' . $fattura->id); ?>"
                                                       class="btn btn-default btn-xs"
                                                       data-toggle="tooltip"
                                                       title="<?php echo _l('fe_visualizza_tooltip'); ?>"
                                                       aria-label="<?php echo _l('fe_visualizza'); ?>">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="<?php echo admin_url('fatturazione_elettronica/download_xml_passiva/' . $fattura->id); ?>"
                                                       class="btn btn-default btn-xs"
                                                       data-toggle="tooltip"
                                                       title="<?php echo _l('fe_download_xml_tooltip'); ?>"
                                                       aria-label="<?php echo _l('fe_download_xml'); ?>">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                    <?php if (!$has_expense && !$is_archived): ?>
                                                        <a href="<?php echo admin_url('fatturazione_elettronica/crea_spesa/' . $fattura->id); ?>"
                                                           class="btn btn-info btn-xs"
                                                           data-toggle="tooltip"
                                                           title="<?php echo _l('fe_crea_spesa_tooltip'); ?>"
                                                           aria-label="<?php echo _l('fe_crea_spesa'); ?>">
                                                            <i class="fa fa-receipt" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="<?php echo admin_url('fatturazione_elettronica/archivia_passiva/' . $fattura->id); ?>"
                                                           class="btn btn-default btn-xs"
                                                           data-toggle="tooltip"
                                                           title="<?php echo _l('fe_archivia_tooltip'); ?>"
                                                           aria-label="<?php echo _l('fe_archivia'); ?>">
                                                            <i class="fa fa-archive" aria-hidden="true"></i>
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

<script>
$(function() {
    // Inizializza tooltip
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>

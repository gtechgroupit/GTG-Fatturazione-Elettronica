<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <h4 class="tw-font-bold tw-m-0">
                        <i class="fa-solid fa-file-invoice-dollar tw-mr-2"></i>
                        <?php echo _l('fe_fattura_passiva_dettaglio'); ?>
                    </h4>
                    <a href="<?php echo admin_url('fatturazione_elettronica/fatture_passive'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i>
                        <?php echo _l('fe_torna_lista'); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Info Principale -->
            <div class="col-md-8">
                <!-- Dati Fornitore -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold"><?php echo _l('fe_dati_fornitore'); ?></h5>
                                <table class="table table-striped table-condensed">
                                    <tr>
                                        <td><strong><?php echo _l('fe_denominazione'); ?></strong></td>
                                        <td><?php echo $fattura->fornitore_denominazione; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_partita_iva'); ?></strong></td>
                                        <td><?php echo $fattura->fornitore_partita_iva; ?></td>
                                    </tr>
                                    <?php if ($fattura->fornitore_codice_fiscale): ?>
                                        <tr>
                                            <td><strong><?php echo _l('fe_codice_fiscale'); ?></strong></td>
                                            <td><?php echo $fattura->fornitore_codice_fiscale; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (isset($dettagli['fornitore'])): ?>
                                        <tr>
                                            <td><strong><?php echo _l('fe_indirizzo'); ?></strong></td>
                                            <td>
                                                <?php echo $dettagli['fornitore']['indirizzo']; ?>
                                                <?php if ($dettagli['fornitore']['cap']): ?>
                                                    , <?php echo $dettagli['fornitore']['cap']; ?>
                                                <?php endif; ?>
                                                <?php if ($dettagli['fornitore']['comune']): ?>
                                                    <?php echo $dettagli['fornitore']['comune']; ?>
                                                <?php endif; ?>
                                                <?php if ($dettagli['fornitore']['provincia']): ?>
                                                    (<?php echo $dettagli['fornitore']['provincia']; ?>)
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold"><?php echo _l('fe_dati_documento'); ?></h5>
                                <table class="table table-striped table-condensed">
                                    <tr>
                                        <td><strong><?php echo _l('fe_tipo'); ?></strong></td>
                                        <td><?php echo fe_get_tipo_documento_label($fattura->tipo_documento); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_numero'); ?></strong></td>
                                        <td><code><?php echo $fattura->numero_documento; ?></code></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_data'); ?></strong></td>
                                        <td><?php echo _d($fattura->data_documento); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_id_sdi'); ?></strong></td>
                                        <td><code><?php echo $fattura->identificativo_sdi; ?></code></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('fe_data_ricezione'); ?></strong></td>
                                        <td><?php echo _dt($fattura->data_ricezione); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Righe Documento -->
                <?php if (isset($dettagli['linee']) && !empty($dettagli['linee'])): ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_righe_documento'); ?></h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="40">#</th>
                                            <th><?php echo _l('fe_descrizione'); ?></th>
                                            <th class="text-right"><?php echo _l('fe_quantita'); ?></th>
                                            <th class="text-right"><?php echo _l('fe_prezzo_unitario'); ?></th>
                                            <th class="text-right"><?php echo _l('fe_iva'); ?></th>
                                            <th class="text-right"><?php echo _l('fe_totale'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dettagli['linee'] as $linea): ?>
                                            <tr>
                                                <td><?php echo $linea['numero_linea']; ?></td>
                                                <td>
                                                    <?php echo $linea['descrizione']; ?>
                                                    <?php if ($linea['codice_articolo']): ?>
                                                        <br><small class="text-muted">Cod: <?php echo $linea['codice_articolo']; ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php echo number_format($linea['quantita'], 2, ',', '.'); ?>
                                                    <?php if ($linea['unita_misura']): ?>
                                                        <?php echo $linea['unita_misura']; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php echo app_format_money($linea['prezzo_unitario'], 'EUR'); ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php echo number_format($linea['aliquota_iva'], 0); ?>%
                                                    <?php if ($linea['natura']): ?>
                                                        <br><small>(<?php echo $linea['natura']; ?>)</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php echo app_format_money($linea['prezzo_totale'], 'EUR'); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Riepilogo IVA -->
                <?php if (isset($dettagli['riepilogo']) && !empty($dettagli['riepilogo'])): ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_riepilogo_iva'); ?></h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('fe_aliquota'); ?></th>
                                            <th><?php echo _l('fe_natura'); ?></th>
                                            <th class="text-right"><?php echo _l('fe_imponibile'); ?></th>
                                            <th class="text-right"><?php echo _l('fe_imposta'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dettagli['riepilogo'] as $riep): ?>
                                            <tr>
                                                <td><?php echo number_format($riep['aliquota_iva'], 0); ?>%</td>
                                                <td>
                                                    <?php echo $riep['natura'] ?: '-'; ?>
                                                    <?php if ($riep['riferimento_normativo']): ?>
                                                        <br><small class="text-muted"><?php echo $riep['riferimento_normativo']; ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right"><?php echo app_format_money($riep['imponibile_importo'], 'EUR'); ?></td>
                                                <td class="text-right"><?php echo app_format_money($riep['imposta'], 'EUR'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Dati Pagamento -->
                <?php if (isset($dettagli['pagamento']) && !empty($dettagli['pagamento'])): ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_dati_pagamento'); ?></h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('fe_modalita'); ?></th>
                                            <th><?php echo _l('fe_scadenza'); ?></th>
                                            <th class="text-right"><?php echo _l('fe_importo'); ?></th>
                                            <th><?php echo _l('fe_iban'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dettagli['pagamento'] as $pag): ?>
                                            <tr>
                                                <td><?php echo $pag['modalita']; ?></td>
                                                <td><?php echo $pag['data_scadenza'] ? _d($pag['data_scadenza']) : '-'; ?></td>
                                                <td class="text-right"><?php echo app_format_money($pag['importo'], 'EUR'); ?></td>
                                                <td><?php echo $pag['iban'] ?: '-'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

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
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Totali -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_totali'); ?></h5>
                        <table class="table">
                            <tr>
                                <td><?php echo _l('fe_imponibile'); ?></td>
                                <td class="text-right"><strong><?php echo app_format_money($fattura->imponibile, 'EUR'); ?></strong></td>
                            </tr>
                            <tr>
                                <td><?php echo _l('fe_iva'); ?></td>
                                <td class="text-right"><strong><?php echo app_format_money($fattura->iva, 'EUR'); ?></strong></td>
                            </tr>
                            <tr class="tw-bg-gray-100">
                                <td><strong><?php echo _l('fe_totale'); ?></strong></td>
                                <td class="text-right"><strong class="tw-text-xl"><?php echo app_format_money($fattura->totale, 'EUR'); ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Azioni -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_azioni'); ?></h5>

                        <div class="btn-group-vertical tw-w-full">
                            <a href="<?php echo admin_url('fatturazione_elettronica/download_xml_passiva/' . $fattura->id); ?>" class="btn btn-default tw-mb-2">
                                <i class="fa fa-download tw-mr-2"></i>
                                <?php echo _l('fe_download_xml'); ?>
                            </a>

                            <?php if (!$fattura->expense_id && !$fattura->archiviato): ?>
                                <a href="<?php echo admin_url('fatturazione_elettronica/crea_spesa/' . $fattura->id); ?>" class="btn btn-primary tw-mb-2">
                                    <i class="fa fa-receipt tw-mr-2"></i>
                                    <?php echo _l('fe_crea_spesa'); ?>
                                </a>

                                <a href="<?php echo admin_url('fatturazione_elettronica/archivia_passiva/' . $fattura->id); ?>" class="btn btn-default tw-mb-2">
                                    <i class="fa fa-archive tw-mr-2"></i>
                                    <?php echo _l('fe_archivia'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Spesa Collegata -->
                <?php if (isset($expense)): ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold tw-mb-3">
                                <i class="fa fa-receipt tw-mr-2"></i>
                                <?php echo _l('fe_spesa_collegata'); ?>
                            </h5>
                            <p>
                                <a href="<?php echo admin_url('expenses/list_expenses/' . $expense->expenseid); ?>" target="_blank">
                                    <?php echo _l('expense'); ?> #<?php echo $expense->expenseid; ?>
                                </a>
                            </p>
                            <p>
                                <?php echo _l('expense_total'); ?>: <strong><?php echo app_format_money($expense->amount, 'EUR'); ?></strong>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Note -->
                <?php if ($fattura->note): ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold tw-mb-3"><?php echo _l('fe_note'); ?></h5>
                            <p><?php echo nl2br(htmlspecialchars($fattura->note)); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <hr>
        <h4 class="tw-font-semibold tw-mb-3">
            <i class="fa-solid fa-file-invoice tw-mr-2"></i>
            <?php echo _l('fe_dati_fatturazione_elettronica'); ?>
        </h4>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="fe_codice_destinatario"><?php echo _l('fe_codice_destinatario'); ?></label>
            <input type="text" id="fe_codice_destinatario" name="fe_codice_destinatario" class="form-control" value="<?php echo htmlspecialchars($codice_destinatario); ?>" maxlength="7" placeholder="XXXXXXX">
            <small class="text-muted"><?php echo _l('fe_codice_destinatario_cliente_help'); ?></small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="fe_pec"><?php echo _l('fe_pec'); ?></label>
            <input type="email" id="fe_pec" name="fe_pec" class="form-control" value="<?php echo htmlspecialchars($pec); ?>" placeholder="pec@esempio.it">
            <small class="text-muted"><?php echo _l('fe_pec_help'); ?></small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="fe_codice_fiscale"><?php echo _l('fe_codice_fiscale'); ?></label>
            <input type="text" id="fe_codice_fiscale" name="fe_codice_fiscale" class="form-control" value="<?php echo htmlspecialchars($codice_fiscale); ?>" maxlength="16" placeholder="RSSMRA80A01H501U">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="fe_split_payment"><?php echo _l('fe_split_payment'); ?></label>
            <select id="fe_split_payment" name="fe_split_payment" class="form-control">
                <option value="0" <?php echo $split_payment != '1' ? 'selected' : ''; ?>><?php echo _l('no'); ?></option>
                <option value="1" <?php echo $split_payment == '1' ? 'selected' : ''; ?>><?php echo _l('yes'); ?></option>
            </select>
            <small class="text-muted"><?php echo _l('fe_split_payment_help'); ?></small>
        </div>
    </div>
</div>

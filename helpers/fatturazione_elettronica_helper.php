<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper per il modulo Fatturazione Elettronica
 */

/**
 * Ottiene il meta di un cliente
 *
 * @param int $client_id ID del cliente
 * @param string $meta_key Chiave del meta
 * @return mixed
 */
if (!function_exists('get_client_meta')) {
    function get_client_meta($client_id, $meta_key)
    {
        $CI = &get_instance();

        $CI->db->select('value');
        $CI->db->where('rel_id', $client_id);
        $CI->db->where('rel_type', 'clients');
        $CI->db->where('fieldname', $meta_key);
        $row = $CI->db->get(db_prefix() . 'customfieldsvalues')->row();

        return $row ? $row->value : null;
    }
}

/**
 * Imposta il meta di un cliente
 *
 * @param int $client_id ID del cliente
 * @param string $meta_key Chiave del meta
 * @param mixed $value Valore
 * @return bool
 */
if (!function_exists('set_client_meta')) {
    function set_client_meta($client_id, $meta_key, $value)
    {
        $CI = &get_instance();

        // Controlla se esiste già
        $existing = get_client_meta($client_id, $meta_key);

        if ($existing !== null) {
            $CI->db->where('rel_id', $client_id);
            $CI->db->where('rel_type', 'clients');
            $CI->db->where('fieldname', $meta_key);
            return $CI->db->update(db_prefix() . 'customfieldsvalues', ['value' => $value]);
        } else {
            return $CI->db->insert(db_prefix() . 'customfieldsvalues', [
                'rel_id'    => $client_id,
                'rel_type'  => 'clients',
                'fieldname' => $meta_key,
                'value'     => $value,
            ]);
        }
    }
}

/**
 * Valida un codice fiscale italiano
 *
 * @param string $cf Codice fiscale
 * @return bool
 */
if (!function_exists('fe_valida_codice_fiscale')) {
    function fe_valida_codice_fiscale($cf)
    {
        $cf = strtoupper(trim($cf));

        if (strlen($cf) != 16 && strlen($cf) != 11) {
            return false;
        }

        // Se è di 11 caratteri è una partita IVA usata come CF
        if (strlen($cf) == 11) {
            return fe_valida_partita_iva($cf);
        }

        // Validazione codice fiscale 16 caratteri
        if (!preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/i', $cf)) {
            return false;
        }

        $set1 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $set2 = 'ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $even = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $odd = 'BAKPLCQDREVOSFTGUHMINJWZYX';
        $sum = 0;

        for ($i = 0; $i < 15; $i++) {
            $char = $cf[$i];
            $pos = strpos($set1, $char);
            if ($pos !== false) {
                $char = $set2[$pos];
            }
            if ($i % 2 == 0) {
                $pos = strpos($even, $char);
                $sum += strpos($odd, $even[$pos]);
            } else {
                $sum += strpos($even, $char);
            }
        }

        $check = $even[$sum % 26];
        return $cf[15] == $check;
    }
}

/**
 * Valida una partita IVA italiana
 *
 * @param string $piva Partita IVA
 * @return bool
 */
if (!function_exists('fe_valida_partita_iva')) {
    function fe_valida_partita_iva($piva)
    {
        $piva = trim($piva);

        // Rimuovi eventuale prefisso IT
        if (strtoupper(substr($piva, 0, 2)) == 'IT') {
            $piva = substr($piva, 2);
        }

        if (strlen($piva) != 11) {
            return false;
        }

        if (!preg_match('/^[0-9]{11}$/', $piva)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 11; $i++) {
            $digit = intval($piva[$i]);
            if ($i % 2 == 0) {
                $sum += $digit;
            } else {
                $double = $digit * 2;
                $sum += ($double > 9) ? $double - 9 : $double;
            }
        }

        return $sum % 10 == 0;
    }
}

/**
 * Valida un codice destinatario SDI
 *
 * @param string $codice Codice destinatario
 * @return bool
 */
if (!function_exists('fe_valida_codice_destinatario')) {
    function fe_valida_codice_destinatario($codice)
    {
        $codice = strtoupper(trim($codice));

        // 7 caratteri per privati/B2B, 6 per PA
        if (strlen($codice) != 7 && strlen($codice) != 6) {
            return false;
        }

        // Solo caratteri alfanumerici
        if (!preg_match('/^[A-Z0-9]+$/', $codice)) {
            return false;
        }

        return true;
    }
}

/**
 * Formatta un importo per FatturaPA (2 decimali, punto come separatore)
 *
 * @param float $amount Importo
 * @return string
 */
if (!function_exists('fe_format_amount')) {
    function fe_format_amount($amount)
    {
        return number_format((float)$amount, 2, '.', '');
    }
}

/**
 * Formatta una data per FatturaPA (YYYY-MM-DD)
 *
 * @param string|int $date Data
 * @return string
 */
if (!function_exists('fe_format_date')) {
    function fe_format_date($date)
    {
        if (is_numeric($date)) {
            return date('Y-m-d', $date);
        }
        return date('Y-m-d', strtotime($date));
    }
}

/**
 * Ottiene il codice natura IVA per le esenzioni
 *
 * @param float $tax_rate Aliquota IVA
 * @param string $description Descrizione dell'esenzione
 * @return string|null
 */
if (!function_exists('fe_get_natura_iva')) {
    function fe_get_natura_iva($tax_rate, $description = '')
    {
        if ($tax_rate > 0) {
            return null;
        }

        $description = strtolower($description);

        // Escluso art. 15
        if (strpos($description, 'art. 15') !== false || strpos($description, 'art.15') !== false) {
            return 'N1';
        }

        // Non soggetto
        if (strpos($description, 'non soggett') !== false) {
            return 'N2.2';
        }

        // Non imponibile - esportazioni
        if (strpos($description, 'esportazion') !== false) {
            return 'N3.1';
        }

        // Non imponibile - cessioni intracomunitarie
        if (strpos($description, 'intracomunit') !== false) {
            return 'N3.2';
        }

        // Esente
        if (strpos($description, 'esent') !== false || strpos($description, 'art. 10') !== false) {
            return 'N4';
        }

        // Regime del margine
        if (strpos($description, 'margin') !== false) {
            return 'N5';
        }

        // Inversione contabile (reverse charge)
        if (strpos($description, 'reverse') !== false || strpos($description, 'inversione') !== false) {
            return 'N6.9';
        }

        // Default: esente generico
        return 'N2.2';
    }
}

/**
 * Ottiene la descrizione del tipo documento
 *
 * @param string $tipo_documento Codice tipo documento
 * @return string
 */
if (!function_exists('fe_get_tipo_documento_label')) {
    function fe_get_tipo_documento_label($tipo_documento)
    {
        $tipi = [
            'TD01' => 'Fattura',
            'TD02' => 'Acconto/Anticipo su fattura',
            'TD03' => 'Acconto/Anticipo su parcella',
            'TD04' => 'Nota di Credito',
            'TD05' => 'Nota di Debito',
            'TD06' => 'Parcella',
            'TD16' => 'Integrazione fattura reverse charge interno',
            'TD17' => 'Integrazione/autofattura per acquisto servizi dall\'estero',
            'TD18' => 'Integrazione per acquisto di beni intracomunitari',
            'TD19' => 'Integrazione/autofattura per acquisto di beni ex art.17',
            'TD20' => 'Autofattura per regolarizzazione e integrazione',
            'TD21' => 'Autofattura per splafonamento',
            'TD22' => 'Estrazione beni da Deposito IVA',
            'TD23' => 'Estrazione beni da Deposito IVA con versamento IVA',
            'TD24' => 'Fattura differita (art. 21, comma 4, lett. a)',
            'TD25' => 'Fattura differita (art. 21, comma 4, lett. b)',
            'TD26' => 'Cessione di beni ammortizzabili e passaggi interni',
            'TD27' => 'Fattura per autoconsumo o cessioni gratuite',
        ];

        return $tipi[$tipo_documento] ?? $tipo_documento;
    }
}

/**
 * Ottiene la descrizione dello stato SDI
 *
 * @param string $stato Stato
 * @return string
 */
if (!function_exists('fe_get_stato_label')) {
    function fe_get_stato_label($stato)
    {
        $stati = [
            'bozza'                  => _l('fe_stato_bozza'),
            'generata'               => _l('fe_stato_generata'),
            'inviata'                => _l('fe_stato_inviata'),
            'consegnata'             => _l('fe_stato_consegnata'),
            'non_consegnata'         => _l('fe_stato_non_consegnata'),
            'accettata'              => _l('fe_stato_accettata'),
            'rifiutata'              => _l('fe_stato_rifiutata'),
            'decorrenza_termini'     => _l('fe_stato_decorrenza_termini'),
            'impossibilita_recapito' => _l('fe_stato_impossibilita_recapito'),
            'scartata'               => _l('fe_stato_scartata'),
            'mancata_consegna'       => _l('fe_stato_mancata_consegna'),
            'ricevuta'               => _l('fe_stato_ricevuta'),
        ];

        return $stati[$stato] ?? $stato;
    }
}

/**
 * Ottiene la classe CSS per lo stato
 *
 * @param string $stato Stato
 * @return string
 */
if (!function_exists('fe_get_stato_class')) {
    function fe_get_stato_class($stato)
    {
        $classi = [
            'bozza'                  => 'default',
            'generata'               => 'info',
            'inviata'                => 'primary',
            'consegnata'             => 'success',
            'non_consegnata'         => 'warning',
            'accettata'              => 'success',
            'rifiutata'              => 'danger',
            'decorrenza_termini'     => 'warning',
            'impossibilita_recapito' => 'danger',
            'scartata'               => 'danger',
            'mancata_consegna'       => 'danger',
            'ricevuta'               => 'info',
        ];

        return $classi[$stato] ?? 'default';
    }
}

/**
 * Ottiene il codice modalità pagamento per FatturaPA
 *
 * @param string $payment_mode Nome modalità pagamento Perfex
 * @return string
 */
if (!function_exists('fe_get_modalita_pagamento')) {
    function fe_get_modalita_pagamento($payment_mode)
    {
        $payment_mode = strtolower($payment_mode);

        // Mapping delle modalità pagamento più comuni
        $mappings = [
            'contanti'                 => 'MP01',
            'cash'                     => 'MP01',
            'assegno'                  => 'MP02',
            'cheque'                   => 'MP02',
            'assegno circolare'        => 'MP03',
            'contanti tesoreria'       => 'MP04',
            'bonifico'                 => 'MP05',
            'bank transfer'            => 'MP05',
            'wire transfer'            => 'MP05',
            'vaglia cambiario'         => 'MP06',
            'bollettino'               => 'MP07',
            'carta di credito'         => 'MP08',
            'credit card'              => 'MP08',
            'rid'                      => 'MP09',
            'rid utenze'               => 'MP10',
            'rid veloce'               => 'MP11',
            'riba'                     => 'MP12',
            'mav'                      => 'MP13',
            'quietanza'                => 'MP14',
            'giroconto'                => 'MP15',
            'domiciliazione'           => 'MP16',
            'sepa'                     => 'MP16',
            'domiciliazione postale'   => 'MP17',
            'bollettino postale'       => 'MP18',
            'sepa dd'                  => 'MP19',
            'sepa direct debit'        => 'MP19',
            'sepa dd core'             => 'MP20',
            'sepa dd b2b'              => 'MP21',
            'trattenuta'               => 'MP22',
            'pagopa'                   => 'MP23',
        ];

        foreach ($mappings as $key => $code) {
            if (strpos($payment_mode, $key) !== false) {
                return $code;
            }
        }

        // Default: bonifico
        return 'MP05';
    }
}

/**
 * Genera il progressivo invio
 *
 * @return string
 */
if (!function_exists('fe_genera_progressivo')) {
    function fe_genera_progressivo()
    {
        $CI = &get_instance();
        $anno = date('Y');

        // Ottieni e incrementa il progressivo
        $CI->db->where('anno', $anno);
        $row = $CI->db->get(db_prefix() . 'fe_progressivo')->row();

        if (!$row) {
            $CI->db->insert(db_prefix() . 'fe_progressivo', [
                'anno'              => $anno,
                'ultimo_progressivo' => 1
            ]);
            $progressivo = 1;
        } else {
            $progressivo = $row->ultimo_progressivo + 1;
            $CI->db->where('anno', $anno);
            $CI->db->update(db_prefix() . 'fe_progressivo', [
                'ultimo_progressivo' => $progressivo
            ]);
        }

        // Formatta il progressivo (5 caratteri alfanumerici)
        $formato = get_option('fe_formato_progressivo') ?: 'XXXXX';
        $lunghezza = strlen($formato);

        // Converti in base 36 (0-9, A-Z)
        $progressivo_str = strtoupper(base_convert($progressivo, 10, 36));

        // Padding a sinistra con zeri
        return str_pad($progressivo_str, $lunghezza, '0', STR_PAD_LEFT);
    }
}

/**
 * Genera il nome file per la fattura elettronica
 *
 * @param string $partita_iva Partita IVA del cedente
 * @param string $progressivo Progressivo invio
 * @return string
 */
if (!function_exists('fe_genera_nome_file')) {
    function fe_genera_nome_file($partita_iva, $progressivo)
    {
        // Formato: IT + PartitaIVA + _ + Progressivo
        $partita_iva = preg_replace('/[^0-9]/', '', $partita_iva);
        return 'IT' . $partita_iva . '_' . $progressivo . '.xml';
    }
}

/**
 * Ottiene il percorso di upload per le fatture elettroniche
 *
 * @param string $type Tipo (attive, passive, notifiche)
 * @param int $year Anno (opzionale, default anno corrente)
 * @return string
 */
if (!function_exists('fe_get_upload_path')) {
    function fe_get_upload_path($type = 'attive', $year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $path = FCPATH . 'uploads/fatturazione_elettronica/' . $type . '/' . $year . '/';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }
}

/**
 * Log delle operazioni del modulo
 *
 * @param string $azione Azione eseguita
 * @param string $descrizione Descrizione
 * @param int|null $fattura_attiva_id ID fattura attiva
 * @param int|null $fattura_passiva_id ID fattura passiva
 */
if (!function_exists('fe_log')) {
    function fe_log($azione, $descrizione = '', $fattura_attiva_id = null, $fattura_passiva_id = null)
    {
        $CI = &get_instance();

        $CI->db->insert(db_prefix() . 'fe_log', [
            'fattura_attiva_id'  => $fattura_attiva_id,
            'fattura_passiva_id' => $fattura_passiva_id,
            'azione'             => $azione,
            'descrizione'        => $descrizione,
            'staff_id'           => get_staff_user_id(),
            'ip_address'         => $CI->input->ip_address(),
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
    }
}

/**
 * Invia notifica email
 *
 * @param string $to Destinatario
 * @param string $subject Oggetto
 * @param string $message Messaggio
 * @param array $attachments Allegati
 * @return bool
 */
if (!function_exists('fe_send_email')) {
    function fe_send_email($to, $subject, $message, $attachments = [])
    {
        if (get_option('fe_email_notifiche') != '1') {
            return false;
        }

        $CI = &get_instance();
        $CI->load->library('email');

        $CI->email->from(get_option('smtp_email'), get_option('companyname'));
        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->message($message);

        foreach ($attachments as $attachment) {
            $CI->email->attach($attachment);
        }

        return $CI->email->send();
    }
}

/**
 * Decodifica i caratteri speciali per XML
 *
 * @param string $string Stringa da decodificare
 * @return string
 */
if (!function_exists('fe_xml_encode')) {
    function fe_xml_encode($string)
    {
        $string = trim($string);
        $string = htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        return $string;
    }
}

/**
 * Pulisce una stringa per l'uso in XML FatturaPA
 *
 * @param string $string Stringa da pulire
 * @param int $maxLength Lunghezza massima
 * @return string
 */
if (!function_exists('fe_clean_string')) {
    function fe_clean_string($string, $maxLength = 0)
    {
        // Rimuovi caratteri non validi per XML
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $string);

        // Rimuovi spazi multipli
        $string = preg_replace('/\s+/', ' ', $string);

        // Trim
        $string = trim($string);

        // Tronca se necessario
        if ($maxLength > 0 && mb_strlen($string) > $maxLength) {
            $string = mb_substr($string, 0, $maxLength);
        }

        return $string;
    }
}

/**
 * Verifica se un cliente è una Pubblica Amministrazione
 *
 * @param int $client_id ID del cliente
 * @return bool
 */
if (!function_exists('fe_is_pubblica_amministrazione')) {
    function fe_is_pubblica_amministrazione($client_id)
    {
        $codice_destinatario = get_client_meta($client_id, 'fe_codice_destinatario');

        // Il codice destinatario PA è di 6 caratteri
        if ($codice_destinatario && strlen($codice_destinatario) == 6) {
            return true;
        }

        return false;
    }
}

/**
 * Ottiene il formato trasmissione corretto
 *
 * @param int $client_id ID del cliente
 * @return string FPA12 per PA, FPR12 per privati
 */
if (!function_exists('fe_get_formato_trasmissione')) {
    function fe_get_formato_trasmissione($client_id)
    {
        return fe_is_pubblica_amministrazione($client_id) ? 'FPA12' : 'FPR12';
    }
}

/**
 * Normalizza la partita IVA (rimuove IT se presente)
 *
 * @param string $partita_iva Partita IVA
 * @return string
 */
if (!function_exists('fe_normalizza_partita_iva')) {
    function fe_normalizza_partita_iva($partita_iva)
    {
        $partita_iva = strtoupper(trim($partita_iva));

        if (substr($partita_iva, 0, 2) == 'IT') {
            $partita_iva = substr($partita_iva, 2);
        }

        return preg_replace('/[^0-9A-Z]/', '', $partita_iva);
    }
}

/**
 * Determina il codice nazione da una partita IVA
 *
 * @param string $partita_iva Partita IVA
 * @return string
 */
if (!function_exists('fe_get_nazione_from_piva')) {
    function fe_get_nazione_from_piva($partita_iva)
    {
        $partita_iva = strtoupper(trim($partita_iva));

        if (preg_match('/^([A-Z]{2})/', $partita_iva, $matches)) {
            return $matches[1];
        }

        return 'IT';
    }
}

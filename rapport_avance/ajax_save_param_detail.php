<?php

/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief sauve la nouvelle ligne de détail (paramètre) et renvoie un xml
 * avec code = ok ou nok, p_id pk de formulaire_param et html le code html à afficher : soit une ligne à ajouter à la table
 * soit le message d'erreur à ajouter dans le div
 *
 *
 */
require_once NOALYSS_INCLUDE.'/lib/class_impress.php';
require_once 'include/class_formulaire_param_detail.php';
$fp_id = HtmlInput::default_value_get('fp_id', '-1');
switch ($tab)
{
    case 'account_tva':
        $acc_tva = new RAPAV_Account_Tva($fp_id);
        $acc_tva->tva_id = $code_tva;
        $acc_tva->tmp_val = $formtva;
        $acc_tva->jrn_def_type = $code_jrn;
        $acc_tva->p_id = $p_id;
        $acc_tva->type_detail = 2;
        $acc_tva->tt_id = $code_base;
        $acc_tva->jrn_def_id = $p_ledger;
        $acc_tva->date_paid=$p_paid;
        if ($acc_tva->verify() == 1)
        {
            $code = 'nok';
            $html = "Erreur dans la formule " . $acc_tva->errcode;
        } else
        {
            $acc_tva->save();
            $code = 'ok';
            $fp_id = $acc_tva->fp_id;
            $html = '<td>';
            ob_start();
            $acc_tva->display_row();
            $html.=ob_get_contents();
            ob_end_clean();
            $html.= '</td>';
            $html.=$acc_tva->button_delete();
            $html.=$acc_tva->button_modify();
        }
        break;
    case 'formula':
        $acc_formula = new RAPAV_Formula($fp_id);
        $acc_formula->fp_formula = $formula_new;
        $acc_formula->p_id = $p_id;
        $acc_formula->type_detail = 1;
        $acc_formula->jrn_def_id = $p_ledger;
        $acc_formula->date_paid=$p_paid;
        if ($acc_formula->verify() == 1)
        {
            $code = 'nok';
            $html = $acc_formula->errcode;
        } else
        {
            $acc_formula->save();
            $fp_id = $acc_formula->fp_id;
            $code = 'ok';
            $html = '<td>';
            ob_start();
            $acc_formula->display_row();
            $html.=ob_get_contents();
            ob_end_clean();
            $html.= '</td>';
            $html.=$acc_formula->button_delete();
            $html.=$acc_formula->button_modify();
        }
        break;
    case 'compute_id':
        $acc_compute = new RAPAV_Compute($fp_id);
        $acc_compute->fp_formula = $form_compute;
        $acc_compute->p_id = $p_id;
        $acc_compute->type_detail = 3;
        $acc_compute->jrn_def_id = null;
        if ($acc_compute->verify() == 1)
        {
            $code = 'nok';
            $html = $acc_compute->errcode;
        } else
        {
            $acc_compute->save();
            $fp_id = $acc_compute->fp_id;
            $code = 'ok';
            $html = '<td>';
            ob_start();
            $acc_compute->display_row();
            $html.=ob_get_contents();
            ob_end_clean();
            $html.= '</td>';
            $html.=$acc_compute->button_delete();
            $html.=$acc_compute->button_modify();
        }
        break;
    case 'new_account_id':
        $acc_account = new RAPAV_Account($fp_id);
        $acc_account->tmp_val = $account_first;
        $acc_account->with_tmp_val = $account_second;
        $acc_account->p_id = $p_id;
        $acc_account->type_detail = 4;
        $acc_account->type_sum_account = $account_sum_type;
        $acc_account->jrn_def_id = $p_ledger;
        $acc_account->date_paid=$p_paid;
        if ($acc_account->verify() == 1)
        {
            $code = 'nok';
            $html = $acc_account->errcode;
        } else
        {
            $acc_account->save();
            $fp_id = $acc_account->fp_id;
            $code = 'ok';
            $html = '<td>';
            ob_start();
            $acc_account->display_row();
            $html.=ob_get_contents();
            ob_end_clean();
            $html.= '</td>';
            $html.=$acc_account->button_delete();
            $html.=$acc_account->button_modify();
        }
        break;

    case 'new_reconcile_id':
        $acc_account = new RAPAV_Reconcile($fp_id);
        $acc_account->tmp_val = $acrec_first;
        $acc_account->with_tmp_val = $acrec_second;
        $acc_account->operation_pcm_val = $acrec_third;
        $acc_account->p_id = $p_id;
        $acc_account->type_detail = 5;
        $acc_account->type_sum_account = $account_sum_type;
        $acc_account->jrn_def_id = $p_ledger;
        if ($acc_account->verify() == 1)
        {
            $code = 'nok';
            $html = $acc_account->errcode;
        } else
        {
            $acc_account->save();
            $fp_id = $acc_account->fp_id;
            $code = 'ok';
            $html = '<td>';
            ob_start();
            $acc_account->display_row();
            $html.=ob_get_contents();
            ob_end_clean();
            $html.= '</td>';
            $html.=$acc_account->button_delete();
            $html.=$acc_account->button_modify();
        }
        break;
}
//echo $html;exit();
$html = escape_xml($html);

header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>$code</code>
<html>$html</html>
<p_id>$p_id</p_id>
<fp_id>$fp_id</fp_id>
</data>
EOF;
?>

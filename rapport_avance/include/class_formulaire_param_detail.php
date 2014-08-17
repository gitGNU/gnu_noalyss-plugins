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

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief
 *
 */
require_once 'class_rapport_avance_sql.php';
require_once 'class_rapav_formulaire.php';
require_once 'class_rapav.php';

class Formulaire_Param_Detail extends Formulaire_Param_Detail_SQL
{

    function input_new($p_id)
    {
        global $cn;
        $parent = new Formulaire_Param($p_id);
        echo HtmlInput::title_box('Formule', 'param_detail_div');
        echo '<h2>' . $parent->p_code . " " . $parent->p_libelle . '</h2>';

        require_once 'template/param_detail_new.php';
    }

    function button_delete()
    {
        $html='<td id="del_' . $this->fp_id . '">';
        $html.=HtmlInput::anchor("Effacer", "", sprintf("onclick=\"delete_param_detail('%s','%s','%s','%s')\""
                                , $_REQUEST['plugin_code'], $_REQUEST['ac'], $_REQUEST['gDossier'], $this->fp_id));
        $html.= '</td>';
        return $html;
    }
    function button_modify()
    {
        $html='<td id="mod_' . $this->fp_id . '">';
        $html.=HtmlInput::anchor("Modifier", "", sprintf("onclick=\"modify_param_detail('%s','%s','%s','%s')\""
                                , $_REQUEST['plugin_code'], $_REQUEST['ac'], $_REQUEST['gDossier'], $this->fp_id));
        $html.= '</td>';
        return $html;
    }
     /**
     * display a choice of ledger
     * @global cn
     */
    function input_ledger()
    {
        global $cn;
        $select = new ISelect('p_ledger');
        $a_ledger = $cn->make_array('select jrn_def_id,jrn_def_name from jrn_def order by 2', 1);
        $a_ledger[0]['label'] = '-- Tous les journaux -- ';
        $select->value = $a_ledger;
        if ( $this->fp_id != -1)
        {
            $select->selected=$this->jrn_def_id;
        }

        echo '<p> Filtrage par journal ' . $select->input() . '</p>';
    }

    /**
     * Display a select for the date
     */
    function input_date_paiement()
    {
        $s_date = new ISelect('p_paid');
        $s_date->value = array();
        $s_date->value[] = array("value" => 0, "label" => 'Date d\'opération');
        $s_date->value[] = array("value" => 1, "label" => 'Date de paiement');
        $s_date->value[] = array("value" => 2, "label" => 'Date d\'échéance');
        echo '<p> Si la date donnée concerne la date de paiement ou d\'écheance, cela limitera la recherche aux journaux VEN et ACH ';
        echo HtmlInput::infobulle(36);
        if ( $this->fp_id != -1)
        {
            $s_date->selected=$this->date_paid;
        }
        echo $s_date->input();
        echo '</p>';
    }

}

class RAPAV_Formula extends Formulaire_Param_Detail
{

    function display_row()
    {
        $ledger = RAPAV::get_ledger_name($this->jrn_def_id);
        $paid = RAPAV::str_date_type($this->date_paid);
        printf("Résultat de la formule %s utilisant $ledger %s", $this->fp_formula, $paid);
    }

    function input()
    {
        $account = new IPoste("formula_new", "", "form_id");
        if ($this->fp_id != -1) 
        {
            $account->value=$this->fp_formula;
        }
        $account->size = 50;
        $account->label = _("Recherche poste");
        $account->set_attribute('gDossier', dossier::id());
        $account->set_attribute('bracket', 1);
        $account->set_attribute('no_overwrite', 1);
        $account->set_attribute('noquery', 1);
        $account->set_attribute('account', $account->id);
        echo $account->input();
         $this->input_date_paiement();
        $this->input_ledger();
    }
    static function new_row()
    {
        $obj=new RAPAV_Formula();
        $obj->input();
    }
    function verify()
    {
        global $errcode;
        $ret = RAPAV::verify_compute($this->fp_formula);
        $this->errcode = $errcode;
        return $ret;
    }

}

class RAPAV_Account_Tva extends Formulaire_Param_Detail
{

    function display_row()
    {
        global $cn;
        $ledger = RAPAV::get_ledger_name($this->jrn_def_id);
        $type_total = $cn->get_value("select tt_label from rapport_advanced.total_type where tt_id=$1", array($this->tt_id));
        $paid = RAPAV::str_date_type($this->date_paid);
        printf("Poste comptable %s avec le code tva %s (%s) dans le journal de type %s [ %s ] $ledger %s", $this->tmp_val, $this->tva_id, $this->tva_id, $this->jrn_def_type, $type_total, $paid);
    }

    function input()
    {
        global $cn;
        $account = new IPoste("formtva", "", "formtva_id");
        $account->size = 20;
        $account->label = _("Recherche poste");
        $account->set_attribute('gDossier', dossier::id());
        $account->set_attribute('noquery', 1);
        $account->set_attribute('account', $account->id);
        $account->value=$this->tmp_val;

        $tva = new ITva_Popup("code_tva");
        $tva->id = HtmlInput::generate_id("code_tva");
        $tva->set_attribute('gDossier', dossier::id());
        $tva->value=$this->tva_id;
        // Jrn type
        $select = new ISelect('code_jrn');
        $select->value = array(
            array('value' => 'VEN', 'label' => 'journaux Vente'),
            array('value' => 'ACH', 'label' => 'journaux Achat')
        );
        $select->selected=$this->jrn_def_type;
        echo '<table>';
        echo '<tr><td>Poste comptable</td>';
        echo td($account->input());
        echo '</tr>';
        echo td('TVA') . td($tva->input());
        echo '</tr>';
        echo td(_('Choix du type de journal ')) . td($select->input());
        // Base or VAT
        echo '</tr>';
        $code_base = new ISelect('code_base');
        $code_base->value = $cn->make_array("select tt_id,tt_label from rapport_advanced.total_type order by 2");
        $code_base->selected=$this->tt_id;
        echo td("Type de total");
        echo td($code_base->input());
        echo '</tr>';
        echo '</table>';
        $this->input_date_paiement();
        $this->input_ledger();
    }
    static function new_row()
    {
        $obj=new RAPAV_Account_Tva();
        $obj->input();
    }
    function verify()
    {
        global $cn;
        if (trim($this->tmp_val) == "")
        {
            $this->errcode = 'Poste comptable est vide';
            return 1;
        }
        $count = $cn->get_value("select count(*) from tva_rate where tva_id=$1", array($this->tva_id));
        if ($count == 0)
        {
            $this->errcode = 'Code TVA inexistant';
            return 1;
        }
    }

}

class RAPAV_Compute extends Formulaire_Param_Detail
{

    function display_row()
    {
        printf("Total des codes du formulaire %s", $this->fp_formula);
    }

    function input()
    {
        global $cn;
        $f_id = $cn->get_value("select f_id from rapport_advanced.formulaire_param where p_id=$1", array($this->p_id));
        $account = new IText("form_compute");
        $account->size = 50;
        $account->value=$this->fp_formula;
        echo $account->input();
        echo HtmlInput::button('rapav_search_code_bt', 'Cherche codes', sprintf(" onclick=\"rapav_search_code('%s','%s','%s','%s')\"", $_REQUEST['ac'], $_REQUEST['plugin_code'], $_REQUEST['gDossier'], $f_id));
    }
    static function new_row($p_id)
    {
        $obj=new RAPAV_Compute();
        $obj->p_id=$p_id;
        $obj->input();
    }
    function verify()
    {
        global $errcode;
        $ret = RAPAV::verify_compute($this->fp_formula);
        $this->errcode = $errcode;
        return $ret;
    }

}

/**
 * @brief poste comptable utilisé avec le poste comptable, choix entre diff crédit - debit, diff débit-crédit, crédit, débit
 */
class RAPAV_Account extends Formulaire_Param_Detail
{

    function display_row()
    {
        global $cn;
        $ledger = RAPAV::get_ledger_name($this->jrn_def_id);
        $paid = RAPAV::str_date_type($this->date_paid);
        $total_type_account = $cn->get_value('select tt_label from rapport_advanced.total_type_account where tt_id=$1', array($this->type_sum_account));
        printf("Total %s poste comptable %s utilisé avec le poste comptable %s utilisant $ledger %s", $total_type_account, $this->tmp_val, $this->with_tmp_val, $paid);
    }

    function input()
    {
        global $cn;
        $sum_type = new ISelect('account_sum_type');
        $sum_type->value = $cn->make_array("select tt_id, tt_label from rapport_advanced.total_type_account ");
        $sum_type->selected=$this->type_sum_account;
        $account = new IPoste("account_first", "", "account_first_id");
        $account->size = 10;
        $account->label = _("Recherche poste");
        $account->value=$this->tmp_val;
        $account->set_attribute('gDossier', dossier::id());
        $account->set_attribute('account', $account->id);

        $account_second = new IPoste("account_second", "", "account_second_id");
        $account_second->size = 10;
        $account_second->label = _("Recherche poste");
        $account_second->set_attribute('gDossier', dossier::id());
        $account_second->set_attribute('account', $account_second->id);
        $account_second->value=$this->with_tmp_val;
        echo '<p>';
        echo 'Calculer ';
        echo $sum_type->input();
        echo '</p>';
        echo '<p>';
        echo 'du poste comptable ' . HtmlInput::infobulle(203);
        echo $account->input();
        echo '</p>';
        echo '<p>';
        echo ' utilisé avec le poste comptable ' . HtmlInput::infobulle(203);
        echo $account_second->input();
        echo '</p>';
         $this->input_date_paiement();
        $this->input_ledger();
    }
    static function new_row()
    {
        $obj=new RAPAV_Account();
        $obj->input();
    }
    function verify()
    {

        if (trim($this->tmp_val) == "" || trim($this->with_tmp_val) == "")
        {
            $this->errcode = " Un poste comptable est manquant";
            return 1;
        }
        return 0;
    }

}

/**
 * @brief poste comptable utilisé avec le poste comptable, choix entre diff crédit - debit, diff débit-crédit, crédit, débit
 */
class RAPAV_Reconcile extends Formulaire_Param_Detail
{

    function display_row()
    {
        global $cn;
        $total_type_account = $cn->get_value('select tt_label from rapport_advanced.total_type_account where tt_id=$1', array($this->type_sum_account));
        $ledger = RAPAV::get_ledger_name($this->jrn_def_id);
        printf("Total %s poste comptable %s utilisé avec le poste comptable %s rapprochée dans la période donnée
			avec une opération utilisant le poste comptable %s  utilisant $ledger", $total_type_account, $this->tmp_val, $this->with_tmp_val, $this->operation_pcm_val);
    }

    function input()
    {
        global $cn;
        $sum_type = new ISelect('account_sum_type');
        $sum_type->value = $cn->make_array("select tt_id, tt_label from rapport_advanced.total_type_account ");
        $sum_type->selected=$this->type_sum_account;
        $account = new IPoste("acrec_first", "", "acrec_first_id");
        $account->size = 10;
        $account->label = _("Recherche poste");
        $account->set_attribute('gDossier', dossier::id());
        $account->set_attribute('account', $account->id);
        $account->value=$this->tmp_val;

        $account_second = new IPoste("acrec_second", "", "acrec_second_id");
        $account_second->size = 10;
        $account_second->label = _("Recherche poste");
        $account_second->set_attribute('gDossier', dossier::id());
        $account_second->set_attribute('account', $account_second->id);
        $account_second->value=$this->with_tmp_val;
            
        $account_third = new IPoste("acrec_third", "", "acrec_third_id");
        $account_third->size = 10;
        $account_third->label = _("Recherche poste");
        $account_third->set_attribute('gDossier', dossier::id());
        $account_third->set_attribute('account', $account_third->id);
        $account_third->value=$this->operation_pcm_val;
        
        echo '<p>';
        echo 'Calculer ';
        echo $sum_type->input();
        echo '</p>';
        echo '<p>';
        echo 'du poste comptable ' . HtmlInput::infobulle(203);
        echo $account->input();
        echo '</p>';
        echo '<p>';
        echo ' utilisé avec le poste comptable ' . HtmlInput::infobulle(203);
        echo $account_second->input();
        echo '</p>';
        echo '<p>';
        echo ' rapproché avec une opération dans la période donnée utilisant le poste comptable ' . HtmlInput::infobulle(203);
        echo $account_third->input();

        echo '</p>';
        $this->input_ledger();
    }
    static function new_row()
    {
        $obj=new RAPAV_Reconcile();
        $obj->input();
    }
    function verify()
    {

        if (trim($this->tmp_val) == "" || trim($this->with_tmp_val) == "" || trim($this->operation_pcm_val) == '')
        {
            $this->errcode = " Un poste comptable est manquant";
            return 1;
        }
        return 0;
    }

}

?>

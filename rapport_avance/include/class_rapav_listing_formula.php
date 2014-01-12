<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rapav_listing_formula
 *
 * @author dany
 */
require_once 'class_impress.php';
require_once 'class_rapav.php';
require_once 'class_rapport_avance_sql.php';

abstract class RAPAV_Listing_Formula
{

    abstract public function compute($p_start, $p_end);

    abstract public function input();

    abstract function save($p_array);

    function save_computed()
    {
        $this->detail->lf_id = $this->fiche->lf_id;
        $this->detail->lp_id = $this->data->lp_id;
        $this->detail->lc_code = $this->data->lp_code;
        $this->detail->lc_comment = $this->data->lp_comment;
        $this->detail->lc_order = $this->data->l_order;
        $this->detail->lc_histo = $this->data->lp_histo;
        $this->detail->save();
        $this->fiche->save();
    }

    function set_listing_compute($param)
    {
        $this->detail->lc_id = $param;
        $this->fiche->lc_id = $param;
    }

    function set($p_array)
    {
        $this->data->setp("code", $p_array["code_id"]);
        $this->data->setp("comment", $p_array["comment"]);
        $this->data->setp("order", $p_array["order"]);
    }

    function set_to_null($p_array)
    {
        foreach ($p_array as $key)
        {
            $this->data->$key = null;
        }
    }

    static function make_object(RAPAV_Listing_Param_SQL $obj)
    {
        switch ($obj->getp('formula_type'))
        {
            case 'ATTR':
                $ret = new Rapav_Formula_Attribute($obj);
                break;
            case 'FORM':
                $ret = new Rapav_Formula_Formula($obj);
                break;
            case 'ACCOUNT':
                $ret = new Rapav_Formula_Account($obj);
                break;
            case 'COMP':
                $ret = new Rapav_Formula_Compute($obj);
                break;
            default:
                throw new Exception('Object ' . var_export($obj, true) . ' invalide ');
                break;
        }
        return $ret;
    }

    function display_code()
    {
        return $this->data->getp('code');
    }

    function display_comment()
    {
        return $this->data->getp('comment');
    }

    function display_order()
    {
        return $this->data->getp('order');
    }

    function load()
    {
        $this->data->load();
    }

    function set_listing($p_id)
    {
        $this->data->setp('listing_id', $p_id);
    }

    function set_fiche($f_id)
    {
        $this->fiche->f_id = $f_id;
    }

    function filter_operation($param)
    {
        $this->type_operation = $param;
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
        if ($this->data->lp_id!= -1)
        {
            $select->selected = $this->data->jrn_def_id;
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
        if ($this->data->lp_id != -1)
        {
            $s_date->selected = $this->data->date_paid;
        }
        echo $s_date->input();
        echo '</p>';
    }

}

///////////////////////////////////////////////////////////////////////////////////////////////////
// RAPAV_Formula_Attribute
///////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * @brief Class for the listing detail attribute, this class use RAPAV_Listing_Param_SQL
 * the specific columns are 
 *   - attribut_card
 */
class RAPAV_Formula_Attribute extends RAPAV_Listing_Formula
{

    /**
     * RAPAV_Listing_Param_SQL objet */
    var $data;

    /**
     * < RAPAV_Listing_Compute_Fiche_SQL object */
    var $fiche;

    /**
     * < RAPAV_Listing_Compute_Detail_SQL object */
    var $detail;

    /**
     * categorie id */
    var $cat;

    /**
     * Object signature */
    var $sig;

    function __construct(RAPAV_Listing_Param_SQL $obj)
    {
        global $cn;
        $this->data = $obj;
        if ($this->data->getp('lp_id') > 0)
        {
            $this->cat = $cn->get_value('select fd_id 
                                from rapport_advanced.listing 
                                where
                                l_id=$1
                                ', array($this->data->getp('listing_id')));
            if ($this->cat == "")
                throw new Exception(__FILE__ . ':' . __LINE__ . 'Aucune catégorie définie');
        }
        $this->sig = 'ATTR';
        $this->fiche = new RAPAV_Listing_Compute_Fiche_SQL();
        $this->detail = new RAPAV_Listing_Compute_Detail_SQL();
    }

    function display()
    {
        global $cn;
        $desc = $cn->get_value('select ad_text from attr_def where ad_id=$1', array($this->data->getp('attribut_card')));
        return "Utilisant l'attribut " . h($desc);
    }

    function compute($p_start, $p_end)
    {
        global $cn;
        $value = $cn->get_value("select ad_value from fiche_detail "
                . "where "
                . "f_id=$1 and ad_id=$2", array($this->fiche->f_id,
            $this->data->getp('attribut_card')));
        $type = $cn->get_value('select ad_type from attr_def where 
                ad_id=$1', array($this->data->getp('attribut_card')));
        switch ($type)
        {
            case "numeric":
                $this->detail->ld_value_numeric = $value;
                break;
            case "date":
                $this->detail->ld_value_date = $value;
                break;
            default:
                $this->detail->ld_value_text = $value;
                break;
        }
    }

    function input()
    {
        global $cn;
        $select = new ISelect('p_attribute');

        $select->value = $cn->make_array('select a.ad_id,a.ad_text 
                                        from
                                        attr_def as a join jnt_fic_attr as j on (a.ad_id=j.ad_id)
                                        where
                                        fd_id=' . $this->data->getp('listing_id') . ' order by 2');

        $select->selected = $this->data->getp('attribut_card');
        return "Attribut à afficher pour chaque fiche " . $select->input();
    }

    function save($p_array)
    {
        parent::set($p_array);
        $this->data->setp('listing_id', $p_array['listing_id']);
        /* Clean everything but keep the lp_id, l_id, with_Card and ad_id  + common */
        $a_toclean = explode(',', 'operation_pcm_val,with_tmp_val,tmp_val, date_paid,jrn_def_id,type_sum_account, tt_id, jrn_def_type, fp_signed, fp_formula, tva_id' .
                ',lp_card_saldo');

        parent::set_to_null($a_toclean);
        $this->data->setp('with_card', 'N');
        $this->data->setp('attribut_card', $p_array['p_attribute']);
        $this->data->setp('formula_type', 'ATTR');
        $this->data->save();
    }

}

///////////////////////////////////////////////////////////////////////////////
// RAPAV_Formula_Formula
///////////////////////////////////////////////////////////////////////////////
/**
 * @brief Class for the listing detail attribute, this class 
 * use RAPAV_Listing_Param_SQL the specific columns are 
 *   - fp_formula
 *   - jrn_def_id
 *   - date_paid
 *  
 */
class RAPAV_Formula_Formula extends RAPAV_Listing_Formula
{

    /**
     * < RAPAV_Listing_Param_SQL objet */
    var $data;

    /**
     * < RAPAV_Listing_Compute_Fiche_SQL object */
    var $fiche;

    /**
     * < Object signature 
     */
    var $sig;

    /**
     * < RAPAV_Listing_Compute_Detail_SQL object */
    var $detail;

    function __construct(RAPAV_Listing_Param_SQL $obj, $p_cat_id = 0)
    {
        global $cn;
        $this->data = $obj;
        $this->sig = 'FORM';
        $this->fiche = new RAPAV_Listing_Compute_Fiche_SQL();
        $this->detail = new RAPAV_Listing_Compute_Detail_SQL();
    }

    function display()
    {
        $ledger = RAPAV::get_ledger_name($this->data->jrn_def_id);
        ;
        $paid = RAPAV::str_date_type($this->data->date_paid);
        $str = sprintf("Résultat de la formule %s utilisant $ledger %s", $this->data->fp_formula, $paid);
        return $str;
    }

    function compute($p_start, $p_end)
    {
        global $cn;
        $sql = "";
        switch ($this->type_operation)
        {
            case 0:
                /* --all operation -- */
                $sql_filter_operation = "";
                break;
            case '1':
                /* -- only paid -- */
                $sql_filter_operation = " and j_id in (select j_id from jrnx join 
                    jrn on (j_grpt=jr_grpt_id) where jr_rapt='paid')";
                break;
            case '2':
                /* -- only unpaid -- */
                $sql_filter_operation = " and j_id in (select j_id from jrnx join 
                    jrn on (j_grpt=jr_grpt_id) 
                    join jrn_def on (jrn_def_id=jr_def_id)
                    where coalesce(jr_rapt,'')='' and jrn_def_type in ('ACH','VEN'))";
                break;
        }
        if ($this->data->jrn_def_id != null)
        {
            $sql = ' and j_jrn_def =' . $this->data->jrn_def_id;
        }
        if ($this->data->date_paid == 1)
        {
            $sql.=sprintf(" and j_id in ( select j_id from jrnx join jrn on (j_grpt=jr_grpt_id) where jr_date_paid >= to_date('%s','DD.MM.YYYY') and jr_date_paid <= to_date ('%s','DD.MM.YYYY'))", $p_start, $p_end);
            $p_start = '01.01.1900';
            $p_end = '01.01.2100';
        }
        if ($this->data->date_paid == 2)
        {
            $sql.=sprintf(" and j_id in ( select j_id from jrnx join jrn on (j_grpt=jr_grpt_id) where jr_ech >= to_date('%s','DD.MM.YYYY') and jr_ech <= to_date ('%s','DD.MM.YYYY'))", $p_start, $p_end);
            $p_start = '01.01.1900';
            $p_end = '01.01.2100';
        }
        $sql.=$sql_filter_operation;
        $amount = Impress::parse_formula($cn, "", $this->data->fp_formula, $p_start, $p_end, true, 1, $sql);
        $this->detail->ld_value_numeric = $amount['montant'];
    }

    function input()
    {
        $account = new IPoste("p_formula", "", "formula_input_id");
        $account->size = 50;
        $account->label = _("Recherche poste");
        $account->set_attribute('gDossier', dossier::id());
        $account->set_attribute('bracket', 1);
        $account->set_attribute('no_overwrite', 1);
        $account->set_attribute('noquery', 1);
        $account->set_attribute('account', "formula_input_id");
        $account->value=$this->data->fp_formula;
        echo $account->input();
        $this->input_date_paiement();
        $this->input_ledger();
    }

    function save($p_array)
    {
        parent::set($p_array);
        $this->data->setp('listing_id', $p_array['listing_id']);
        /* Clean everything but keep the lp_id, l_id, with_Card and ad_id  + common */
        $a_toclean = explode(',', 'operation_pcm_val,with_tmp_val,tmp_val, '
                . 'type_sum_account, tt_id, '
                . 'fp_signed,  tva_id'
                . ',lp_card_saldo,attribut_card');

        parent::set_to_null($a_toclean);
        $this->data->setp('with_card', 'N');
        $this->data->setp('formula', $p_array['p_formula']);
        $this->data->setp('date_paid', $p_array['p_paid']);
        $this->data->setp('jrn_def_id', $p_array['p_ledger']);
        $this->data->setp('formula_type', 'FORM');
        $this->data->save();
    }

    /**
     * @brief check if the formula is valid, return 1 for an error
     * and set errode to the error
     */
    function verify()
    {
        global $errcode;
        $ret = RAPAV::verify_formula($this->data->fp_formula);
        $this->errcode = $errcode;
        return $ret;
    }

}

///////////////////////////////////////////////////////////////////////////////
// RAPAV_Formula_Compute
///////////////////////////////////////////////////////////////////////////////
/**
 * @brief Class for the listing detail compute,
 * Formula compute the already computed code
 *  this class 
 * use RAPAV_Listing_Param_SQL the specific columns are 
 *   - fp_formula
 *  
 */
class RAPAV_Formula_Compute extends RAPAV_Listing_Formula
{

    /**
     * < RAPAV_Listing_Param_SQL objet */
    var $data;

    /**
     * < RAPAV_Listing_Compute_Fiche_SQL object */
    var $fiche;

    /**
     * < Object signature 
     */
    var $sig;

    /**
     * < RAPAV_Listing_Compute_Detail_SQL object */
    var $detail;

    function __construct(RAPAV_Listing_Param_SQL $obj)
    {
        global $cn;
        $this->data = $obj;
        $this->sig = 'COMP';
        $this->fiche = new RAPAV_Listing_Compute_Fiche_SQL();
        $this->detail = new RAPAV_Listing_Compute_Detail_SQL();
    }

    function display()
    {
        $str = sprintf("Formule avec les codes du formulaire %s ", $this->data->fp_formula);
        return $str;
    }

    function compute($p_start, $p_end)
    {
        global $cn;
        $amount = 0;
        bcscale(2);

        // copy $this->form->fp_formula to a variable
        $formula = $this->data->fp_formula;

        // split the string from  into smaller piece
        preg_match_all("/\[([A-Z]*[0-9]*)*([0-9]*[A-Z]*)\]/i", $formula, $e);
        $tmp = $e[0];

        foreach ($tmp as $piece)
        {
            // Find the code in the database
            $search = str_replace('[', '', $piece);
            $search = str_replace(']', '', $search);
            $value = $cn->get_value('select coalesce(sum(ld_value_numeric),0) as value
				from 
                                    rapport_advanced.listing_compute_detail as ld
                                    join rapport_advanced.listing_compute_fiche as lf on (ld.lf_id=lf.lf_id) 
                                    join rapport_advanced.listing_param  as lp on (ld.lp_id=lp.lp_id) 
                                where 
                                ld.lc_id=$1 
                                and lp.lp_code=$2
                                and lf.f_id = $3
                                ', array($this->detail->lc_id, $search, $this->fiche->f_id));
            $formula = str_replace($piece, $value, $formula);
           
        }
        /** Protect against division by zero */
        if (strpos("1" . $formula, "/0.0000") != 0)
        {
            $amount = 0;
        } else
        {
            eval('$amount = ' . $formula . ';');
        }
        //
        $this->detail->ld_value_numeric = $amount;
    }

    function input()
    {
        global $cn;
        $f_id = $this->data->getp('listing_id');
        $account = new IText("form_compute");
        $account->value=$this->data->fp_formula;
        $account->size = 50;
        echo $account->input();
        echo HtmlInput::button('listing_search_code_bt', 'Cherche codes', sprintf(" onclick=\"listing_search_code('%s','%s','%s','%s')\"", $_REQUEST['ac'], $_REQUEST['plugin_code'], $_REQUEST['gDossier'], $f_id));
    }

    function save($p_array)
    {
        parent::set($p_array);
        $this->data->setp('listing_id', $p_array['listing_id']);

        /* Clean everything but keep the lp_id, l_id, ad_id  + common */
        $a_toclean = explode(',', 'operation_pcm_val,with_tmp_val,'
                . 'tmp_val,date_paid,jrn_def_id,type_sum_account,type_detail,'
                . 'tt_id,jrn_def_type,fp_signed,tva_id,lp_with_card,'
                . 'lp_card_saldo,ad_id');

        parent::set_to_null($a_toclean);
        $this->data->setp('formula', $p_array['form_compute']);
        $this->data->setp('formula_type', 'COMP');
        $this->data->save();
    }

    /**
     * @brief check if the formula is valid, return 1 for an error
     * and set errode to the error
     */
    function verify()
    {
        global $errcode;
        $ret = RAPAV::verify_compute($this->data->fp_formula);
        $this->errcode = $errcode;
        return $ret;
    }

}

///////////////////////////////////////////////////////////////////////////////
// RAPAV_Formula_Saldo
///////////////////////////////////////////////////////////////////////////////
/**
 * @brief Class for the listing detail account, 
 * Compute the saldo of account used with a given accounting, the type of 
 * saldo can be
 *   - C
 *   - D
 *   - D - C
 * You can also choose to get the account saldo of the card or of the given
 * accounting
 * 
 * this class use RAPAV_Listing_Param_SQL the specific columns are 
 *   - fp_formula
 *   - jrn_def_id
 *   - date_paid
 *   - lp_card_saldo
 *   - type_sum_account
 *  
 */
class RAPAV_Formula_Account extends RAPAV_Listing_Formula
{

    /**
     * < RAPAV_Listing_Param_SQL objet */
    var $data;

    /**
     * < RAPAV_Listing_Compute_Fiche_SQL */
    var $fiche;

    /**
     * < Object signature 
     */
    var $sig;

    /**
     * < RAPAV_Listing_Compute_Detail_SQL object */
    var $detail;

    function __construct(RAPAV_Listing_Param_SQL $obj)
    {
        global $cn;
        $this->data = $obj;
        $this->sig = 'ACCOUNT';
        $this->fiche = new RAPAV_Listing_Compute_Fiche_SQL();
        $this->detail = new RAPAV_Listing_Compute_Detail_SQL();
    }

    function display()
    {
        $ledger = RAPAV::get_ledger_name($this->data->jrn_def_id);
        $paid = RAPAV::str_date_type($this->data->date_paid);
        $a_sum = explode(',', 'invalid,Débit-Crédit,Crédit-Débit,Débit,Crédit');
        $sumof = ($this->data->lp_card_saldo == 0) ? " du poste comptable" : "de la fiche";
        $str = sprintf("%s %s %s utilisant $ledger %s", $a_sum[$this->data->type_sum_account], $sumof, $this->data->fp_formula, $paid);
        return $str;
    }

    function compute($p_start, $p_end)
    {
        global $cn;
        $this->histo = array();
        $filter_ledger = "";
        if ($this->data->jrn_def_id != "")
        {
            $filter_ledger = " and jrn1.j_jrn_def = " . sql_string($this->data->jrn_def_id);
        }

        $card_saldo = ($this->data->lp_card_saldo == 0) ? "jrn1" : "jrn2";
        $sql_date = RAPAV::get_sql_date($this->data->date_paid, $card_saldo);
        switch ($this->type_operation)
        {
            case 0:
                /* --all operation -- */
                $sql_filter_operation = "";
                break;
            case '1':
                /* -- only paid -- */
                $sql_filter_operation = " and $card_saldo.j_id in (select j_id from jrnx join 
                    jrn on (j_grpt=jr_grpt_id) where jr_rapt='paid')";
                break;
            case '2':
                /* -- only unpaid -- */
                $sql_filter_operation = " and $card_saldo.j_id in (select j_id from jrnx join 
                    jrn on (j_grpt=jr_grpt_id) 
                    join jrn_def on (jrn_def_id=jr_def_id)
                    where coalesce(jr_rapt,'')='' and jrn_def_type in ('ACH','VEN'))";
                break;
        }
        bcscale(2);
        switch ($this->data->type_sum_account)
        {
            // Saldo
            case 1:
            case 2:
                 $card_saldo = ($this->data->lp_card_saldo == 0) ? "jrn1" : "jrn2";
                 $card_saldo = ($this->data->lp_card_saldo == 0) ? "jrn1" : "jrn2";
                // Compute D-C
                $sql = "
                        
                         (
                                select distinct $card_saldo.j_id,$card_saldo.j_grpt,case when $card_saldo.j_debit = 't' then $card_saldo.j_montant else $card_saldo.j_montant*(-1) end as jrnx_amount
                                from jrnx as jrn1
                                join jrnx as jrn2 on (jrn1.j_grpt=jrn2.j_grpt)
                                where
                                jrn1.j_poste like $1
                                $sql_date
                                and
                                jrn2.f_id = $4
                                $filter_ledger
                                $sql_filter_operation    
                                ) as tv_amount
							 ";
                $amount = $cn->get_value("select sum(jrnx_amount) from " . $sql, array(
                    $this->data->fp_formula,
                    $p_start,
                    $p_end,
                    $this->fiche->f_id
                ));
                if ($this->data->lp_histo == 1)
                {
                    $this->histo = $cn->get_array("select distinct jr_id from 
                        jrn join $sql on (j_grpt=jr_grpt_id) ", array(
                        $this->data->fp_formula,
                        $p_start,
                        $p_end,
                        $this->fiche->f_id));
                }
                // if C-D is asked then reverse the result
                if ($this->data->type_sum_account == 2)
                    $amount = bcmul($amount, -1);
                break;
            // Only DEBIT
            case 3:
                $sql = "
                         (
                                select distinct $card_saldo.j_id,$card_saldo.j_grpt,$card_saldo.j_montant as jrnx_amount
                                from jrnx as jrn1
                                join jrnx as jrn2 on (jrn1.j_grpt=jrn2.j_grpt)
                                where
                                jrn1.j_poste like $1
                                $sql_date
                                and
                                jrn2.f_id = $4
                                and
                                $card_saldo.j_debit='t'
                                $filter_ledger
                                $sql_filter_operation
                                ) as tv_amount
							 ";
                $amount = $cn->get_value("select sum(jrnx_amount) from" . $sql, array(
                    $this->data->fp_formula,
                    $p_start,
                    $p_end,
                    $this->fiche->f_id
                ));
                if ($this->data->lp_histo == 1)
                {
                    $this->histo = $cn->get_array("select distinct jr_id from 
                        jrn join $sql on (j_grpt=jr_grpt_id) ", array(
                        $this->data->fp_formula,
                        $p_start,
                        $p_end,
                        $this->fiche->f_id));
                }

                break;
            // Only CREDIT
            case 4:
                $sql = "
                         (
                                select distinct $card_saldo.j_id,$card_saldo.j_grpt,$card_saldo.j_montant as jrnx_amount
                                from jrnx as jrn1
                                join jrnx as jrn2 on (jrn1.j_grpt=jrn2.j_grpt)
                                where
                                jrn1.j_poste like $1
                                $sql_date
                                and
                                jrn2.f_id = $4
                                and
                                $card_saldo.j_debit='f'
                                $filter_ledger
                                $sql_filter_operation
                                ) as tv_amount
							 ";
                $amount = $cn->get_value("select sum(jrnx_amount) from " . $sql, array(
                    $this->data->fp_formula,
                    $p_start,
                    $p_end,
                    $this->fiche->f_id
                ));
                if ($this->data->lp_histo == 1)
                {
                    $this->histo = $cn->get_array("select distinct jr_id from 
                        jrn join $sql on (j_grpt=jr_grpt_id) ", array(
                        $this->data->fp_formula,
                        $p_start,
                        $p_end,
                        $this->fiche->f_id));
                }

                break;

            default:
                if (DEBUG)
                    var_dump($this);
                die(__FILE__ . ":" . __LINE__ . " UNKNOW SUM TYPE");
                break;
        }
        /*
         * 4 possibilities with type_sum_account
         */
        if ($amount == "")
            $amount = 0;
        $this->detail->ld_value_numeric = $amount;
    }

    function save_computed()
    {
        parent::save_computed();
        /*
         * Save history now
         */
        if ($this->data->lp_histo == 1)
        {
            for ($e = 0; $e < count($this->histo); $e++)
            {
                $histo = new RAPAV_Listing_Compute_Historique_SQL();
                $histo->jr_id = $this->histo[$e]['jr_id'];
                $histo->ld_id = $this->detail->ld_id;
                $histo->save();
                unset($histo);
            }
        }
    }

    function input()
    {
        global $cn;
        $histo_operation = new ICheckBox('histo');
        $histo_operation->set_check( $this->data->lp_histo);
        $account = new IPoste("p_formula", "", "formula_acc_input_id");
        $account->label = _("Recherche poste");
        $account->set_attribute('gDossier', dossier::id());
        $account->set_attribute('account', "formula_acc_input_id");
        $account->value=$this->data->fp_formula;
        echo "Poste comptable utilisée avec chaque fiche " . $account->input();
        $sel_total_type_row = new ISelect('tt_id');
        $sel_total_type_row->value = $cn->make_array('select tt_id,tt_label from '
                . ' rapport_advanced.total_type_account order by 2');
        $sel_total_type_row->selected=$this->data->type_sum_account;
        echo '<p>';
        echo "Reprendre historique opération: " . $histo_operation->input();
        echo '</p>';

        echo '<p>';
        echo "type de total : " . $sel_total_type_row->input();
        echo '</p>';

        $ck = new ICheckBox('card_saldo');
        $ck->value=1;
        $ck->set_check($this->data->lp_card_saldo);
        echo '<p>';
        echo 'Prendre le total de la fiche ' . $ck->input();
        echo '</p>';
        $this->input_date_paiement();
        $this->input_ledger();
    }

    function save($p_array)
    {
        parent::set($p_array);
        $this->data->setp('listing_id', $p_array['listing_id']);
        /* Clean everything but keep the lp_id, l_id, with_Card and ad_id  + common */
        $a_toclean = explode(',', 'operation_pcm_val,with_tmp_val,tmp_val, '
                . 'type_sum_account, tt_id, '
                . 'fp_signed,  tva_id'
                . ',lp_card_saldo,attribut_card');

        parent::set_to_null($a_toclean);
        $this->data->setp('with_card', 'N');
        $this->data->setp('formula', $p_array['p_formula']);
        $this->data->setp('date_paid', $p_array['p_paid']);
        $this->data->setp('jrn_def_id', $p_array['p_ledger']);
        $this->data->setp('formula_type', 'ACCOUNT');
        $this->data->setp('sum_signed', $p_array['tt_id']);
        $this->data->lp_card_saldo = (isset($p_array['card_saldo'])) ? 1 : 0;
        $this->data->lp_histo = (isset($p_array['histo'])) ? 1 : 0;
        $this->data->save();
    }

    /**
     * @brief check if the formula is valid, return 1 for an error
     * and set errode to the error
     * @todo verifier que le poste comptable existe
     */
    function verify()
    {
        return 0;
    }

}

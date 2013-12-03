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
        $this->detail->lf_id=$this->fiche->lf_id;
        $this->detail->lp_id=$this->data->lp_id;
        $this->detail->save();
    }
    function set_listing_compute($param)
    {
        $this->detail->lc_id=$param;
        $this->fiche->lc_id=$param;
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


    function save_fiche()
    {
        $this->fiche->save();
    }
    function filter_operation($param)
    {
        $this->type_operation=$param;
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
     * < RAPAV_Listing_Compute_SQL object */
    var $fiche;

    /**
     * < RAPAV_Listing_Detail_SQL object */
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
        $value=$cn->get_value("select ad_value from fiche_detail "
                . "where "
                . "f_id=$1 and ad_id=$2",array($this->fiche->f_id,
                    $this->data->getp('attribut_card')));
        $type=$cn->get_value('select ad_type from attr_def where 
                ad_id=$1',array($this->data->getp('attribut_card')));
        switch ($type)
        {
            case "numeric":
                $this->detail->ld_value_numeric=$value;
                break;
            case "date":
                $this->detail->ld_value_date=$value;
                break;
            default:
                $this->detail->ld_value_text=$value;
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
     * < RAPAV_Listing_Detail_SQL object */
    var $fiche;

    /**
     * < Object signature 
     */
    var $sig;

    /**
     * < RAPAV_Listing_Detail_SQL object */
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
        return 0;
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
        echo $account->input();
        RAPAV::input_date_paiement();
        RAPAV::input_ledger();
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
        $ret = RAPAV::verify_compute($this->data->fp_formula);
        $this->errocode = $errcode;
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
     * < RAPAV_Listing_Detail_SQL object */
    var $fiche;

    /**
     * < Object signature 
     */
    var $sig;

    /**
     * < RAPAV_Listing_Detail_SQL object */
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
        return 0;
    }

    function input()
    {
        global $cn;
        $f_id = $this->data->getp('listing_id');
        $account = new IText("form_compute");
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
        $this->errocode = $errcode;
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
 *   - tt
 *  
 */
class RAPAV_Formula_Account extends RAPAV_Listing_Formula
{

    /**
     * < RAPAV_Listing_Param_SQL objet */
    var $data;

    /**
     * < RAPAV_Listing_Detail_SQL object */
    var $fiche;

    /**
     * < Object signature 
     */
    var $sig;

    /**
     * < RAPAV_Listing_Detail_SQL object */
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
        ;
        $paid = RAPAV::str_date_type($this->data->date_paid);
        $str = sprintf("Résultat de la formule %s utilisant $ledger %s", $this->data->fp_formula, $paid);
        return $str;
    }

    function compute($p_start, $p_end)
    {
        return 0;
    }

    function input()
    {
        global $cn;
        $account = new IPoste("p_formula", "", "formula_acc_input_id");
        $account->label = _("Recherche poste");
        $account->set_attribute('gDossier', dossier::id());
        $account->set_attribute('account', "formula_acc_input_id");
        echo "Poste comptable utilisée avec chaque fiche " . $account->input();
        $sel_total_type_row = new ISelect('tt_id');
        $sel_total_type_row->value = $cn->make_array('select tt_id,tt_label from '
                . ' rapport_advanced.total_type_account order by 2');

        echo '<p>';
        echo "type de total : " . $sel_total_type_row->input();
        echo '</p>';

        $ck = new ICheckBox('card_saldo');
        echo '<p>';
        echo 'Prendre le total de la fiche ' . $ck->input();
        echo '</p>';
        RAPAV::input_date_paiement();
        RAPAV::input_ledger();
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

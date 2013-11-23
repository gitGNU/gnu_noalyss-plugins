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
abstract class RAPAV_Listing_Formula
{

    abstract public function compute($p_start, $p_end);

    abstract public function input();

    abstract function save($p_array);

    function save_computed()
    {
        
    }

    function get_ledger_name()
    {
        global $cn;
        $ledger = "";
        if ($this->data->jrn_def_id == null || $this->data->jrn_def_id == -1)
        {
            $ledger = " tous les journaux";
        } else
        {
            $tledger = $cn->get_value('select jrn_def_name from jrn_def where jrn_def_id=$1', array($this->data->jrn_def_id));
            $ledger.="  le journal " . $tledger;
        }
        return $ledger;
    }

    static function input_ledger()
    {
        global $cn;
        $select = new ISelect('p_ledger');
        $a_ledger = $cn->make_array('select jrn_def_id,jrn_def_name from jrn_def order by 2', 1);
        $a_ledger[0]['label'] = '-- Tous les journaux -- ';
        $select->value = $a_ledger;

        echo '<p> Filtrage par journal ' . $select->input() . '</p>';
    }

    static function input_date_paiement()
    {
        $ck_paid = new ICheckBox('p_paid');
        echo '<p> La date donnée concerne la date de paiement, ce qui limitera la recherche aux journaux VEN et ACH ';
        echo HtmlInput::infobulle(36);
        echo $ck_paid->input();
        echo '</p>';
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
                $ret=new Rapav_Formula_Attribute($obj);
                break;

            default:
                throw new Exception ('Object '.$obj.' invalide ');
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

}

///////////////////////////////////////////////////////////////////////////////////////////////////
// RAPAV_Formula_Attribute
///////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * @brief Class for the listing detail attribute, this class use RAPAV_Listing_Param_SQL
 * the specific columns are attribut_card
 */
class RAPAV_Formula_Attribute extends RAPAV_Listing_Formula
{

    var $data;           /**< RAPAV_Listing_Param_SQL objet */
    var $cat;            /**< categorie id */
    var $sig;            /**< Object signature */

    function __construct(RAPAV_Listing_Param_SQL $obj, $p_cat_id = 0)
    {
        global $cn;
        $this->data = $obj;
        if ($p_cat_id == 0)
        {
            $this->cat = $cn->get_value('select fd_id 
                                from rapport_advanced.listing 
                                where
                                l_id=$1
                                ', array($this->data->getp('listing_id')));
            if ($this->cat == "")
                throw new Exception(__FILE__ . ':' . __LINE__ . 'Aucune catégorie définie');
        }
        else
        {
            $this->cat = $p_cat_id;
        }
        $this->sig = 'ATTR';
    }

    function display()
    {
        global $cn;
        $desc = $cn->get_value('select ad_text from attr_def where ad_id=$1', array($this->data->getp('attribut_card')));
        return "Utilisant l'attribut " . h($desc);
    }

    function compute($p_start, $p_end)
    {
        return 0;
    }

    function input()
    {
        global $cn;
        $select = new ISelect('p_attribute');

        $select->value = $cn->make_array('select a.ad_id,a.ad_text 
                                        from
                                        attr_def as a join jnt_fic_attr as j on (a.ad_id=j.ad_id)
                                        where
                                        fd_id=' . $this->cat . ' order by 2');

        $select->selected = $this->data->getp('attribut_card');
        return $select->input();
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

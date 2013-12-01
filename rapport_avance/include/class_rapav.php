<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Common function to RAPAV_Listing RAPAV_Formulaire and RAPAV_Declaration
 *
 * @author dany
 */
class RAPAV
{

    /**
     * Return the ledger's name of p_jrn
     * @param $p_jrn jrn_def::jrn_def_id if -1, it means all the ledger
     * @return string
     */
    static function get_ledger_name($p_jrn)
    {
        global $cn;
        $ledger = "";
        if ($p_jrn == null || $p_jrn == -1)
        {
            $ledger = " tous les journaux";
        } else
        {
            $tledger = $cn->get_value('select jrn_def_name from jrn_def where jrn_def_id=$1', array($p_jrn));
            $ledger.="  le journal " . $tledger;
        }
        return $ledger;
    }
    /**
     * display a choice of ledger
     * @global cn
     */
    static function input_ledger()
    {
        global $cn;
        $select = new ISelect('p_ledger');
        $a_ledger = $cn->make_array('select jrn_def_id,jrn_def_name from jrn_def order by 2', 1);
        $a_ledger[0]['label'] = '-- Tous les journaux -- ';
        $select->value = $a_ledger;

        echo '<p> Filtrage par journal ' . $select->input() . '</p>';
    }
    /**
     * Display a select for the date
     */
    static function input_date_paiement()
    {
        $s_date = new ISelect('p_paid');
        $s_date->value = array();
        $s_date->value[] = array("value" => 0, "label" => 'Date calendrier');
        $s_date->value[] = array("value" => 1, "label" => 'Date de paiement');
        $s_date->value[] = array("value" => 2, "label" => 'Date d\'échéance');
        echo '<p> Si la date donnée concerne la date de paiement ou d\'écheance, cela limitera la recherche aux journaux VEN et ACH ';
        echo HtmlInput::infobulle(36);
        echo $s_date->input();
        echo '</p>';
    }
    /**
     * Compute the string to display for date
     * @param $p_type
     * @return string
     * @throws Exception
     */
    static function str_date_type($p_type)
    {
        switch ($p_type)
        {
            case 0:
                return "la date concerne la date d'opération";
                break;
            case 1:
                return "la date concerne la date de paiement, la recherche sera limitée au journaux de type ACH & VEN";
                break;
            case 2:
                return "la date concerne la date d'échéance, la recherche sera limitée au journaux de type ACH & VEN";
                break;
        }
        throw new Exception('str_date_type : type de date inconnu');
    }

    /**
     * Compute the SQL for the date
     *    -  0  given date
     *    -  1  Date of payment
     *    -  2  Limit Date 
     * @param $p_date integer
     * @return string
     * @throws Exception if $p_date not valid
     */
    static function get_sql_date($p_date)
    {
        switch ($p_date)
        {
            case 0:
                $sql_date = "and (jrn1.j_date >= to_date($2,'DD.MM.YYYY') and jrn1.j_date <= to_date($3,'DD.MM.YYYY'))";
                break;
            case 1:
                $sql_date = " and j_id in 
                (select j_id from jrnx join jrn on (j_grpt = jr_grpt_id)
                    where
                    coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) >= to_date($2,'DD.MM.YYYY')
                    and coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) <= to_date($3,'DD.MM.YYYY')
                 )
                    ";
                break;
            case 2:
                $sql_date = " and j_id in 
                (select j_id from jrnx join jrn on (j_grpt = jr_grpt_id)
                    where
                    coalesce(jr_ech,to_date('01.01.1900','DD.MM.YYYY')) >= to_date($2,'DD.MM.YYYY')
                    and coalesce(jr_ech,to_date('01.01.1900','DD.MM.YYYY')) <= to_date($3,'DD.MM.YYYY')
                 )
                    ";
                break;

            default:
                throw new Exception('get_sql_date paramètre invalide');
                break;
        }
        return $sql_date;
    }

}

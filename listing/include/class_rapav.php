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
    static function get_sql_date($p_date,$p_prefix="")
    {
        if ($p_prefix != "") {$p_prefix=$p_prefix.".";}
        switch ($p_date)
        {
            case 0:
                $sql_date = "and (".$p_prefix."j_date >= to_date($2,'DD.MM.YYYY') and ".$p_prefix."j_date <= to_date($3,'DD.MM.YYYY'))";
                break;
            case 1:
                $sql_date = " and ".$p_prefix."j_id in 
                (select j_id from jrnx join jrn on (j_grpt = jr_grpt_id)
                    where
                    coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) >= to_date($2,'DD.MM.YYYY')
                    and coalesce(jr_date_paid,to_date('01.01.1900','DD.MM.YYYY')) <= to_date($3,'DD.MM.YYYY')
                 )
                    ";
                break;
            case 2:
                $sql_date = " and ".$p_prefix."j_id in 
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

    /**
     * @brief check if the formula is valid, return 1 for an error
     * and set errcode to the error msg
     * errcode is global variable
     */
    static function verify_compute($p_formula)
    {
        global $errcode;
        $errcode = "";
        if (trim($p_formula) == "")
        {
            $errcode = " Aucune formule trouvée";
            return 1;
        }

        // copy $this->form->fp_formula to a variable
        $formula = $p_formula;

        // remove the valid
        preg_match_all("/\[([A-Z]*[0-9]*)*([0-9]*[A-Z]*)\]/i", $formula, $e);
        $formula = preg_replace('/_/', '', $formula);
        $formula = preg_replace("/\[([A-Z]*[0-9]*)*([0-9]*[A-Z]*)%*s*d*c*\]/i", '', $formula);
        $formula = preg_replace("/\[([a-z]*[0-9]*)*([0-9]*[A-Z]*)%*s*d*c*\]/i", '', $formula);
        $formula = preg_replace('/([0-9]+.{0,1}[0.9]*)*(\+|-|\*|\/)*/', '', $formula);
        $formula = preg_replace('/(\(|\))/', '', $formula);
        $formula = preg_replace('/\s/', '', $formula);
        $formula = preg_replace('/</', '', $formula);
        $formula = preg_replace('/>/', '', $formula);
        $formula = preg_replace('/=/', '', $formula);
        $formula = preg_replace('/\?/', '', $formula);

        // if something remains it should be a mistake
        if ($formula != '')
        {
            $errcode = _(" Erreur dans la formule ") . $formula;
            return 1;
        }
        return 0;
    }

    static function verify_formula($p_formula)
    {
        global $errcode;
        $errcode = "";
        if (Impress::check_formula($p_formula) == false)
        {
            $errcode = "Erreur dans votre formule";
            return 1;
        }
        if (trim($p_formula) == "")
        {
            $errcode = " Aucune formule trouvée";
            return 1;
        }
        return 0;
    }

}

<?php

/*
 * Copyright (C) 2016 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//!\file
//!\brief usefull static functions


require_once NOALYSS_INCLUDE."/class/class_acc_ledger.php";
require_once NOALYSS_INCLUDE."/class/class_acc_ledger_fin.php";
require_once NOALYSS_INCLUDE."/class/class_acc_ledger_purchase.php";
require_once NOALYSS_INCLUDE."/class/class_acc_ledger_sold.php";
/// Different tools used directly by the objects , some parts of this code should
/// Move to the right class in a later development
class Impacc_Tool
{

    /// Factory pattern to get a ledger of the right type
    /// based on $p_jrn_def_id which is the id of the table jrn_def
    /// This code should be moved to Acc_Ledger
    //!\param $p_jrn_def_id is the id of the ledger
    //!\return Acc_Ledger , Acc_Ledger_Purchase , Acc_Ledger_Fin or Acc_Ledger_Sold
    //!\exception Exception if no ledger is found
    static function ledger_factory($p_jrn_def_id)
    {
        $cn=Dossier::connect();
        $tmp=new Acc_Ledger($cn, $p_jrn_def_id);
        $ledger=null;
        switch ($tmp->get_type())
        {
            case "ACH":
                $ledger=new Acc_Ledger_Purchase($cn, $p_jrn_def_id);
                break;
            case "ODS":
                $ledger=new Acc_Ledger($cn, $p_jrn_def_id);
                break;
            case "VEN":
                $ledger=new Acc_Ledger_Sold($cn, $p_jrn_def_id);
                break;
            case "FIN":
                $ledger=new Acc_Ledger_Fin($cn, $p_jrn_def_id);
                break;

            default:
                throw new Exception(_("journal inconnu"), 1);
                break;
        }
        return $ledger;
    }
    /// Mark a group of rows transferred as a single operation 
    //!\param $p_code_group is import_detail::id_code_group
    //!\param $p_import_id import id (import_file.id = import_detail.import_id)
    static function mark_group_transferred($p_code_group,$p_import_id)
    {
        $cn=Dossier::connect();
        $sql=" update impacc.import_detail set id_status=2 where id_code_group=$1 and import_id=$2 ";
        $cn->exec_sql($sql, array($p_code_group,$p_import_id));
    }
    ///convert_amount($array[$i]->id_amount_novat,$this->detail->s_thousand,$this->s_decimal);
    static function convert_amount($p_amount,$p_thousand,$p_decimal)
    {
        if ( $p_thousand == 1) $p_amount=str_replace(',', '', $p_amount);
        if ( $p_thousand == 2) $p_amount=str_replace('.', '', $p_amount);
        if ($p_decimal == 1) $p_amount=str_replace(',', '.', $p_amount);
        return $p_amount;
    }
    
    /// Find the correct TVA from the table impacc.parameter_tva
    //!\param $p_code is the tva code from the file
    static function convert_tva($p_code)
    {
        $cn=Dossier::connect();
        $tva_id=$cn->get_value("select tva_id from impacc.parameter_tva where tva_code=$1",array($p_code));
        return $tva_id;
    }
}

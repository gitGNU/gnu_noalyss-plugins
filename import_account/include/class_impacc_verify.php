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

//!@file
//@brief  Verify info from impacc.import_detail

require_once DIR_IMPORT_ACCOUNT."/database/class_impacc_import_detail_sql.php";

///All the methods to check the column from 
/// The table impacc.import_detail are here
class Impacc_Verify
{

    //-----------------------------------------------------------------------
    ///
    ///
    //!@param $row is an Impacc_Import_detail_SQL object
    //-----------------------------------------------------------------------
    static function check_quantity(Impacc_Import_detail_SQL $row, $p_thousand,
            $p_decimal)
    {
        $row->id_quant_conv=Impacc_Tool::convert_amount($row->id_quant,
                        $p_thousand, $p_decimal);
        if (isNumber($row->id_quant_conv)==0)
        {
            $and=($row->id_message=="")?"":",";
            $row->id_message.=$and."CK_INVALID_AMOUNT";
        }
    }

    //-----------------------------------------------------------------------
    ///
    ///
    //!@param $row is an Impacc_Import_detail_SQL object
    //-----------------------------------------------------------------------
    static function check_amount_vat(Impacc_Import_detail_SQL $row, $p_thousand,
            $p_decimal)
    {
        $row->id_amount_vat_conv=Impacc_Tool::convert_amount($row->id_amount_vat,
                        $p_thousand, $p_decimal);
        if (isNumber($row->id_amount_vat_conv)==0)
        {
            $and=($row->id_message=="")?"":",";
            $row->id_message.=$and."CK_INVALID_AMOUNT";
        }
    }

    //-------------------------------------------------------------------
    /// Check that the sale / purchase card exist
    /// Update the object $row (id_message)
    //!@param $row is an Impacc_Import_detail_SQL object
    //-------------------------------------------------------------------
    static function check_service(Impacc_Import_detail_SQL $row)
    {
        $cn=Dossier::connect();
        $card=new Fiche($cn);
        $card->get_by_qcode($row->id_acc_second, false);
        if ($card->id==0)
        {
            $and=($row->id_message=="")?"":",";
            $row->id_message.=$and."CK_INVALID_ACCOUNTING";
        }
    }

    //-------------------------------------------------------------------
    /// Check that TVA_CODE does exist , return true if exists otherwise false
    //-------------------------------------------------------------------
    static function check_tva($p_tva_code)
    {
        $cn=Dossier::connect();
        global $g_parameter;
        // If we don't use VAT , no need to check it
        if ($g_parameter->MY_TVA_USE=="N")
            return true;

        // VAT Mandatory and empty 
        if ($p_tva_code=="")
        {
            return false;
        }

        $exist=$cn->get_value("select count(*) from impacc.parameter_tva where tva_code=$1",
                array($p_tva_code));
        if ($exist!=1)
        {
            return false;
        }
        return true;
    }

    //-------------------------------------------------------------------
    /// Check that the date payment has valid format . Return false or
    // a valid date in format DD.MM.YYYY
    //!\param $p_date is the value to check
    //!\param $p_format_date check for date_limit and date_payment
    //-------------------------------------------------------------------
    static function check_date($p_date,$p_format_date)
    {
        if ($p_date=="")            return false;

        $test=DateTime::createFromFormat($p_format_date, $p_date);

        if ($test==false)
        {
            return false;
        }
        else
        {
            $p_return=$test->format('d.m.Y');
            return $p_return;
        }
    }

    //-------------------------------------------------------------------
    /// Check that a card exist and use a valid accounting
    //!@param $p_account quick_code of a card, 
    //!@returns true if correct , else false
    //-------------------------------------------------------------------
    static function check_card($p_account)
    {
        $cn=Dossier::connect();
        $card=new Fiche($cn);
        $card->get_by_qcode($p_account, false);
        if ($card->id==0)
        {

            return false;
        }
        else
        {
            // The accounting must also be checked 
            $poste_str=$card->strAttribut(ATTR_DEF_ACCOUNT);

            $poste=new Acc_Account_Ledger($cn, $poste_str);
            if ($poste->do_exist()==0)
            {
                return false;
            }
        }
        return true;
    }

}

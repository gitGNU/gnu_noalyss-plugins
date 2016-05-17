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
require_once 'class_impacc_csv_sale_purchase.php';

/// For Ledger of style SALE
class Impacc_Csv_Sale extends Impacc_Csv_Sale_Purchase
{
    //!< date_paid will introduced after insert
    private $jr_date_paid;
    private $suggest_pj;
    /// Transform a group of rows to an array and set $jr_date_paid
    /// useable by Acc_Ledger_Sold::insert
    function adapt( $p_row)
    {
        bcscale(4);
        $cn=Dossier::connect();
        
        // Get the code_group
        $code_group=$p_row;
        
        // get all rows from this group
        $t=new Impacc_Import_detail_SQL($cn);
        $all_row=$t->collect_objects("where import_id=$1 and id_code_group=$2",
                array($p_row['import_id'],$p_row['id_code_group']));
        
        // No block due to analytic
        global $g_parameter;
        $g_parameter->MY_ANALYTIC="N";
        // Save the date of payment
        $this->jr_date_paid=$all_row[0]->id_date_payment_conv;
        
        // Initialise the array
        $array=array();
        // Suppress payment
        $array['e_mp']=0;
         // The first row gives all the information , the others of the group
        // add services
        $array['e_client']=$all_row[0]->id_acc;
        $array['nb_item']=count($all_row);
        $array['e_comm']=$all_row[0]->id_label;
        // Must be transformed into DD.MM.YYYY
        $array['e_date']=$all_row[0]->id_date_conv; 
        $array['e_ech']=$all_row[0]->id_date_limit_conv; 
        $array['e_pj']=$all_row[0]->id_pj;
        
        $array['mt']=microtime();
        $array['jrn_type']='VEN';
        $nb_row=count($all_row);
        for ( $i=0;$i<$nb_row;$i++)
        {
            $array["e_march".$i]=$all_row[$i]->id_acc_second;
            $price=$all_row[$i]->id_amount_novat_conv;
            $quant=$all_row[$i]->id_quant_conv;
            $pricetax=$all_row[$i]->id_amount_vat_conv;
            $price_unit=bcdiv($price,$quant);
            $array["e_march".$i."_price"]=$price_unit;
            $array["e_march".$i."_label"]=$all_row[$i]->id_label;
            $array["e_march".$i."_tva_id"]=Impacc_Tool::convert_tva($all_row[$i]->tva_code); // Find code
            $array["e_march".$i."_tva_amount"]=bcsub($pricetax,$price);
            $array["e_quant".$i]=$quant;
        }
        return $array;
    }
    /// Transfer operation with the status correct to the
    /// accountancy . Change the status of the row (id_status to 1) after
    /// because it can transferred several rows in one operation
    function insert($a_group, Acc_Ledger_Sold $p_ledger)
    {
       $cn=Dossier::connect();
       $nb_group=count($a_group);
       for ( $i=0;$i< $nb_group;$i++)
       {
            $array=$this->adapt($a_group[$i]);
            
            //Complete the array
            $array["p_jrn"]=$p_ledger->id;
            
            //Receipt
            if (trim($array['e_pj'])=="") $array["e_pj"]=$p_ledger->guess_pj();
            $array['e_pj_suggest']=$p_ledger->guess_pj();
            
            // Insert
            $p_ledger->insert($array);
            
           // Update this group , status = 2  , jr_id
           // and date_payment
            $cn->exec_sql("update impacc.import_detail set jr_id=$1 , id_status=2 where id_code_group=$2",
                    array($p_ledger->jr_id,$a_group[$i]['id_code_group']));
            $cn->exec_sql(" update public.jrn set jr_date_paid=to_date($1,'DD.MM.YYYY') where jr_id=$2",
                    array($this->jr_date_paid,$p_ledger->jr_id));
            
       }
       
    }

 

}
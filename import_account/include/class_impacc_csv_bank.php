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


/*!
 * @file 
 * @brief Filter for the Financial format
 *
 */
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_csv.php";

///Filter for the Financial format
class Impacc_Csv_Bank
{
    /*!
     * \brief insert file into the table import_detail
     */
    function record(Impacc_Csv $p_csv, Impacc_File $p_file)
    {
        global $aseparator;
        // Open the CSV file
        $hFile=fopen($p_file->import_file->i_tmpname, "r");
        $cn=Dossier::connect();
        $delimiter=$aseparator[$p_csv->detail->s_delimiter-1]['label'];
        $surrount=($p_csv->detail->s_surround=="")?'"':$p_csv->detail->s_surround;
        //---- For each row ---
        while ($row=fgetcsv($hFile, 0,$delimiter ,$surrount ))
        {
            $insert=new Impacc_Import_detail_SQL($cn);
            $insert->import_id=$p_file->import_file->id;
            $nb_row=count($row);
            if ($nb_row<5)
            {
                $insert->id_status=-1;
                $insert->id_message=join($row,$delimiter );
            }
            else
            {
                $insert->id_date=$row[0];
                $insert->id_code_group=$row[1];
                $insert->id_acc=$row[2];
                $insert->id_pj=$row[3];
                $insert->id_label=$row[4];
                $insert->id_amount_novat=$row[5];
            }
            $insert->insert();
        }

    }
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
        
        // Initialise the array
        $array=array();
         // The first row gives all the information , the others of the group
        // add services
        $array['nb_item']=count($all_row); // must == 1
        $array['chdate']=2; // must == 1
        $array['e_other0']=$all_row[0]->id_acc;
        $array['e_concerned0']="";
        $array['e_other0_comment']=$all_row[0]->id_label;
        // Must be transformed into DD.MM.YYYY
        $array['dateop0']=$all_row[0]->id_date_conv; 
        $array['e_pj']=$all_row[0]->id_pj;
        
        $array['mt']=microtime();
        $array['jrn_type']='FIN';
        $array["e_other0_amount"]=$all_row[0]->id_amount_novat;
        return $array;
    }
    /// Transfer operation with the status correct to the
    /// accountancy . Change the status of the row (id_status to 1) after
    /// because it can transferred several rows in one operation
    function insert($a_group, Acc_Ledger_Fin $p_ledger)
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
            
            // Because of a bug in Acc_Ledger_Fin we have to unset this
            // global variable 
            unset($_FILES);
            
            // Insert
            $p_ledger->insert($array);
            
           // Update this group , status = 2  , jr_id
           // and date_payment
            $cn->exec_sql("update impacc.import_detail set id_status=2 where id_code_group=$1",
                    array($a_group[$i]['id_code_group']));
            
       }
       
    }
}

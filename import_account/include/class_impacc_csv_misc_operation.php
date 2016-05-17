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


/* * *
 * @file 
 * @brief
 *
 */
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_csv.php";

class Impacc_Csv_Misc_Operation 
{

    //----------------------------------------------
    ///insert file into the table import_detail
    //!\param Impacc_Csv $p_csv CSV setting
    //!\param Impacc_File $p_file File information
    function record(Impacc_Csv $p_csv, Impacc_File $p_file)
    {
         global $aseparator;
        // Open the CSV file
        $hFile=fopen($p_file->import_file->i_tmpname, "r");
        $error=0;
        $cn=Dossier::connect();
        $delimiter=$aseparator[$p_csv->detail->s_delimiter-1]['label'];
        $surround=($p_csv->detail->s_surround=="")?'"':$p_csv->detail->s_surround;
        //---- For each row ---
        while ($row=fgetcsv($hFile, 0, $delimiter, $surround))
        {
            $insert=new Impacc_Import_detail_SQL($cn);
            $insert->import_id=$p_file->import_file->id;
            $nb_row=count($row);
            if ($nb_row<6)
            {
                 $insert->id_status=-1;
                $insert->id_message=join($row,$delimiter);
            }
            else
            {
                $insert->id_date=$row[0];
                $insert->id_code_group=$row[1];
                $insert->id_acc=$row[2];
                $insert->id_pj=$row[3];
                $insert->id_label=$row[4];
                $insert->id_amount_novat=$row[5];
                $insert->id_debit=$row[6];
            }
            $insert->insert();
        }
    }
    /// Check if Data are valid for one row
    //!@param $row is an Impacc_Import_detail_SQL object
    function check(Impacc_Import_detail_SQL $row)      
    {
        
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
        $sql_row= "
            select id,
                id_amount_novat_conv, 
                id_date_conv,
                (select f_id from fiche_detail where ad_value=id_acc and ad_id=23) as f_id , 
                id_acc ,
                id_label,
                id_debit,
                id_acc,
                id_pj
            from 
                impacc.import_detail
            where
                import_id=$1 
                and id_code_group=$2";
        $all_row=$cn->get_array($sql_row,
                array($p_row['import_id'],$p_row['id_code_group']));
        // No block due to analytic
        global $g_parameter;
        $g_parameter->MY_ANALYTIC="N";
        
        // Initialise the array
        $array=array();
         // The first row gives all the information , the others of the group
        // add services
        $array['nb_item']=count($all_row);
        $array['e_comm']=$all_row[0]['id_label'];
        $array['desc']=$all_row[0]['id_label'];
        $array['jrn_concerned']="";
        // Must be transformed into DD.MM.YYYY
        $array['e_pj']=$all_row[0]['id_pj'];
        $array['e_date']=$all_row[0]['id_date_conv'];
        
        $array['mt']=microtime();
        $array['jrn_type']='ODS';
        $nb_row=count($all_row);
        for ( $i=0;$i<$nb_row;$i++)
        {
            $price=$all_row[$i]['id_amount_novat_conv'];
            if ( $all_row[$i]['id_debit'] == "D") {
                $array["ck".$i]="";
            }
            if ($all_row[$i]['f_id']=="")
            {
                $array["poste".$i]=$all_row[$i]["id_acc"];
            }else {
                $array["qc_".$i]=$all_row[$i]["id_acc"];
            }
            $array["amount".$i]=$price;
            $array["ld".$i]=$all_row[$i]["id_label"];
        }
        return $array;
    }
    /// Transfer operation with the status correct to the
    /// accountancy . Change the status of the row (id_status to 1) after
    /// because it can transferred several rows in one operation
    function insert($a_group, Acc_Ledger $p_ledger)
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
            
            var_dump($array);
            // Insert
            $p_ledger->save($array);
            
           // Update this group , status = 2  , jr_id
           // and date_payment
            $cn->exec_sql("update impacc.import_detail set jr_id=$1 , id_status=2 where id_code_group=$2",
                    array($p_ledger->jr_id,$a_group[$i]['id_code_group']));
            
       }
       
    }
}
    
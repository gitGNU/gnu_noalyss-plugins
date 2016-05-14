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
        //---- For each row ---
        while ($row=fgetcsv($hFile, 0, $delimiter, $p_csv->detail->s_surround))
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
            }
            $insert->insert();
        }
    }
    /// Check if Data are valid for one row
    //!@param $row is an Impacc_Import_detail_SQL object
    function check(Impacc_Import_detail_SQL $row)      
    {
        
    }
    /// Transfer operation with the status correct to the
    /// accountancy
    function transfer()
    {
        throw new Exception("Not Yet Implemented");
    }
}
    
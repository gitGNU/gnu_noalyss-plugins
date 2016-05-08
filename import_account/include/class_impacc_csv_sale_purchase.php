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
 * @brief Insert into the the table impacc.import_detail + status
 *
 */
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_csv.php";

class Impacc_Csv_Sale_Purchase
{

    function transform()
    {
        throw new Exception("Not Yet Implemented");
    }

    /*!
     * \brief call parent and after specific check
     */
    function check()
    {
        throw new Exception("Not Yet Implemented");
    }

    function insert()
    {
        throw new Exception("Not Yet Implemented");
    }

    function check_tva()
    {
        throw new Exception("Not Yet Implemented");
    }

    function check_date_payment()
    {
        throw new Exception("Not Yet Implemented");
    }

    function check_date_limit()
    {
        throw new Exception("Not Yet Implemented");
    }

    function check_nb_column()
    {
        throw new Exception("Not Yet Implemented");
    }

    /**
     * @brief insert file into the table import_detail
     */
    function record(Impacc_Csv $p_csv, Impacc_File $p_file)
    {
         global $aseparator;
        // Open the CSV file
        $hFile=fopen($p_file->import_file->i_tmpname, "r");
        $error=0;
        $cn=Dossier::connect();
        var_dump($p_csv->detail);
        var_dump($aseparator);
        $delimiter=$aseparator[$p_csv->detail->s_delimiter-1]['label'];
        //---- For each row ---
        while ($row=fgetcsv($hFile, 0, $delimiter, $p_csv->detail->s_surround))
        {
            $nb_row=count($row);
            $insert=new Impacc_Import_detail_SQL($cn);
            $insert->import_id=$p_file->import_file->id;
            if ($nb_row<9)
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
                $insert->id_acc_second=$row[5];
                $insert->id_quant=$row[6];
                $insert->id_amount_novat=$row[7];
                $insert->tva_code=$row[8];
                $insert->id_amount_vat=$row[9];
                $date_limit=(isset($row[10]))?$row[10]:"";
                $insert->id_date_limit=$date_limit;
                $date_payment=(isset($row[11]))?$row[11]:"";
                $insert->id_date_limit=$date_payment;
                $insert->id_nb_row=0;
            }
            $insert->insert();
        }
        if ($error>0)
        {
            echo "</ul>";
        }


        // insert row into table with status
    }

}

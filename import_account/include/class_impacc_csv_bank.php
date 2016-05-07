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

class Impacc_Csv_Bank
{
    /*!
     * \brief insert file into the table import_detail
     */
    function record(Impacc_Csv $p_csv, Impacc_File $p_file)
    {
        // Open the CSV file
        $hFile=fopen($p_file->import_file->i_tmpname, "r");
        $cn=Dossier::connect();
        //---- For each row ---
        while ($row=fgetcsv($hFile, 0, $p_csv->delimiter, $p_csv->surround))
        {
            $insert=new Impacc_Import_detail_SQL($cn);
            $insert->import_id=$p_file->import_file->id;
            $nb_row=count($row);
            if ($nb_row<5)
            {
                $insert->id_status=-1;
                $insert->id_message=join($row,$p_csv->delimiter);
            }
            else
            {
                $insert->id_date=$row[0];
                $insert->id_acc=$row[1];
                $insert->id_pj=$row[2];
                $insert->id_label=$row[3];
                $insert->id_amount_novat=$row[4];
            }
            $insert->insert();
        }
        if ( $error > 0 ) {
            echo "</ul>";
        }

    }

}

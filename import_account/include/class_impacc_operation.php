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


/***
 * @file 
 * @brief Redirect to CSV or other format
 *
 */
/// Redirect to CSV or other format
class Impacc_Operation
{
    /// call the right filter to import operation
    function record_file(Impacc_File $p_file)
    {
        // in p_file we have the type CSV , XML NOALYSS or XML FEC
        switch ($p_file->format)
        {
            case "CSV":
                /**
                 * Following the ledger type we have to use impacc_csv_bank,
                 * sale_purchase or mis_operation
                 * 
                 */
                $csv=new Impacc_CSV();
                
                $csv->record($p_file);
                break;

            default:
                throw new Exception(_("Non supporté"), 1);
        }
    }
    /// call the check and validate import , depending of the format (CSV...)
    function check(Impacc_File $p_file)
    {
        switch ($p_file->format)
        {
            case 'CSV':

                $csv=new Impacc_CSV();
                $csv->check($p_file);
                break;

            default:
                break;
        }
    }
    ///Transfer operation from uploaded file to the 
    /// tables of Noalyss
    //!\param $p_file Impacc_File $p_file
   function transfer(Impacc_File $p_file)
   {
       switch($p_file->format) 
       {
           case 'CSV':
               $csv=new Impacc_CSV();
               $obj=$csv->make_csv_class($p_file->import_file->id);
               $obj->transfer();
               break;
           default:
              throw new Exception(_("Non supporté"), 1);

       }
   }
}

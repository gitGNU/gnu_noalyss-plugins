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
/* $Revision$ */

// Copyright (c) 2016 Author Dany De Bontridder dany@alchimerys.be

/***
 * @file
 * @brief upload operation
 *
 */
require_once 'class_impacc_file.php';

// step 1, select a file
if (!isset($_POST['upload'])&&!isset($_POST['check'])&&!isset($_POST['transfer']))
{
    
    $impacc_Operation = new Impacc_File();
    $impacc_Operation->input_file();
    return;
}
// step 2 save file into impdol.operation
if (isset($_POST['upload']))
{
    // save the file
    $io=new Impacc_File();
    
    // save info for file + setting
    $io->save_file();
    
    // record the rows of the file into the right table CSV or XML
    $io->record();
    
    // Basic check
    $io->check();

    // show the result
    $io->result();
    echo '<div style="margin-left:20%">';
    echo '<form method="POST">';
    echo "<p class=\"notice\">".
    _("Les opérations qui ne sont pas marquées comme correctes ne seront pas transfèrées").
    " </p>";
    echo HtmlInput::hidden("impid", $io->impid);
    // If CSV show the target
    if ( $io->import_file->i_type=="CSV")
    {
        $csv=new Impacc_CSV();
        $csv->load_import($io->impid);
        $target=$csv->make_csv_class($io->impid);
        $ledger=new Acc_Ledger($cn,$csv->detail->jrn_def_id);
        printf (_("Transfert vers le journal %s"),$ledger->get_name());
    }
    echo '<p>';
    echo HtmlInput::submit("transfer", _("Transfert des opérations"));
    echo '</p>';
    echo "</FORM>";
    echo '</div>';
}
// step 3, insert data into the target ledger
if (isset($_POST['transfer']))
{
    $io=new Impacc_File();
    $io->impid=HtmlInput::default_value_post('impid',0);
    $io->load($io->impid);
    if ( $io->impid == 0 )        throw new Exception(_("Erreur paramètre"));
    $io->transfer();
    //$io->result_transfer();
}
?>

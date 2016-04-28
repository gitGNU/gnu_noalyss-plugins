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

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief upload operation
 *
 */
require_once 'class_impacc_operation.php';

// step 1, select a file
if (!isset($_POST['upload'])&&!isset($_POST['check'])&&!isset($_POST['transfer']))
{
    
    $impacc_Operation = new Impacc_Operation();
    $impacc_Operation->input_format();
    return;
}
// step 2 save file into impdol.operation
if (isset($_POST['upload']))
{
    // save the file
    $io=new Impacc_Operation();
    $io->save_file();

    // record the file into the table operation
    $io->record();

    // show the data
    $io->check();

    // show the result
    $io->result();
    echo '<div style="margin-left:20%">';
    echo '<form method="POST">';
    echo "<p class=\"notice\">".
    _("Les opérations qui ne sont pas marquées comme correctes ne seront pas transfèrées").
    " </p>";
    echo HtmlInput::hidden("impid", $io->impid);
    $l=new ISelect("p_jrn");
    $l->value=$cn->make_array("select jrn_def_id, jrn_def_name ||'  ['||jrn_def_type||']' from jrn_def
		where
		jrn_def_type in ('ACH','VEN')
		order by jrn_def_name");
    echo "Vers le journal ".$l->input();
    echo '<p>';
    echo HtmlInput::submit("transfer", _("Transfert des opérations"));
    echo '</p>';
    echo "</FORM>";
    echo '</div>';
}
// step 3, insert data into the target ledger
if (isset($_POST['transfer']))
{
    $io=new Impdol_Operation();
    $io->impid=$_POST['impid'];
    $io->transfer();
    $io->result_transfer();
}
?>

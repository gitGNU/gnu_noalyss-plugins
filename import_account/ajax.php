<?php

/*
 * * Copyright (C) 2016 Dany De Bontridder <dany@alchimerys.be>
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

 * 
 */


require_once 'impacc_constant.php';
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_tva.php";
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_file.php";
/**
 * @file
 * @brief Ajax 
 */
$action=HtmlInput::default_value_request("action", "none");
if ( $action == "tva_parameter_modify")
{
    $tva=new Impacc_TVA();
    $tva_id=HtmlInput::default_value_request("tva_id", 0);
    $tva->display_modify($tva_id);
}
if ( $action == "tva_parameter_add")
{
    $tva=new Impacc_TVA();
    $tva->display_add();
}
if ( $action == "tva_parameter_delete")
{
    $tva=new Impacc_TVA();
    $tva_id=HtmlInput::default_value_request("pt_id", 0);
    $tva->delete($tva_id);
    echo "";
}
if ( $action == "history_delete")
{
    $hist=new Impacc_File();
    $id=HtmlInput::default_value_request("id", 0);
    $hist->delete($id);
    echo "";
}

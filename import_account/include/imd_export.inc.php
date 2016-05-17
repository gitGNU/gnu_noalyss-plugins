<?php

/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require_once DIR_IMPORT_ACCOUNT.'/include/class_impacc_export.php';
/**
 * @file
 * @brief Export operation from NOALYSS
 */

/// Step 1 : ask for date + ledger
$export=new Impacc_Export_CSV();
$export->input_param();

/// Step 2 : export a CSV file
$export_param=HtmlInput::default_value_get("export_operation", "none");
if ( $export_param != "none")
{
    $export->get_param();
    $export->export_csv();
}

?>

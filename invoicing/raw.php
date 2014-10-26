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

/*!\file
 * \brief raw file for PDF ewa
 */
require_once('invoicing_constant.php');
extract ($_REQUEST);
$zip_file=HtmlInput::default_value_request('file','null');
if ($zip_file=='null')
{
    die ('No file asked');
}

$zip_file=$_ENV['TMP']."/".$zip_file;

header('Content-type: application/zip');
header('Content-Disposition: attachment; filename="' . $file);
$h_file=fopen($zip_file,"r");
if ($h_file != true) {
    die ('cannot open file');
}
$buffer=fread ($h_file,filesize($zip_file));
echo $buffer;
?>

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
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))     die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief manage the ajax calls
 */
require_once __DIR__."/include/class_impcard_format_sql.php";
/// Save a format
if (isset($_POST["format_save"])) {
    $name=HtmlInput::default_value_post("format_name", "");
    $delimiter=HtmlInput::default_value_post("rdelimiter", "");
    $skip_row=HtmlInput::default_value_post("skip_row", "");
    $surround=HtmlInput::default_value_post("rsurround", "");
    $encoding=HtmlInput::default_value_post("encodage", "N");
    $card_category=HtmlInput::default_value_post("rfichedef", "");
    $head_code=HtmlInput::default_value_post("head_col", "");
    
    // if name , delimiter , fiche_def or head_col is empty then
    // we throw an error
    if ( trim($name)=="") { echo "NONAME"; exit;}
    if ( trim($card_category)=="") { echo "NOCARD"; exit;}
    if ( ! is_array($head_code) || empty($head_code)) { echo "NOHEAD"; exit;}
    if ( trim($delimiter)=="") { echo "NODELIM"; exit;}
    
    $format=new Importcard_Format_SQL($cn,-1);
    $format->f_name=$name;
    $format->f_card_category=$card_category;
    $format->f_skiprow=$skip_row;
    $format->f_delimiter=$delimiter;
    $format->f_surrount=$surround;
    $format->f_unicode_encoding=($encoding=="N")?"N":"Y";
    $format->f_position=join($head_code,",");
    $format->save();
    echo "OK";
    return;
}
if (isset($_GET["getFormat"]) )
{
    $id=HtmlInput::default_value_get("format_id", -1);
    if (isNumber($id)==0) {
        echo 'ERRFORMAT';
        return;
    }
    
}

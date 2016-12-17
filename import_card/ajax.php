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
    $format_id=HtmlInput::default_value_post("format_id", 0);
    $name=HtmlInput::default_value_post("format_name", "");
    if ( $format_id == 0 || trim($name)=="")
    {
        echo "Paramètre invalide";
        return;
    }
    $format=new Importcard_Format_SQL($cn,$format_id);
    $format->f_name=html_entity_decode($name,ENT_COMPAT | ENT_HTML401,"utf-8");
    $format->f_saved=1;
    $format->save();
    printf(_("Sauvegarde du modèle %s"), $name);
    return;
}
if (isset($_GET["getFormat"]) )
{
    $id=HtmlInput::default_value_get("format_id", -1);
    if (isNumber($id)==0) {
        echo 'ERRFORMAT';
        return;
    }
    // retrieve info 
    $format=new Importcard_Format_SQL($cn,$id);
    $array=array();
    $array['rdelimiter']=$format->f_delimiter;
    $array['encodage']=$format->f_unicode_encoding;
    $array['rsurround']=$format->f_surround;
    $array['skip_row']=$format->f_skiprow;
    $array['f_position']=explode(',',$format->f_position);
    header('Content-Type: application/json');
    echo json_encode($array);
    
}

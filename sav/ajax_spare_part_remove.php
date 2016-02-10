<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS isfree software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS isdistributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// Copyright (2015) Author Dany De Bontridder <dany@alchimerys.be>

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
require_once 'include/class_sav_spare_part.php';

$spare_part=HtmlInput::default_value_get('spare_part_id', -1);

if ( $spare_part == -1 || isNumber($spare_part)==0)
    throw new Exception("Invalid parameter");

$spare=new Sav_Spare_Part($spare_part);
$erreur="ok";
try
{
    $spare->remove();
}
catch (Exception $exc)
{
    $erreur=$exc->getCode();
}
if (! headers_sent() )    header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data> 
<code>{$erreur}</code>
</data>
EOF;
?>
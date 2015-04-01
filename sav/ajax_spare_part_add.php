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
// Copyright (2015) Author Dany De Bontridder <dany@alchimerys.be>

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief add a spare_part to a repair_card
 * @param p_repair_card :integer 
 */

require_once 'include/class_sav_spare_part.php';

$qcode=HtmlInput::default_value_get('qcode', "");
$quant=HtmlInput::default_value_get('quant',-1);
$repair=HtmlInput::default_value_get('repair',-1);



$erreur="";
$row="";
try
{
    $spare=new Sav_Spare_Part();
    if ( $quant == -1 || isNumber($quant )== 0 ||trim($qcode)=="" ||$repair==-1||isNumber($repair)==0)
        throw new Exception("Invalid parameter",APPEL_INVALIDE);
    $spare->repair_card_add($repair,$qcode, $quant);
    $row=escape_xml($spare->print_row());
}
catch (Exception $exc)
{
    $erreur=$exc->getCode();
}
if (! headers_sent() )    header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data> 
<id>{$spare->get_id()}</id>
<name>{$spare->get_name()}</name>
<quant>{$quant}</quant>
<qcode>{$spare->get_qcode()}</qcode>
<error>{$erreur}</error>
<html>{$row}</html>
</data>
EOF;

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

// Copyright 2014 Author Dany De Bontridder danydb@aevalys.eu

// require_once '.php';
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
/**
 * @file
 * @brief add a workhour to a repair_card
 * @param p_repair_card :integer 
 */

require_once 'include/class_sav_workhour.php';

$hour=HtmlInput::default_value_get('hour', -1);
$workhour_qcode=HtmlInput::default_value_get('workhour_qcode', -1);
$desc=HtmlInput::default_value_get('description',"");
$repair=HtmlInput::default_value_get('repair',-1);


$workhour=new Sav_WorkHour();
$erreur="";
$row="";
try
{
    if ( $hour == -1 || isNumber($hour)== 0 ||$repair==-1||isNumber($repair)==0||$workhour_qcode==-1||$workhour_qcode== "")
    throw new Exception("Invalid parameter",APPEL_INVALIDE);

    $workhour->add($repair,$workhour_qcode,$hour,$desc);
    $row=escape_xml($workhour->print_row());
}
catch (Exception $exc)
{
    $erreur=$exc->getCode();
}
if (! headers_sent() )    header('Content-type: text/xml; charset=UTF-8');

echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data> 
<id>{$workhour->get_id()}</id>
<quant>{$hour}</quant>
<error>$erreur</error>
<html>{$row}</html>
</data>
EOF;
?>        
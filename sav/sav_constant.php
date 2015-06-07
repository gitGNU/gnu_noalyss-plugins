<?php 
//This file is part of NOALYSS and is under GPL 
//see licence.txt
/**
 *@file
 *Contains all the needed variable for the plugin
 *is name is plugin_name_constant.php
 * You can use some globale variable, especially for the database
 *  connection
 */

require_once ('class_database.php');
require_once 'include/class_service_after_sale_parameter.php';

define ("NOMATERIAL",1000);
define ("APPEL_INVALIDE",1001);
define ("NOSPAREPART",1002);
define ("DATEINVALIDE",1003);

global $cn,$g_sav_parameter;
$cn=new Database (dossier::id());
if ( $cn->exist_schema('service_after_sale') == true)
{
    $g_sav_parameter=new Service_After_Sale_Parameter($cn);
}

global $gDossier,$ac,$plugin_code;
$gDossier=Dossier::id();
$ac=HtmlInput::default_value_request("ac", -1);
$plugin_code=HtmlInput::default_value_request("plugin_code", -1);

?>
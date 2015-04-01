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
require_once 'include/class_SKEL_parameter.php';

global $cn,$g_SKEL_parameter,$gDossier,$g_plugin,$g_access;
$cn=new Database (dossier::id());
$g_SKEL_parameter=new SKEL_Parameter();
$gDossier=Dossier::id();
$g_plugin=HtmlInput::default_value_request('plugin_code', "");
$g_access=HtmlInput::default_value_request('ac', "");
?>
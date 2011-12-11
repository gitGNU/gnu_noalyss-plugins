<?
/**
 *@file
 *Contains all the needed variable for the plugin
 *is name is plugin_name_constant.php
 * You can use some globale variable, especially for the database
 *  connection
 */

require_once ('class_database.php');
require_once 'include/class_copro_parameter.php';
global $cn,$g_copro_parameter,$gDossier;

$gDossier=Dossier::id();
$g_copro_parameter=new Copro_Parameter();

?>
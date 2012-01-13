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
require_once 'class_acc_ledger.php';

global $cn,$g_copro_parameter,$gDossier;
$cn=Dossier::connect();
$gDossier=Dossier::id();
$g_copro_parameter=new Copro_Parameter();
define ("COPRO_MAX_LOT",19);
define("MAXROWBUD",15);
?>
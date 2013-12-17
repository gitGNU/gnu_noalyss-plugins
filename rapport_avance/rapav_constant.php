<?php 
/**
 *@file
 *Contains all the needed variable for the plugin
 *is name is plugin_name_constant.php
 * You can use some globale variable, especially for the database
 *  connection
 */

require_once ('class_database.php');

global $cn,$rapav_version,$errcode;

$cn=new Database (dossier::id());
$rapav_version=4;
?>

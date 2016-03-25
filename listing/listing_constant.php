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

require_once NOALYSS_INCLUDE.'/lib/class_database.php';

global $cn,$rapav_version,$errcode;
global $g_listing_home ;
$g_listing_home=__DIR__;
$cn=Dossier::connect();;
$rapav_version=6;
?>

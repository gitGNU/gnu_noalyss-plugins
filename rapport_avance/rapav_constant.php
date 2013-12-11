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
/** -- pour utiliser unoconv démarrer un server libreoffice 
 * libreoffice --headless --accept="socket,host=127.0.0.1,port=2002;urp;" --nofirststartwizard */
define ('OFFICE','unoconv ');
define ('GENERATE_PDF','NO');
$cn=new Database (dossier::id());
$rapav_version=4;
?>

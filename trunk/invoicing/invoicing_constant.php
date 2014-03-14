<?php 
/**
 *@file
 *Contains all the needed variable for the plugin
 *is name is plugin_name_constant.php
 * You can use some globale variable, especially for the database
 *  connection
 */

require_once ('class_database.php');

global $cn,$g_SKEL_parameter;
$cn=new Database (dossier::id());

// document_type.dt_id, dt_value='FACTURE'
define ('DOCUMENT_TYPE',4); 
?>
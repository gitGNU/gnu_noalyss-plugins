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

global $cn;
$cn=new Database (dossier::id());

function detail_material($f_id,$p_label)
{
  $href=sprintf('<A class="detail" style="text-decoration:underline" href="javascript:display_material(%d,%d,\'%s\',\'bxmat\')">%s</A>',
		dossier::id(),$f_id,$_REQUEST['plugin_code'],$p_label);
  return $href;
}
$amortissement_version=3;
?>

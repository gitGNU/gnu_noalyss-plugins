<?php
require_once('coprop-constant.php');
require_once('class_database.php');

extract($_GET);
global $cn;
$html='';$extra='';$ctl='';
ob_start();
switch($act) {
	// ajout un lien copro + lot
case 'addcopro':
  /* the hide button */
  require_once('include/ajax_add_copro_lot.php');
  break;
default:

	var_dump($_GET);
}
?>

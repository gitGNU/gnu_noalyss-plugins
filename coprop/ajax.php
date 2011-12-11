<?php
require_once('coprop-constant.php');
require_once('class_database.php');

extract($_GET);
global $cn;
$html='';$extra='';$ctl='';
ob_start();
switch($act) {
case 'dsp_decl':
  /* the hide button */
  require_once('include/ajax_dsp.php');
  break;
default:

	var_dump($_GET);
}
?>

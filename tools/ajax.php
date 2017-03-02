<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
  /*
EXAMPLE
require_once 'skel_constant.php';
require_once NOALYSS_INCLUDE.'/lib/class_database.php';
require_once 'class_ext_tva.php';
require_once NOALYSS_INCLUDE.'/lib/class_ibutton.php';
require_once 'class_ext_list_intra.php';
require_once 'class_ext_list_assujetti.php';

extract($_GET, EXTR_SKIP);
global $cn;
$html='';$extra='';$ctl='';
ob_start();
switch($act) {
case 'dsp_decl':
  // the hide button 
  require_once('include/ajax_dsp.php');
  break;
}

$html=ob_get_contents();

ob_end_clean();

$html=escape_xml($html);

header('Content-type: text/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<data>';
echo "<ctl>$ctl</ctl>";
echo "<html>$html</html>";
echo "<extra>$extra</extra>";
echo "</data>";
*/
?>

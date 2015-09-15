<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
require_once('skel_constant.php');
require_once NOALYSS_INCLUDE.'/lib/class_database.php';
require_once('class_ext_tva.php');
require_once('class_ibutton.php');
require_once ('class_ext_list_intra.php');
require_once ('class_ext_list_assujetti.php');

extract($_GET);
global $cn;
$html='';$extra='';$ctl='';
ob_start();
switch($act) {
case 'dsp_decl':
  /* the hide button */
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
?>

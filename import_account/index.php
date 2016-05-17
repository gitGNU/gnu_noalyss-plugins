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
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/*!\file
 * \brief main file for importing card
 */

/*
 * load javascript
 */
require_once 'impacc_constant.php';

global $cn;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=importation_operation" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';
$cn=Dossier::connect();
global $version_plugin;
$version_plugin=SVNINFO;

Extension::check_version(6915);
$plugin_code=HtmlInput::default_value_request("plugin_code", "");
$ac=HtmlInput::default_value_request("ac", "");

?>
<script>
    var dossier="<?php echo Dossier::id();?>";
    var plugin_code="<?php echo $plugin_code;?>";
    var ac="<?php echo $ac;?>";
</script>
<?php
// Javascript
 ob_start();
 require_once('impacc.js');
$j=ob_get_contents();
ob_end_clean();
echo create_script($j);



$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code']."&ac=".$_REQUEST['ac'];

$array=array (
	array($url.'&sa=opr',_('Import'),_('Importation d\'opérations '),2),
	array($url.'&sa=exp',_('Export'),_('Exportation d\'opérations '),3),
	array($url.'&sa=hist',_('Historique'),_('Historique importation'),4),
	array($url.'&sa=parm',_('Paramètrage'),_('Paramètrage'),5)
	);

$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:1;
switch($sa)
  {
 
  case 'opr':
    $default=2;
    break;
  case 'exp':
    $default=3;
    break;
  case 'parm':
    $default=5;
    break;
  case 'hist':
    $default=4;
    break;
  default:
    $default=2;
  }

  if ($cn->exist_schema('impacc') == false)
  {
    require_once('include/class_install_impacc.php');

    $iplugn=new Install_ImpAcc();
    $iplugn->install($cn);

  }
echo ShowItem($array,'H','mtitle','mtitle',$default,' style="width:80%;margin-left:10%"');
echo '<div class="content" style="padding:10">';

if ($default == 5)
{
	require_once('include/imd_parameter.inc.php');
	exit();

}
if ( $default== 2 )
{
	require_once 'include/imd_operation.inc.php';
	exit();
}
if ( $default== 3 )
{
	require_once 'include/imd_export.inc.php';
	exit();
}
if ( $default== 4 )
{
	require_once 'include/imd_history.inc.php';
	exit();
}

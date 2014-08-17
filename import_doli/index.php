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

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief main file for importing card
 */

/*
 * load javascript
 */
require_once('include/class_import_card.php');
require_once('class_acc_ledger.php');
require_once('class_acc_ledger_purchase.php');
require_once('class_acc_ledger_sold.php');
require_once('class_acc_operation.php');
global $cn;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.phpcompta.eu/doku.php?id=importation_dolibarr" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';
$cn=new Database(dossier::id());
global $version_plugin;
$version_plugin=SVNINFO;

Extension::check_version(4802);

// Javascript
 ob_start();
 require_once('impdol.js');
$j=ob_get_contents();
ob_end_clean();
echo create_script($j);



$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code']."&ac=".$_REQUEST['ac'];

$array=array (
	array($url.'&sa=fiche',_('Fiches'),_('Importation de nouvelles fiches'),1),
	array($url.'&sa=opr',_('Opérations Ventes/Achats'),_('Importation d\'opérations de vente ou d\'achat'),2),
	array($url.'&sa=parm',_('Paramètrage'),_('Paramètrage'),5)
	);

$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:1;
switch($sa)
  {
  case 'fiche':
    $default=1;
    break;
  case 'opr':
    $default=2;
    break;
  case 'parm':
    $default=5;
    break;
  default:
    $default=0;
  }

  if ($cn->exist_schema('impdol') == false)
  {
    require_once('include/class_install_impdol.php');

    $iplugn=new Install_Impdol();
    $iplugn->install($cn);

  }
echo ShowItem($array,'H','mtitle','mtitle',$default,' style="width:80%;margin-left:10%"');
echo '<div class="content" style="padding:10">';
if ($default==1)
{
	if ( ! isset($_REQUEST['sb']))
	{
		Import_Card::new_import();
		exit();
	}

	if ( $_REQUEST['sb']=='test')
	{
		if (Import_Card::test_import() == 0 )    exit();
		Import_Card::new_import();
		exit();

	}

	if($_REQUEST['sb'] == 'record')
	{
		if (Import_Card::record_import() ==0 )     exit();
		Import_Card::new_import();
  }
}
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

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
require_once('include/class_import_bank.php');
require_once('bank_constant.php');
require_once('class_html_table.php');
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(6722);
global $cn;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.phpcompta.eu/doku.php?id=importation_de_banque" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';
$cn=new Database(dossier::id());

ob_start();
require_once('bank_js.js');
$j=ob_get_contents();
ob_end_clean();
echo create_script($j);

/*
 * Menu
 */
global $cn;

$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code']."&ac=".$_REQUEST['ac'];

$array=array (
	array($url.'&sa=import',_('Importation'),_('Importation de nouveaux fichiers'),1),
	array($url.'&sa=purge',_('Liste Import '),_('Historique des imports effectués'),5)
	);

$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:1;
switch($sa)
  {
  case 'import':
    $default=1;
    break;
  case 'reconc':
    $default=2;
    break;
  case 'transfer':
    $default=3;
    break;
  case 'purge':
    $default=5;
    break;
  default:
    $default=0;
  }
echo ShowItem($array,'H','mtitle','mtitle',$default,' style="width:80%;margin-left:10%"');
if ($cn->exist_schema('importbank') == false)
  {
    require_once('include/class_install_plugin.php');

    $iplugn=new Install_Plugin($cn);
    $iplugn->install();

  }
/*
 *Menu : import bank, reconciliation operation, purge temporary table
 */
if ( ! isset($_REQUEST['sa']))
  {
    require_once('include/import_bank.php');
    exit();
  }
/*
 * Import file
 */
if ( $_REQUEST['sa']=='import')
  {
    require_once('include/import_bank.php');
    exit();
  }

if($_REQUEST['sa'] == 'reconc')
  {
    Import_Bank::reconciliation();
  }


if($_REQUEST['sa'] == 'purge')
  {
    echo '<div class="content" style="width:80%;margin-left:10%">';
    if (isset($_REQUEST['delete']))
      {
	Import_Bank::delete($_REQUEST);
      }
    if (isset($_REQUEST['delete_record']))
      {
	Import_Bank::delete_record($_REQUEST);
      }
    if (isset($_REQUEST['transfer_record']))
      {
	Import_Bank::transfer_record($_REQUEST);
      }

    /*
     * Show all the import
     */
    if ( ! isset($_REQUEST['sb']))
      {
	Import_Bank::show_import();
	exit();
      }
    if ($_REQUEST['sb'] == 'list')
      {
	Import_Bank::list_record($_REQUEST['id']);
	exit();
      }

  }

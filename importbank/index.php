<?php
/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
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
global $cn;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.phpcompta.eu/doku.php?id=importation_de_banque" target="_blank">Aide</a></div>';
$cn=new Database(dossier::id());

/*
 * Menu
 */
global $cn;

$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code'];

$array=array (
	array($url.'&sa=import',_('Importation'),_('Importation de nouveaux fichiers'),1),
	array($url.'&sa=reconc',_('Reconciliation'),_('Réconciliation entre les opérations importées'),2),
	array($url.'&sa=transfer',_('Transfert'),_('Transfert vers la comptabilité des op. importées'),3),
	array($url.'&sa=purge',_('Purge '),_('Vide les imports effectués'),5)
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

if($_REQUEST['sa'] == 'transfer')
  {
    Import_Bank::transfert();
  }

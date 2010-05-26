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
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USAtv
*/
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief main entry
 */

require_once('class_database.php');
require_once('class_dossier.php');
$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code'];

$cn=new Database (dossier::id());

if ( $cn->exist_schema('amort') ==false ){
  require_once('class_install_plugin.php');
  $plugin=new Install_Plugin($cn);
  $plugin->install();
}

$menu=array(
	    array($url.'&sa=card','Biens amortissables','Liste des biens amortissables',1),
	    array($url.'&sa=report','Rapport','rapport et  tableaux sur les biens amortissables',2),
	    array($url.'&sa=util','Utilitaire','Génération écriture comptable',3)
	    );
	    

$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:0;
$def=0;

switch($sa) {
case 'card':
  $def=1;
  break;
case 'report':
  $def=2;
  break;
case 'util':
  $def=3;
  break;
}

/* List + add and modify card */
if ($def==1) {
  require_once('am_card.php');
  exit();
}
/* report */
if ( $def==2) {
  require_once('am_print.php');
  exit();
}
/* Utility : write in accountancy */
if ( $def==3) {
  require_once('am_util.php');
  exit();
}
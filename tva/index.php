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
 * \brief main file for tva
 */

$url='?'.dossier::get().'&code=tva';
$array=array (
	array($url.'&sa=dec',_('Déclaration TVA'),_('Déclaration Trimestriel ou annuel de TVA'),1),
	array($url.'&sa=li',_('Listing intracommunautaire'),_('Listing intracommunautaire trimestriel'),2),
	array($url.'&sa=lc',_('Listing Assujetti'),_('Listing des clients assujettis'),3),
	array($url.'&sa=ltva',_('Liste des déclarations TVA'),_('Historique des déclarations TVA'),4),
	array($url.'&sa=param',_('Paramètrage '),_('Paramètre pour la TVA'),5)
	);

$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:0;
$def=0;
switch($sa) {
case 'dec':
  $def=1;
  break;
case 'li':
  $def=2;
  break;
case 'lc':
  $def=3;
  break;
case 'ltva':
  $def=4;
  break;
case 'param':
  $def=5;
  break;

}

$cn=new Database(dossier::id());
if ( $cn->exist_schema('tva_belge') == false) {
  require_once('class_install_plugin.php');

  $iplugn=new Install_Plugin($cn);
  $iplugn->install();
  /**
   *@todo améliorer le message, peu cosmétique
   */
  echo_warning(_("L'extension est installée, pourriez-vous en vérifier le paramètrage ?"));
  $def=5;
}

// show menu
echo ShowItem($array,'H',"mtitle","mtitle",$def,' width="100%" ');

// include the right file
if ($def==1) {
  require_once('decl_tva.inc.php');
  exit();
}

/* Listing of all */
if ($def==4) {
  require_once('list_tva.inc.php');
  exit();
}
/* listing intracomm */
if ($def==2) {
  require_once('list_intra.inc.php');
  exit();
}
/* listing assujetti */
if ($def==3) {
  require_once('list_assujetti.inc.php');
  exit();
}

/* setting */
if ( $def==5) {
  require_once('tva_param.inc.php');
  exit();
}
?>

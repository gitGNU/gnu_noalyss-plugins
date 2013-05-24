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
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(4900);

$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code']."&ac=".$_REQUEST['ac'];
$array=array (
	array($url.'&sa=dec',_('Déclaration TVA'),_('Déclaration Trimestriel ou annuel de TVA'),1),
	array($url.'&sa=li',_('Listing intracommunautaire'),_('Listing intracommunautaire trimestriel'),2),
	array($url.'&sa=lc',_('Listing Assujetti'),_('Listing des clients assujettis'),3),
	array($url.'&sa=ltva',_('Liste des déclarations TVA'),_('Historique des déclarations TVA'),4),
	array($url.'&sa=param',_('Paramètrage '),_('Paramètre pour la TVA'),5)
	);
echo '<script language="javascript">';
require_once('js_scripts.js');
echo '</script>';

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
$install=0;
$cn=new Database(dossier::id());
if ( $cn->exist_schema('tva_belge') == false) {
  require_once('class_install_plugin.php');
  $install=1;
  $iplugn=new Install_Plugin($cn);
  $iplugn->install();
  echo_warning(_("L'extension est installée, pourriez-vous en vérifier le paramètrage ?"));
  $def=5;
}

// check schema
$a=$cn->exist_column('assujetti_chld','ac_periode','tva_belge');
if ( $a == false)
  $cn->exec_sql("alter table tva_belge.assujetti_chld add ac_periode text");

$a=$cn->exist_column('assujetti_chld','exercice','tva_belge');
if ( $a == false)
  $cn->exec_sql("alter table tva_belge.assujetti_chld add exercice text");

$a=$cn->exist_column('declaration_amount','exercice','tva_belge');
if ( $a == false)
  $cn->exec_sql("alter table tva_belge.declaration_amount add exercice text");

$a=$cn->exist_column('intracomm','exercice','tva_belge');
if ( $a == false)
  $cn->exec_sql("alter table tva_belge.intracomm add exercice text");

$a=$cn->exist_column('assujetti','exercice','tva_belge');
if ( $a == false)
  $cn->exec_sql("alter table tva_belge.assujetti add exercice text");
if ( $cn->exist_table("version","tva_belge")==false)
{

  $file=dirname(__FILE__)."/sql/patch2.sql";
		$cn->execute_script($file);
		if ( $install == 0 ) echo_warning(_("Mise à jour du plugin, pourriez-vous en vérifier le paramètrage ?"));
		$def=5;
}
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.phpcompta.eu/doku.php?id=tva" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';
// show menu
?>
<h2 class="notice">Attention, ce plugin ne permet que le calcul TVA pour 2012 et régime transitoire en 2013, employez
		plutôt l'extension "rapport avancé"
	</h2>
<?php
echo ShowItem($array,'H',"mtitle","mtitle",$def,' style="width:80%;margin-left:10%;border-spacing:5;" ');
?>
<div class="content" style="margin-left: 30px">
<?php
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
</div>

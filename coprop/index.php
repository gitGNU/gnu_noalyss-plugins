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
 * \brief main file for tva
 */

/*
 * load javascript
 */

require_once 'class_acc_ledger.php';
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(6800);
ob_start();
require_once('coprop-javascript.js');
$j=ob_get_contents();
ob_end_clean();
echo create_script($j);

$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code'].'&ac='.$_REQUEST['ac'];
$array=array (
         array($url.'&sa=lot',_('Immeubles,lots et copropriétaires'),_('Listes des immeubles, copropriétaire et lots '),1),
         array($url.'&sa=cle',_('Clef de répartition'),_('Clef de répartition'),2),
         array($url.'&sa=af',_('Appel de fond'),_('Création décompte pour appel de fond'),3),
         array($url.'&sa=bu',_('Budget'),_('budgets'),5),
         array($url.'&sa=hs',_('Historique'),_('Historique des appels'),6),
         array($url.'&sa=pa',_('Paramètre'),_('Configuration et paramètre'),4)
       );

$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:0;
$def=0;
switch($sa)
  {
  case 'lot':
    $def=1;
    break;
  case 'cle':
    $def=2;
    break;
  case 'af':
    $def=3;
    break;
  case 'pa':
    $def=4;
    break;
  case 'bu':
      $def=5;
      break;
  case 'hs':
	  $def=6;
	  break;
  }

$cn=new Database(dossier::id());
if ( $cn->exist_schema('coprop') == false)
  {
    require_once('include/class_install_plugin.php');
/**
 * créer fiche immeuble, lot, copropriétaire, ajouter des select dans catégorie lots pour montrer les copro + immeubles
 */
    $iplugn=new Install_Plugin($cn);
    $iplugn->install();
    echo_warning(_("L'extension est installée, pourriez-vous en vérifier le paramètrage ?"));
	// Affiche paramètre
    $def=4;
  }
  require_once('coprop-constant.php');
  $version=$cn->get_value('select max(v_id) from coprop.version');
 if ( $version < COPROP_VERSION)
 {
	   require_once('include/class_install_plugin.php');
	  $iplugin=new Install_Plugin($cn);
	  $iplugin->upgrade();
 }

// show menu
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=plugin:copropriété" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';

echo ShowItem($array,'H','mtitle',' ',$def,' style="width:98%" class="topmenu"');

// include the right file
/*
 * Lot
 */
if ($def==1)
  {
    require_once('include/lot.inc.php');
    exit();
  }

/*
 * Paramètre
 */
if ($def==4)
  {
    require_once('include/copro-parameter.inc.php');
    exit();
  }
/*
 * Clef
 */
if ($def==2)
  {
    require_once('include/key.inc.php');
    exit();
  }
/*
 * Appel de fond
 */
if ($def==3)
  {
    require_once('include/appel_fond.inc.php');
    exit();
  }
if ($def==5)
  {
    require_once('include/budget.inc.php');
    exit();
  }
if ($def==6)
  {
    require_once('include/historic.inc.php');
    exit();
  }

?>

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
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USAtv
*/
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(6800);
/*!\file
 * \brief main entry
 */
require_once('amortis_constant.php');
require_once NOALYSS_INCLUDE.'/lib/class_database.php';
require_once NOALYSS_INCLUDE.'/class/class_dossier.php';
/*
 * load javascript
 */
ob_start();
require_once('amortize_javascript.js');
$j=ob_get_contents();
ob_end_clean();
echo create_script($j);

$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code'].'&ac='.$_REQUEST['ac'];

$cn=Dossier::connect();;

if ( $cn->exist_schema('amortissement') ==false )
  {
    require_once('include/class_install_plugin.php');
    $plugin=new Install_Plugin($cn);
    $plugin->install();
  }
if ( $cn->get_value('select max(val) from amortissement.version') < $amortissement_version )
{
        require_once('include/class_install_plugin.php');
	$iplugn = new Install_Plugin($cn);
        $current=$cn->get_value('select max(val) from amortissement.version') ;
        for ( $e = $current;$e <= $amortissement_version ; $e++)
        {
            $iplugn->upgrade($e);
        }
}
$menu=array(
        array($url.'&sa=card','Biens amortissables','Liste des biens amortissables',1),
        array($url.'&sa=report','Rapport','rapport et  tableaux sur les biens amortissables',2),
        array($url.'&sa=util','Utilitaire','Génération écriture comptable',3)
      );


$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:'card';
$_REQUEST['sa']=$sa;
$def=0;

switch($sa)
  {
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
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=amortissement" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';
echo '<div class="topmenu">';
echo ShowItem($menu,'H','mtitle ','mtitle ',$def,'class="mtitle"');
echo '</div>';

/* List + add and modify card */
if ($def==1)
  {
    require_once('include/am_card.php');
    exit();
  }
/* report */
if ( $def==2)
  {
    require_once('include/am_print.php');
    exit();
  }
/* Utility : write in accountancy */
if ( $def==3)
  {
    require_once('include/am_util.php');
    exit();
  }

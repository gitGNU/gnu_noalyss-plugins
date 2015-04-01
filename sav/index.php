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

// Copyright (c) 2015 Author Dany De Bontridder dany@alchimerys.be

/*!\file
 * \brief main file for SAV
 */

/*
 * load javascript
 */
require_once 'sav_constant.php';

ob_start();
require_once('sav_javascript.js');
$j=ob_get_contents();
ob_end_clean();
echo create_script($j);
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(6800);

$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code'].'&ac='.$_REQUEST['ac'];

$menu=array (
         array($url.'&sa=enc',_('En-cours'),_('Encodage de fiche'),1),
         array($url.'&sa=histo',_('Historique'),_('Toutes les fiches'),3),
         array($url.'&sa=param',_('Paramètre SAV'),_('Paramétrage du module SAV'),2)
       );

$sa=HtmlInput::default_value_request('sa',0);
$def=0;
switch($sa)
  {
  case 'enc':
    $def=1;
    break;
  case 'param':
    $def=2;
    break;
  case 'histo':
    $def=3;
    break;
  }

if ( $cn->exist_schema('service_after_sale') == false)
  {
    require_once('include/class_install_plugin.php');

    $iplugn=new Install_Plugin($cn);
    $iplugn->install();
    /**
     *@todo améliorer le message, peu cosmétique
     */
    echo_warning(_("L'extension est installée, pourriez-vous en vérifier le paramètrage ?"));
    $def=5;
  }

// show menu
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.phpcompta.eu/doku.php?id=sav" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';
echo '<div class="menu2">';
echo ShowItem($menu,'H','mtitle ','mtitle ',$def,'class="mtitle"');
echo '</div>';

// include the right file
if ($def==1)
  {
    require_once('include/sav_card.inc.php');
    exit();
  }
if ($def==2)
  {
    require_once('include/sav_param.inc.php');
    exit();
  }
if ( $def == 3 )
{
    require_once 'include/sav_histo.php';
}
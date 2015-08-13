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
 * \brief main file for tools
 */
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(5082);

/*
 * load javascript
 */
ob_start();
require_once('tools_javascript.js');
$j=ob_get_contents();
ob_end_clean();
echo create_script($j);

$url='?'.dossier::get()."&plugin_code=".$_REQUEST['plugin_code']."&ac=".$_REQUEST['ac'];
$array=array (
         array($url.'&sa=op',_('Opération'),_('Changer poste comptable ou fiche'),1),
         array($url.'&sa=pj',_('Pièce'),_('Rénuméroter des pièces justificative'),2),
         array($url.'&sa=exe',_('Exercice'),_('Ajouter des années comptables'),3),
         array($url.'&sa=exp',_('Export'),_('Exporter les opérations'),4)
       );

$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:"";
$def=0;
switch($sa)
  {
  case 'op':
    $def=1;
    break;
  case 'pj':
    $def=2;
    break;
  case 'exe':
	  $def=3;
	  break;
  case 'exp':
      $def=4;
      break;
  }

$cn=new Database(dossier::id());
// show menu
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=outil_comptable" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';

echo ShowItem($array,'H','mtitle ','mtitle ',$def,' style="width:80%;margin-left:10%;border-collapse: separate;border-spacing:  5px;"');
require_once('include/tool_function.php');
echo '<div class="content" style="width:80%;margin-left:10%">';
// include the right file
if ($def==1)
  {
    require_once('include/operation.inc.php');
    exit();
  }

/* Receipt */
if ($def==2)
  {
    require_once('include/receipt.inc.php');
    exit();
  }
/* Exercice */
  if ($def == 3 )
  {
	  require_once 'include/tools_exercice.inc.php';
	  exit();
  }
  /** export */
  if ($def == 4 )
  {
      require_once 'include/export_operation.inc.php';
      exit();
  }
?>

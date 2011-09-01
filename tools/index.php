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
 * \brief main file for tools 
 */

/*
 * load javascript
 */
ob_start();
require_once('tools_javascript.js');
$j=ob_get_contents();
ob_clean();
echo create_script($j);

$url='?'.dossier::get()."&plugin_code=".$_REQUEST['plugin_code'];
$array=array (
         array($url.'&sa=op',_('Opération'),_('Changer poste comptable ou fiche'),1),
         array($url.'&sa=pj',_('Pièce'),_('Rénuméroter des pièces justificative'),2)
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
  }

$cn=new Database(dossier::id());
// show menu
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.phpcompta.eu/doku.php?id=outil_comptable" target="_blank">Aide</a></div>';

echo ShowItem($array,'H','mtitle ','mtitle ',$def,' style="width:80%;margin-left:10%;border-collapse: separate;border-spacing:  5px;"');
require_once('include/tool_function.php');
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
?>

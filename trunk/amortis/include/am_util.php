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
 * \brief let you generate the accounting for the paid off for a selected
 *  year history and remove
 */
$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code'].'&sa=util'.'&ac='.$_REQUEST['ac'];

$menu=array(
	    array($url.'&sb=generate','Génére écriture',' Génération écriture comptable ',1),
	    array($url.'&sb=histo','Historique','Historique des opérations',3)
	    );


$sb=(isset($_REQUEST['sb']))?$_REQUEST['sb']:-1;
$_REQUEST['sb']=$sb;
$def=0;

switch($sb)
  {
  case 'generate':
    $def=1;
    break;
  case 'histo':
    $def=3;
    break;
  }

echo ShowItem($menu,'H','mtitle ','mtitle ',$def,' style="width:40%;margin-left:10%;border-collapse: separate;border-spacing:  5px;"');

/* List + add and modify card */
if ($def==1)
  {
    require_once('am_generate.inc.php');
    exit();
  }

/* histo */
if ( $def==3)
  {
    require_once('am_histo.inc.php');
    exit();
  }

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
 * \brief this file let you print a report about
 * - card (simple listing)
 * - paid off of the selected year
 * - ...
 */
$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code'].'&sa=report'."&ac=".$_REQUEST['ac'];

$menu=array(
	    array($url.'&sb=dfiche','Détail fiche',' Détail fiche ',1),
	    array($url.'&sb=dall','Listing','',2),
	    array($url.'&sb=tabyear','Tableau / année','Tableau amortissement par année',3)
	    );


$sb=(isset($_REQUEST['sb']))?$_REQUEST['sb']:-1;
$_REQUEST['sb']=$sb;
$def=0;

switch($sb)
  {
  case 'dfiche':
    $def=1;
    break;
  case 'dall':
    $def=2;
    break;
  case 'tabyear':
    $def=3;
    break;
  }
echo '<div class="menu2">';
echo ShowItem($menu,'H','mtitle ','mtitle ',$def,' class="mtitle" ');
echo '</div>';

/* List + add and modify card */
if ($def==1)
  {
    require_once('print_fiche.inc.php');
    exit();
  }

/* all */
if ( $def==2)
  {
    require_once('print_all.inc.php');
    exit();
  }

/* table per year */
if ( $def==3)
  {
    require_once('print_table.inc.php');
    exit();
  }


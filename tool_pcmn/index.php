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
 * \brief main file for importing or exporting accountancy
 */

/*
 * load javascript
 */
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(6800);
global $cn;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=importation_de_plan_comptable" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';
$cn=new Database(dossier::id());
/*
 * load javascript
 */

$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code']."&ac=".$_REQUEST['ac'];

$cn=new Database (dossier::id());


$menu=array(
        array($url.'&sa=import','Importation','Importation de plan comptable',1),
        array($url.'&sa=export','Export','Exportation du plan comptable',2),
      );


$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:'';
$_REQUEST['sa']=$sa;
$def=0;

switch($sa)
  {
  case 'import':
    $def=1;
    break;
  case 'export':
    $def=2;
    break;
  }

echo ShowItem($menu,'H','mtitle ','mtitle ',$def,' style="width:80%;margin-left:10%;border-collapse: separate;border-spacing:  5px;"');

/* Importat files */
if ($def==1)
  {
    require_once('include/import_plan.php');
    exit();
  }
/* export */
if ( $def==2)
  {
    require_once('include/export_plan.php');
    exit();
  }

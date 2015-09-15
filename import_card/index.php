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
 * \brief main file for importing card
 */
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(6800);
/*
 * load javascript
 */

require_once('include/class_import_card.php');
global $cn;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=importation_de_fiche" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';
$cn=Dossier::connect();
if ( ! isset($_REQUEST['sa']))
  {
    Import_Card::new_import();
    exit();
  }

if ( $_REQUEST['sa']=='test')
  {
    if (Import_Card::test_import() == 0 )    exit();
    Import_Card::new_import();
    exit();

  }

if($_REQUEST['sa'] == 'record')
  {
    if (Import_Card::record_import() ==0 )     exit();
    Import_Card::new_import();
  }

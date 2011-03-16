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
 * \brief main file for importing card
 */

/*
 * load javascript
 */
require_once('include/class_import_bank.php');
global $cn;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.phpcompta.eu/doku.php?id=importation_de_banque" target="_blank">Aide</a></div>';
$cn=new Database(dossier::id());
/*
 *Menu : import bank, reconciliation operation, purge temporary table
 */
if ( ! isset($_REQUEST['sa']))
  {
    Import_Bank::new_import();
    exit();
  }

if ( $_REQUEST['sa']=='test')
  {
    if (Import_Bank::test_import() == 0 )    exit();
    Import_Bank::new_import();
    exit();

  }

if($_REQUEST['sa'] == 'record')
  {
    if (Import_Bank::record_import() ==0 )     exit();
    Import_Bank::new_import();
  }
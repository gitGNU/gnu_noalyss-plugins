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
 * \brief this file is included to create a  new VAT declaration
 * 
 */
require_once('class_ext_tva.php');
  // if the periode is not set we have to ask it
if ( ! isset($_REQUEST['decl']) ){
  echo Ext_Tva::choose_periode();
  exit;
}
// verify the year
if ( trim(strlen($_REQUEST['year'])) < 4 || isNumber($_REQUEST['year'] ) == 0 ||$_REQUEST['year'] < 2000||$_REQUEST['year']>2100) {
  alert(j(_('Année invalide'.' ['.$_REQUEST['year'].']')));
  echo Ext_Tva::choose_periode();
  exit;
}
$cn=new Database(Dossier::id());
$tva=new Ext_Tva($cn);

/* by month */
if ( $_REQUEST['periodic']==1) {
  $str_monthly='';
  $str_month=$_GET['bymonth'];$str_year=$_GET['year'];$str_quaterly="";$str_hidden='';$str_submit='';$str_quater='-';
  $tva->blank($_REQUEST['year'],$_GET['bymonth'],1);

  require_once('form_periode.php');
}

/* by quater */
if ($_REQUEST['periodic'] == 2) {
  $str_quaterly='';
  $str_month="-";
  $str_year=$_GET['year'];
  $str_quater=$_REQUEST['byquaterly'];
  $str_hidden='';$str_submit='';$str_monthly='';
  $tva->blank($_REQUEST['year'],$_GET['byquaterly'],2);
  require_once('form_periode.php');
}

echo $tva->display_info();
$tva->compute();
echo $tva->display_declaration_amount();


  // create the XML files and the OD operation, validate it to save it into the vat history


?>
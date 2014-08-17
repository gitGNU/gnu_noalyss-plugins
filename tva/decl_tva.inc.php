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

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief this file is included to create a  new VAT declaration
 * 
 */
require_once('class_ext_tva.php');
echo '<div class="content" style="width:80%;margin-left:10%">';

// verify the year
if ( isset($_REQUEST['year']) && (trim(strlen($_REQUEST['year'])) < 4 || isNumber($_REQUEST['year'] ) == 0 ||$_REQUEST['year'] < 2000||$_REQUEST['year']>2100)) {
  alert(j(_('Année invalide'.' ['.$_REQUEST['year'].']')));
  echo Ext_Tva::choose_periode();
  echo '</div>';
  exit;
}

  // if the periode is not set we have to ask it
if ( ! isset($_REQUEST['decl']) ){
  echo Ext_Tva::choose_periode();
  echo '</div>';
  exit;
}
$cn=new Database(Dossier::id());
if (isset($_POST['save'] )) {
  $save=new Ext_Tva($cn);
  $save->from_array($_POST);
  $save->insert();
  echo h2info(_('Déclaration sauvée'));
  echo $save->display();
  /**
   *@todo add a div for the button generate, get_xml, create ODS, print...
   */
//   echo '<div style="position:absolute;z-index:14;top:25%;right:30" class="noprint">aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa</div>';
  echo '</div>';
  exit;
}

$tva=new Ext_Tva($cn);

/* by month */
if ( $_REQUEST['periodic']==1) {
  $str_monthly='';
  $str_month=$_GET['bymonth'];$str_year=$_GET['year'];$str_quaterly="";$str_hidden='';$str_submit='';$str_quater='-';
  $tva->blank($_REQUEST['year'],$_GET['bymonth'],1);

}

/* by quater */
if ($_REQUEST['periodic'] == 2) {
  $str_quaterly='';
  $str_month="-";
  $str_year=$_GET['year'];
  $str_quater=$_REQUEST['byquaterly'];
  $str_hidden='';$str_submit='';$str_monthly='';
  $tva->blank($_REQUEST['year'],$_GET['byquaterly'],2);
}
try {
  $r=$tva->compute();
} catch (Exception $e) {

  echo '<div class="content" style="width:80%;margin-left:10%">';
  echo Ext_Tva::choose_periode();
  echo '</div>';
  exit();
  }
require_once('form_periode.php');
echo '<form class="print" method="post">';
echo dossier::hidden();
echo HtmlInput::extension();
echo HtmlInput::hidden('start_periode',$tva->start_periode);
echo HtmlInput::hidden('end_periode',$tva->end_periode);
echo HtmlInput::hidden('flag_periode',$tva->flag_periode);
echo HtmlInput::hidden('name',$tva->tva_name);
echo HtmlInput::hidden('num_tva',$tva->num_tva);
echo HtmlInput::hidden('adress',$tva->adress);
echo HtmlInput::hidden('country',$tva->country);
echo HtmlInput::hidden('exercice',$tva->exercice);



echo $tva->display_info();
echo $r;
echo $tva->display_declaration_amount();
echo HtmlInput::submit('save',_('Sauvegarde'));
echo '</form>';
echo '</div>';
  // create the XML files and the OD operation, validate it to save it into the vat history


?>

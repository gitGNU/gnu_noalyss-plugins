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
 * \brief listing intracom
 */
require_once('class_ext_list_intra.php');
// verify the year
if ( isset($_REQUEST['year']) && (trim(strlen($_REQUEST['year'])) < 4 || isNumber($_REQUEST['year'] ) == 0 ||$_REQUEST['year'] < 2000||$_REQUEST['year']>2100)) {
  alert(j(_('Année invalide'.' ['.$_REQUEST['year'].']')));
  echo Ext_List_Intra::choose_periode();
  exit;
}

// if the periode is not set we have to ask it
if ( ! isset($_REQUEST['decl']) ){
  echo Ext_List_Intra::choose_periode(true);
  exit;
}

$cn=new Database(Dossier::id());
if (isset($_POST['save'] )) {
  $save=new Ext_List_Intra($cn);
  $save->from_array($_POST);
  $save->insert();
  echo h2info(_('Déclaration sauvée'));
  echo $save->display();

  /**
   *@todo add a div for the button generate, get_xml, create ODS, print...
   */
   echo '<div style="position:absolute;z-index:14;top:25%;right:30" class="noprint">aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa</div>';
   exit;
}

$tva=new Ext_List_Intra($cn);

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
/* by year */
if ($_REQUEST['periodic'] == 3) {
  $str_quaterly='';
  $str_month="";
  $str_year=$_GET['year'];
  $str_byyear='1';
  $str_quater='';
  $str_hidden='';$str_submit='';$str_monthly='';
  $by_year=true;
  $tva->blank($_REQUEST['year'],$_GET['byquaterly'],3);
}

// xdebug_disable();
try {
  $r=$tva->compute();
} catch (Exception $e) {

  echo Ext_List_Intra::choose_periode();
  exit();
  }
xdebug_enable();
require_once('form_periode.php');
echo '<div class="content">';
echo '<form method="post">';
echo dossier::hidden();
echo HtmlInput::extension();
echo HtmlInput::phpsessid();
echo HtmlInput::hidden('start_periode',$tva->start_periode);
echo HtmlInput::hidden('end_periode',$tva->end_periode);
echo HtmlInput::hidden('flag_periode',$tva->flag_periode);
echo HtmlInput::hidden('name',$tva->tva_name);
echo HtmlInput::hidden('num_tva',$tva->num_tva);
echo HtmlInput::hidden('adress',$tva->adress);
echo HtmlInput::hidden('country',$tva->country);
echo HtmlInput::hidden('periode_dec',$tva->periode_dec);
echo $tva->display_info();
echo $r;
echo $tva->display_declaration_amount();
echo HtmlInput::submit('save',_('Sauvegarde'));
echo '</form>';
echo '</div>';


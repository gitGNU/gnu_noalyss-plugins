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
 * \brief confirm operation before save it but verify first
 */
require_once ('class_acc_ledger_purchase.php');
require_once ('class_acc_ledger_sold.php');
require_once ('class_modop_operation.php');

$act=new Modop_Operation($cn,'');
$act->suspend_strict();
//----------------------------------------------------------------------
// ACH
if ( $_POST['jrn_type'] == 'ACH') {
  $jrn=new Acc_Ledger_Purchase($cn,$_POST['p_jrn']);
  try {
  echo '<FORM enctype="multipart/form-data" METHOD="POST">';
  echo HtmlInput::hidden('ac',$_REQUEST['ac']);
  echo HtmlInput::extension().dossier::hidden();
  echo HtmlInput::hidden('action','save');
  echo HtmlInput::hidden('ext_jr_id',$_POST['ext_jr_id']);
  echo HtmlInput::hidden('ext_jr_internal',
			 $_POST['ext_jr_internal']);
  echo $jrn->confirm($_POST);

  echo HtmlInput::submit('save','Sauver');
  echo '</FORM>';
  } catch (Exception $e) {
    alert($e->getMessage());
  }
}
//----------------------------------------------------------------------
// VEN
if ( $_POST['jrn_type'] == 'VEN') {
  $jrn=new Acc_Ledger_Sold($cn,$_POST['p_jrn']);
  try {
  $a=$jrn->confirm($_POST);
  echo '<FORM enctype="multipart/form-data" METHOD="POST">';
  echo HtmlInput::hidden('ac',$_REQUEST['ac']);
  echo HtmlInput::extension().dossier::hidden();
  echo HtmlInput::hidden('action','save');
  echo HtmlInput::hidden('ext_jr_id',$_POST['ext_jr_id']);
  echo HtmlInput::hidden('ext_jr_internal',
			 $_POST['ext_jr_internal']);
  echo $a;

  echo HtmlInput::submit('save','Sauver');
  echo '</FORM>';
  } catch (Exception $e) {
    alert($e->getMessage());
  }

}
//----------------------------------------------------------------------
// ODS
if ( $_POST['jrn_type'] == 'ODS') {
  $jrn=new Acc_Ledger($cn,$_POST['p_jrn']);
  $jrn->with_concerned=false;
  try {
    $jrn->verify($_POST);
    $a= $jrn->input($_POST,1);
    echo '<FORM enctype="multipart/form-data" METHOD="POST">';
    echo HtmlInput::hidden('ac',$_REQUEST['ac']);
    echo HtmlInput::extension().dossier::hidden();
    echo HtmlInput::hidden('action','save');
    echo HtmlInput::hidden('ext_jr_id',$_POST['ext_jr_id']);
    echo HtmlInput::hidden('ext_jr_internal',
			 $_POST['ext_jr_internal']);
    echo $a;

    echo HtmlInput::submit('save','Sauver');
    echo '</FORM>';
  } catch (Exception $e) {
    alert($e->getMessage());
  }

}
$act->activate_strict();

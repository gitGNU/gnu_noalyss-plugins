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
 * \brief confirm operation before save it but verify first
 */
require_once ('class_acc_ledger_purchase.php');
require_once ('class_acc_ledger_sold.php');
require_once ('class_modop_operation.php');

$act=new Modop_Operation($cn,'');
$act->suspend_strict();
//----------------------------------------------------------------------
// ACH
if ( $_GET['jrn_type'] == 'ACH') {
  $jrn=new Acc_Ledger_Purchase($cn,$_GET['p_jrn']);
  try {
  echo '<FORM METHOD="GET">';
  echo HtmlInput::extension().dossier::hidden();
  echo HtmlInput::hidden('action','save');
  echo HtmlInput::hidden('ext_jr_id',$_GET['ext_jr_id']);
  echo HtmlInput::hidden('ext_jr_internal',
			 $_GET['ext_jr_internal']);
  echo $jrn->confirm($_GET);

  echo HtmlInput::submit('save','Sauver');
  echo '</FORM>';
  } catch (Exception $e) {
    alert($e->getMessage());
  }
}
//----------------------------------------------------------------------
// VEN
if ( $_GET['jrn_type'] == 'VEN') {
  $jrn=new Acc_Ledger_Sold($cn,$_GET['p_jrn']);
  try {
  $a=$jrn->confirm($_GET);
  echo '<FORM METHOD="GET">';
  echo HtmlInput::extension().dossier::hidden();
  echo HtmlInput::hidden('action','save');
  echo HtmlInput::hidden('ext_jr_id',$_GET['ext_jr_id']);
  echo HtmlInput::hidden('ext_jr_internal',
			 $_GET['ext_jr_internal']);
  echo $a;

  echo HtmlInput::submit('save','Sauver');
  echo '</FORM>';
  } catch (Exception $e) {
    alert($e->getMessage());
  }

}
//----------------------------------------------------------------------
// ODS
if ( $_GET['jrn_type'] == 'ODS') {
  $jrn=new Acc_Ledger($cn,$_GET['p_jrn']);
  $jrn->with_concerned=false;
  try {
    $jrn->verify($_GET);
    $a= $jrn->show_form($_GET,1);
    echo '<FORM METHOD="GET">';
    echo HtmlInput::extension().dossier::hidden();
    echo HtmlInput::hidden('action','save');
    echo HtmlInput::hidden('ext_jr_id',$_GET['ext_jr_id']);
    echo HtmlInput::hidden('ext_jr_internal',
			 $_GET['ext_jr_internal']);
    echo $a;    

    echo HtmlInput::submit('save','Sauver');
    echo '</FORM>';
  } catch (Exception $e) {
    alert($e->getMessage());
  }

}
$act->activate_strict();

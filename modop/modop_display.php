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
 * \brief display the form for the operation
 */
require_once('class_modop_operation.php');
require_once('class_icard.php');
require_once('class_ipopup.php');
require_once ('class_acc_ledger_purchase.php');
require_once ('class_acc_ledger_sold.php');
require_once('class_periode.php');
echo js_include('acc_ledger.js');
require_once ('class_ipopup.php');
//----------------------------------------------------------------------
// create compute button
$compute=new IPopup('compute');
$compute->value=JS_CALC_LINE;
//$compute->title="Calculatrice";
$compute->drag=true; 
$compute->blocking=false;
$compute->set_height("350");
$compute->set_width("500");
echo $compute->input();

$cn=new Database(dossier::id());

/*  we can't modify from a closed periode */
/*  get periode */
$sql="select jr_tech_per from jrn where jr_internal=$1";
$periode=$cn->get_value($sql,array(trim($_GET['jr_internal'])));
if ( $cn->count() == 0 ) {
  alert('Opération non trouvée');
  exit();
}

$oPeriode=new Periode($cn);
$oPeriode->p_id=$periode;

if ($oPeriode->is_closed() == 1) {
  alert('On ne peut pas modifier dans une période fermée');
  exit();
}

/* check if periode is closed */
// retrieve jrn
$op=new Modop_Operation($cn,$_GET['jr_internal']);
try { $op->format();		/* load format the data into an array with by the class_acc_ledger... */
} catch (Exception $e) {
  alert(j($e->getMessage()));
  exit;
  }
/* ---------------------------------------------------------------------- */
// Purchase
/* ---------------------------------------------------------------------- */
if ($op->ledger_type=='ACH') {

  $jrn=new Acc_Ledger_Purchase($cn,$op->array['p_jrn']);
  echo ICard::ipopup('ipopcard');
  echo ICard::ipopup('ipop_newcard');
  echo IPoste::ipopup('ipop_account');
  $search_card=new IPopup('ipop_card');
  $search_card->title=_('Recherche de fiche');
  $search_card->value='';
  echo $search_card->input();
  $pop_tva=new IPopup('popup_tva');
  $pop_tva->title=_('Choix TVA');
  $pop_tva->value='';
  echo $pop_tva->input();

  echo '<FORM METHOD="GET">';
  $op->suspend_receipt();
  echo $jrn->input($op->array);
  echo HtmlInput::extension().dossier::hidden();
  echo HtmlInput::hidden('action','confirm');
  echo HtmlInput::submit('save','Sauve');
  echo HtmlInput::hidden('e_mp',0);
  echo HtmlInput::hidden('ext_jr_id',$op->jr_id);
  echo HtmlInput::hidden('ext_jr_internal',$op->jr_internal);
  echo HtmlInput::button('add_item',_('Ajout article'),      ' onClick="ledger_add_row()"');
  echo '</form>';
  $op->activate_receipt();

}
/* ---------------------------------------------------------------------- */
// SOLD
/* ---------------------------------------------------------------------- */
if ($op->ledger_type=='VEN') {

  $jrn=new Acc_Ledger_Sold($cn,$op->array['p_jrn']);
  echo ICard::ipopup('ipopcard');
  echo ICard::ipopup('ipop_newcard');
  echo IPoste::ipopup('ipop_account');
  $search_card=new IPopup('ipop_card');
  $search_card->title=_('Recherche de fiche');
  $search_card->value='';
  echo $search_card->input();
  $pop_tva=new IPopup('popup_tva');
  $pop_tva->title=_('Choix TVA');
  $pop_tva->value='';
  echo $pop_tva->input();
  $op->suspend_receipt();

  echo '<FORM METHOD="GET">';
  echo $jrn->input($op->array);
  $op->activate_receipt();

  echo HtmlInput::extension().dossier::hidden();
  echo HtmlInput::hidden('action','confirm');
  echo HtmlInput::submit('save','Sauve');
  echo HtmlInput::hidden('e_mp',0);
  echo HtmlInput::hidden('ext_jr_id',$op->jr_id);
  echo HtmlInput::hidden('ext_jr_internal',$op->jr_internal);
  echo HtmlInput::button('add_item',_('Ajout article'),      ' onClick="ledger_add_row()"');
  echo '</form>';

}
/* ---------------------------------------------------------------------- */
// MISC OP
/* ---------------------------------------------------------------------- */
if ($op->ledger_type=='ODS') {

  $jrn=new Acc_Ledger($cn,$op->array['p_jrn']);
  echo ICard::ipopup('ipopcard');
  echo ICard::ipopup('ipop_newcard');
  echo IPoste::ipopup('ipop_account');
  $search_card=new IPopup('ipop_card');
  $search_card->title=_('Recherche de fiche');
  $search_card->value='';
  echo $search_card->input();
  $op->suspend_receipt();

  echo '<FORM METHOD="GET">';
  echo $jrn->show_form($op->array);
  $op->activate_receipt();

  echo HtmlInput::extension().dossier::hidden();
  echo HtmlInput::hidden('action','confirm');
  echo HtmlInput::submit('save','Sauve');
  echo HtmlInput::hidden('ext_jr_id',$op->jr_id);
  echo HtmlInput::hidden('ext_jr_internal',$op->jr_internal);
  echo HtmlInput::button('add',_('Ajout d\'une ligne'),'onClick="quick_writing_add_row()"');
  echo '</form>';
  echo '<div class="info">'.
    _('Débit').' = <span id="totalDeb"></span>'.
    _('Crédit').' = <span id="totalCred"></span>'.
    _('Difference').' = <span id="totalDiff"></span></div> ';
  echo "<script>checkTotalDirect();</script>";
  echo '</div>';


}

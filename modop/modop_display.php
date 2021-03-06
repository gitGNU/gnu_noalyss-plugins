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
 * \brief display the form for the operation
 */
require_once('class_modop_operation.php');
require_once NOALYSS_INCLUDE.'/lib/class_icard.php';
require_once NOALYSS_INCLUDE.'/lib/class_ipopup.php';
require_once NOALYSS_INCLUDE.'/class/class_acc_ledger_purchase.php';
require_once NOALYSS_INCLUDE.'/class/class_acc_ledger_sold.php';
require_once NOALYSS_INCLUDE.'/class/class_periode.php';
echo js_include('acc_ledger.js');
//----------------------------------------------------------------------
// create compute button
$cn=Dossier::connect();

/*  we can't modify from a closed periode */
/*  get periode */
$sql="select jr_tech_per from jrn where jr_id=$1";
$periode=$cn->get_value($sql,array(trim($_GET['jr_id'])));
if ( $cn->count() == 0 )
{
    alert(_('Opération non trouvée'));
    exit();
}

$oPeriode=new Periode($cn);
$oPeriode->p_id=$periode;

if ($oPeriode->is_closed() == 1)
{
    alert(_('On ne peut pas modifier dans une période fermée'));
    exit();
}

/* check if periode is closed */
// retrieve jrn
$op=new Modop_Operation($cn,$_GET['jr_id']);
try
{
    $op->format();		/* load format the data into an array with by the class_acc_ledger... */
}
catch (Exception $e)
{
    alert(j($e->getMessage()));
    exit;
}
/* ---------------------------------------------------------------------- */
// Purchase
/* ---------------------------------------------------------------------- */
if ($op->ledger_type=='ACH')
{
    try
    {
        $jrn=new Acc_Ledger_Purchase($cn,$op->array['p_jrn']);
        echo '<FORM enctype="multipart/form-data"  METHOD="POST" class="print">';
        $op->suspend_receipt();
        echo HtmlInput::hidden('ac',$_REQUEST['ac']);
        echo $jrn->input($op->array);
        echo HtmlInput::extension().dossier::hidden();
        echo HtmlInput::hidden('bon_comm',$op->array['bon_comm']);
        echo HtmlInput::hidden('other_info',$op->array['other_info']);
        echo HtmlInput::hidden('action','confirm');
        echo HtmlInput::submit('save','Sauve');
        echo HtmlInput::hidden('e_mp',0);
        echo HtmlInput::hidden('ext_jr_id',$op->jr_id);
        echo HtmlInput::hidden('ext_jr_internal',$op->jr_internal);
        echo HtmlInput::button('add_item',_('Ajout article'),      ' onClick="ledger_add_row()"');
        echo '</form>';
        
    }
    catch (Exception $exc)
    {
        alert( $exc->getMessage());
    }

    

}
/* ---------------------------------------------------------------------- */
// SOLD
/* ---------------------------------------------------------------------- */
if ($op->ledger_type=='VEN')
{

    try
    {
        $jrn=new Acc_Ledger_Sold($cn,$op->array['p_jrn']);
        $op->suspend_receipt();

        echo '<FORM enctype="multipart/form-data"  METHOD="POST" class="print">';
        echo $jrn->input($op->array);
        echo HtmlInput::hidden('ac',$_REQUEST['ac']);
        echo HtmlInput::extension().dossier::hidden();
        echo HtmlInput::hidden('action','confirm');
        echo HtmlInput::submit('save','Sauve');
        echo HtmlInput::hidden('e_mp',0);
        echo HtmlInput::hidden('ext_jr_id',$op->jr_id);
        echo HtmlInput::hidden('ext_jr_internal',$op->jr_internal);
        echo HtmlInput::hidden('bon_comm',$op->array['bon_comm']);
        echo HtmlInput::hidden('other_info',$op->array['other_info']);
        echo HtmlInput::button('add_item',_('Ajout article'),      ' onClick="ledger_add_row()"');
        echo HtmlInput::button('actualiser',_('Actualiser'),      ' onClick="compute_all_ledger();"');
        echo '</form>';
    }
    catch (Exception $exc)
    {
        alert( $exc->getMessage() ) ;
    }


}
/* ---------------------------------------------------------------------- */
// MISC OP
/* ---------------------------------------------------------------------- */
if ($op->ledger_type=='ODS')
{

    $jrn=new Acc_Ledger($cn,$op->array['p_jrn']);
    $op->suspend_receipt();

    echo '<FORM enctype="multipart/form-data"  METHOD="POST" class="print">';
    echo $jrn->input($op->array);
    echo HtmlInput::hidden('ac',$_REQUEST['ac']);
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
/* ---------------------------------------------------------------------- */
// FIN
/* ---------------------------------------------------------------------- */
if ($op->ledger_type=='FIN')
{
    require_once NOALYSS_INCLUDE.'/class/class_acc_ledger_fin.php';
    $f_legend='Banque';
    $jrn=new Acc_Ledger_Fin($cn,$op->array['p_jrn']);

    $f_add_button=new IButton('add_card');
    $f_add_button->label=_('Créer une nouvelle fiche');
    $f_add_button->set_attribute('ipopup','ipop_newcard');
    $f_add_button->set_attribute('filter',$jrn->get_all_fiche_def ());
    $f_add_button->javascript=" select_card_type(this);";
    $str_add_button=$f_add_button->input();
    echo '<FORM enctype="multipart/form-data"  METHOD="post" class="print">';
    echo HtmlInput::hidden('ac',$_REQUEST['ac']);
    $Date=new IDate("e_date",$op->array['e_date']);
    $f_date=$Date->input();
    $label=HtmlInput::infobulle(3);

    // Ledger (p_jrn)
    //--
    $add_js="";

    $wLedger=$jrn->select_ledger('FIN',2);
    $wLedger->javascript=$add_js;

    if ($wLedger == null) exit ('Pas de journal disponible');

    $label=" Journal ".HtmlInput::infobulle(2) ;
    $f_jrn=$label.$wLedger->input();

    //retrieve bank name
    $e_bank_account=$op->array['e_bank'];
    $e_bank_account_label="";

    // retrieve e_bank_account_label
    if ( $e_bank_account != ""  )
    {
        $fBank=new fiche($cn);
        $fBank->get_by_qcode($e_bank_account);
        $e_bank_account_label=$fBank->strAttribut(ATTR_DEF_NAME).' '.
                              ' Adresse : '.$fBank->strAttribut(ATTR_DEF_ADRESS).' '.
                              $fBank->strAttribut(ATTR_DEF_CP).' '.
                              $fBank->strAttribut(ATTR_DEF_CITY).' ';

    }
    $f_bank=$e_bank_account.$e_bank_account_label;

    $ibank=new ICard();
    $ibank->readonly=false;
    $ibank->label="Banque ".HtmlInput::infobulle(0);
    $ibank->name="e_bank_account";
    $ibank->value=$e_bank_account;
    $ibank->extra='deb';  // credits
    $ibank->typecard='deb';
    $ibank->set_dblclick("fill_ipopcard(this);");
    $ibank->set_attribute('ipopup','ipopcard');

    // name of the field to update with the name of the card
    $ibank->set_attribute('label','e_bank_account_label');
    // Add the callback function to filter the card on the jrn
    $ibank->set_callback('filter_card');
    $ibank->set_function('fill_fin_data');
    $ibank->javascript=sprintf(' onchange="fill_fin_data_onchange(this);" ');

    $f_legend_detail='Opérations financières';

    // Extrait
    $default_pj='';
    $wPJ=new IText('e_pj');
    $wPJ->readonly=false;
    $wPJ->size=10;
    $wPJ->value=$op->array['e_pj'];
    $f_extrait=$wPJ->input();
    $label=HtmlInput::infobulle(7);

    //--------------------------------------------------
    // financial operation
    //-------------------------------------------------

    $array=array();
    // Parse each " tiers"
    $tiers=$op->array['e_other'];
    $tiers_label="";
    $tiers_amount=$op->array['e_amount'];

    $tiers_comment=$op->array['e_comm'];
    $fTiers=new fiche($cn);
    $fTiers->get_by_qcode($tiers);

    $tiers_label=$fTiers->strAttribut(ATTR_DEF_NAME);
    $W1=new ICard();
    $W1->label="";
    $W1->name="e_other";
    $W1->value=$tiers;
    $W1->extra='cred';  // credits
    $W1->typecard='cred';
    $W1->set_dblclick("fill_ipopcard(this);");
    $W1->set_attribute('ipopup','ipopcard');

    // name of the field to update with the name of the card
    $W1->set_attribute('label','e_other_label');
    // name of the field to update with the name of the card
    $W1->set_attribute('typecard','filter');
    // Add the callback function to filter the card on the jrn
    $W1->set_callback('filter_card');
    $W1->set_function('fill_data');
    $W1->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
                            $W1->name);
    $array[0]['qcode']=$W1->input();
    $array[0]['search']=$W1->search();

    // label
    $other=new ISpan("e_other_label", $tiers_label);
    $array[0]['span']=$other->input();
    // Comment
    $wComment=new IText("e_other_comment",$tiers_comment);

    $wComment->size=35;
    $wComment->setReadOnly(false);
    $array[0]['comment']=$wComment->input();
    // amount
    $wAmount=new INum("e_other_amount",$tiers_amount);

    $wAmount->size=7;
    $wAmount->setReadOnly(false);
    $array[0]['amount']=$wAmount->input();
    // show compute
    require_once('template_ledger_fin.php');

    echo HtmlInput::extension().dossier::hidden().HtmlInput::hidden('jrn_type','FIN');
    echo HtmlInput::submit('save',_('Sauve'),'onclick=\'return confirm(&quot;Vous confirmez &quot;)\'');
    echo HtmlInput::hidden('ext_jr_id',$op->jr_id).HtmlInput::hidden('action','save');

    echo '</form>';

}

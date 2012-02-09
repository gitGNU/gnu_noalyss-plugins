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
 * \brief save the operation
 */
require_once ('class_acc_ledger_purchase.php');
require_once ('class_acc_ledger_sold.php');
require_once('class_modop_operation.php');
/* ---------------------------------------------------------------------- */
// Purchase
/* ---------------------------------------------------------------------- */
if ( $_POST['jrn_type'] == 'ACH')
{
    $jrn=new Acc_Ledger_Purchase($cn,$_POST['p_jrn']);
    try
    {
        $op=new Modop_Operation($cn,$_POST['ext_jr_internal']);
        $op->suspend_receipt();
        $op->suspend_strict();
        $pj=$_POST['e_pj'];
		$oldpj=  microtime();
		$cn->exec_sql('update jrn set jr_pj_number=$1  where jr_id=$2',
                      array($oldpj,$_POST['ext_jr_id']));
        $new_internal=$jrn->insert($_POST);
    }
    catch (Exception $e)
    {
        alert($e->getMessage());
        exit();
    }
	$cn->commit();

    /* we delete the old operation */
    $cn->start();
    /* in stock_goods */
    $cn->exec_sql('delete from stock_goods where j_id in (select j_id from jrnx join jrn on (j_grpt=jr_grpt_id) where jr_id=$1)',
                  array($_POST['ext_jr_id']));

    /* in jrnx */
    $cn->exec_sql('delete from jrnx where j_grpt in (select jr_grpt_id from jrn where jr_id=$1)',
                  array($_POST['ext_jr_id']));

    /* in jrn */
    $attach=$cn->get_array('select jr_pj,jr_pj_name,jr_pj_type from jrn where jr_id=$1',
                           array($_POST['ext_jr_id']));
    $cn->exec_sql('delete from jrn where jr_id=$1',array($_POST['ext_jr_id']));
    $cn->exec_sql('update jrn set jr_id=$1,jr_internal=$2,jr_pj_number=$3 where jr_internal=$4',
                  array($_POST['ext_jr_id'],$_POST['ext_jr_internal'],$pj,$new_internal));
    if ($_FILES['pj']['name']=='' && $attach[0]['jr_pj_name'] != '' && ! isset ($_POST['gen_invoice']))
    {
        $cn->exec_sql('update jrn set jr_pj=$1,jr_pj_type=$2,jr_pj_name=$3 where jr_id=$4',
                      array($attach[0]['jr_pj'],$attach[0]['jr_pj_type'],$attach[0]['jr_pj_name'],$_POST['ext_jr_id']));
    }
    /* in quant_purchase */
    $cn->exec_sql('update quant_purchase set qp_internal=$1 where qp_internal=$2',
                  array($_POST['ext_jr_internal'],$new_internal));

    $cn->commit();
    echo '<h2 class="info"> Enregistrement </h2>';
    echo "<h2 >" . _('Opération sauvée') .$_POST['ext_jr_internal'] ;
    if ($jrn->pj != '')
      echo ' Piece : ' . h($jrn->pj);
    echo "</h2>";
    if (strcmp($jrn->pj, $_POST['e_pj']) != 0)
      {
	echo '<h3 class="notice"> ' . _('Attention numéro pièce existante, elle a du être adaptée') . '</h3>';
      }
    /* Save the additional information into jrn_info */
    $obj = new Acc_Ledger_Info($cn);
    $obj->save_extra($jrn->jr_id, $_POST);
    printf('<a class="line" style="display:inline" href="javascript:modifyOperation(%d,%d)">%s</a><hr>', $_POST['ext_jr_id'], dossier::id(), $new_internal);
    // Feedback
    echo $jrn->confirm($_POST, true);
    if (isset($jrn->doc))
      {
	echo '<span class="invoice">';
	echo $jrn->doc;
	echo '</span>';
      }

    echo '</div>';
}
/* ---------------------------------------------------------------------- */
// SOLD
/* ---------------------------------------------------------------------- */
if ( $_POST['jrn_type'] == 'VEN')
{
    $jrn=new Acc_Ledger_Sold($cn,$_POST['p_jrn']);
	$pj=$_POST['e_pj'];
    try
    {	$cn->start();
        $op=new Modop_Operation($cn,$_POST['ext_jr_internal']);
        $op->suspend_receipt();
        $op->suspend_strict();

		$oldpj=  microtime();
		$cn->exec_sql('update jrn set jr_pj_number=$1  where jr_id=$2',
                      array($oldpj,$_POST['ext_jr_id']));
        $new_internal=$jrn->insert($_POST);
    }
    catch (Exception $e)
    {
        alert($e->getMessage());
        exit();
    }
	$cn->commit();
    /* we delete the old operation */
    $cn->start();

    /* in stock_goods */
    $cn->exec_sql('delete from stock_goods where j_id in (select j_id from jrnx join jrn on (j_grpt=jr_grpt_id) where jr_id=$1)',
                  array($_POST['ext_jr_id']));

    /* in jrnx */
    $cn->exec_sql('delete from jrnx where j_grpt in (select jr_grpt_id from jrn where jr_id=$1)',
                  array($_POST['ext_jr_id']));

    /* in jrn */
    $attach=$cn->get_array('select jr_pj,jr_pj_name,jr_pj_type from jrn where jr_id=$1',
                           array($_POST['ext_jr_id']));

    $cn->exec_sql('delete from jrn where jr_id=$1',array($_POST['ext_jr_id']));
    $cn->exec_sql('update jrn set jr_id=$1,jr_internal=$2,jr_pj_number=$3 where jr_internal=$4',
                  array($_POST['ext_jr_id'],$_POST['ext_jr_internal'],$pj,$new_internal));

	if ( $_FILES['pj']['name']=='' && $attach[0]['jr_pj_name'] != '' && ! isset ($_POST['gen_invoice']))
    {
        $cn->exec_sql('update jrn set jr_pj=$1,jr_pj_type=$2,jr_pj_name=$3 where jr_id=$4',
                      array($attach[0]['jr_pj'],$attach[0]['jr_pj_type'],$attach[0]['jr_pj_name'],$_POST['ext_jr_id']));
	 }

    /* in quant_sold */
    $cn->exec_sql('update quant_sold set qs_internal=$1 where qs_internal=$2',
                  array($_POST['ext_jr_internal'],$new_internal));

    $cn->commit();
 /* Show button  */
            echo '<h2 class="info"> Enregistrement </h2>';
            $jr_id=$_POST['ext_jr_id'];

            echo "<h2 >"._('Opération sauvée');
            if ( $jrn->pj != '') echo ' Piece : '.h($jrn->pj);
            echo "</h2>";
            if ( strcmp($jrn->pj,$_POST['e_pj']) != 0 )
            {
                echo '<h3 class="notice"> '._('Attention numéro pièce existante, elle a du être adaptée').'</h3>';
            }

            printf ('<a class="line" style="display:inline" href="javascript:modifyOperation(%d,%d)">%s</a><hr>',
                    $jr_id,dossier::id(),$new_internal);
			echo $jrn->confirm($_POST,true);
            /* Show link for Invoice */
            if (isset ($jrn->doc) )
            {
                echo '<span class="invoice">';
                echo $jrn->doc;
                echo '</span>';
            }


            /* Save the additional information into jrn_info */
            $obj=new Acc_Ledger_Info($cn);
            $obj->save_extra($jr_id,$_POST);


            echo '</div>';
}
/* ---------------------------------------------------------------------- */
// ODS
/* ---------------------------------------------------------------------- */
if ( $_POST['jrn_type'] == 'ODS')
{
    $jrn=new Acc_Ledger($cn,$_POST['p_jrn']);
    try
    {
        $op=new Modop_Operation($cn,$_POST['ext_jr_internal']);
        $op->suspend_receipt();
        $op->suspend_strict();
        $pj=$_POST['e_pj'];
        $_POST['e_pj']=microtime();
        $jrn->save($_POST);
        $new_internal=$jrn->internal;
    }
    catch (Exception $e)
    {
        alert($e->getMessage());
        exit();
    }
    /* we delete the old operation */
    $cn->start();

    /* in jrnx */
    $cn->exec_sql('delete from jrnx where j_grpt in (select jr_grpt_id from jrn where jr_id=$1)',
                  array($_POST['ext_jr_id']));

    /* in jrn */
    $attach=$cn->get_array('select jr_pj,jr_pj_name,jr_pj_type from jrn where jr_id=$1',
                           array($_POST['ext_jr_id']));
    $cn->exec_sql('delete from jrn where jr_id=$1',array($_POST['ext_jr_id']));
    $cn->exec_sql('update jrn set jr_id=$1,jr_internal=$2,jr_pj_number=$3 where jr_internal=$4',
                  array($_POST['ext_jr_id'],$_POST['ext_jr_internal'],$pj,$new_internal));
    if ( $attach[0]['jr_pj_name'] != '')
    {
        $cn->exec_sql('update jrn set jr_pj=$1,jr_pj_type=$2,jr_pj_name=$3 where jr_id=$4',
                      array($attach[0]['jr_pj'],$attach[0]['jr_pj_type'],$attach[0]['jr_pj_name'],$_POST['ext_jr_id']));
    }

    $cn->commit();

}

/* ---------------------------------------------------------------------- */
// Purchase
/* ---------------------------------------------------------------------- */
if ( $_POST['jrn_type'] == 'FIN')
{
    extract ($_POST);
    $user=new User($cn);
    try
    {
        /*  verify if the card can be used in this ledger */
        if ( $user->check_jrn($p_jrn) != 'W' )
            throw new Exception (_('Accès interdit'),20);
        /* check if there is a customer */
        if ( strlen(trim($e_bank_account)) == 0 )
            throw new Exception(_('Vous n\'avez pas donné de banque'),11);

        /*  check if the date is valid */
        if ( isDate($e_date) == null )
        {
            throw new Exception('Date invalide', 2);
        }
        $fiche=new fiche($cn);
        $fiche->get_by_qcode($e_bank_account);
        if ( $fiche->empty_attribute(ATTR_DEF_ACCOUNT) == true)
            throw new Exception('La fiche '.$e_bank_account.'n\'a pas de poste comptable',8);
        if ( $fiche->belong_ledger($p_jrn,'cred') !=1 && $fiche->belong_ledger($p_jrn,'deb') !=1 )
            throw new Exception('La fiche '.$e_bank_account.'n\'est pas accessible à ce journal',10);
        $fiche=new fiche($cn);
        $fiche->get_by_qcode($e_other);
        if ( $fiche->empty_attribute(ATTR_DEF_ACCOUNT) == true)
            throw new Exception('La fiche '.$e_other.'n\'a pas de poste comptable',8);
        if ( $fiche->belong_ledger($p_jrn,'deb') !=1 )
            throw new Exception('La fiche '.$e_other.'n\'est pas accessible à ce journal',10);
        if ( isNumber($ {'e_other_amount'}) == 0 )
            throw new Exception('La fiche '.$e_other.'a un montant invalide ['.$e_other_amount.']',6);
    }
    catch (Exception $e)
    {
        echo $e->getMessage();
        exit();
    }

    try
    {
        $cn->start();
        /* find periode thanks the date */
        $periode=new Periode($cn);
        $periode->find_periode($e_date);
        if ($periode->is_closed())
            throw new Exception ('Période fermée');

        /* update amount */
        $cn->exec_sql("update jrnx set j_montant=$1,j_jrn_def=$3,j_date=to_date($4,'DD.MM.YYYY'),j_tech_per=$5,j_tech_date=now() where j_grpt in (select jr_grpt_id from jrn where jr_id=$2)",array(abs($e_other_amount),$ext_jr_id,$p_jrn,$e_date,$periode->p_id));


        /* in jrn */
        $cn->exec_sql("update jrn set jr_montant=$1,jr_comment=$2,jr_date=to_date($3,'DD.MM.YYYY'),jr_def_id=$4,jr_tech_per=$5,jr_pj_number=$6,jr_tech_date=now() where jr_id=$7",
                      array(abs($e_other_amount),$e_other_comment,$e_date,$p_jrn,$periode->p_id,$e_pj,$ext_jr_id));
        /* in quant_fin */
        /* find the f_id of the bank */
        $fbank=new Fiche($cn);
        $fbank->get_by_qcode($e_bank_account);
        $post_bank=$fbank->strAttribut(ATTR_DEF_ACCOUNT);

        $fother=new Fiche($cn);
        $fother->get_by_qcode($e_other);
        $post_other=$fother->strAttribut(ATTR_DEF_ACCOUNT);
        if ($e_other_amount > 0 )
        {
            $cn->exec_sql('update jrnx set j_poste=$1,j_qcode=$2 where j_debit=false and j_grpt in (select jr_grpt_id from jrn where jr_id=$3)',array($post_other,$e_other,$ext_jr_id));
            $cn->exec_sql('update jrnx set j_poste=$1,j_qcode=$2 where j_debit=true  and j_grpt in (select jr_grpt_id from jrn where jr_id=$3)',array($post_bank,$e_bank_account,$ext_jr_id));
        }
        else
        {
            $cn->exec_sql('update jrnx set j_poste=$1,j_qcode=$2 where j_debit=false  and j_grpt in (select jr_grpt_id from jrn where jr_id=$3)',array($post_bank,$e_bank_account,$ext_jr_id));
            $cn->exec_sql('update jrnx set j_poste=$1,j_qcode=$2 where j_debit=true  and j_grpt in (select jr_grpt_id from jrn where jr_id=$3)',array($post_other,$e_other,$ext_jr_id));
        }
        $cn->exec_sql('update quant_fin set qf_bank=$1,qf_amount=$3,qf_other=$2 where jr_id=$4',array($fbank->id,$fother->id,$e_other_amount,$ext_jr_id));
        $cn->commit();
    }
    catch (Exception $e)
    {
        $cn->rollback();
        echo $e->getMessage();
        exit();
    }
    echo h2info('Opération sauvée');
}

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
if ( $_GET['jrn_type'] == 'ACH') {
  $jrn=new Acc_Ledger_Purchase($cn,$_GET['p_jrn']);
  try {
    $op=new Modop_Operation($cn,$_GET['ext_jr_internal']);
    $op->suspend_receipt();
    $pj=$_GET['e_pj'];
    $_GET['e_pj']=microtime();
    $new_internal=$jrn->insert($_GET);
    $op->activate_receipt();
  } catch(Exception $e) {
    var_dump($e);
  }
  /* we delete the old operation */
  $cn->start();
  /* in stock_goods */
  $cn->exec_sql('delete from stock_goods where j_id in (select j_id from jrnx join jrn on (j_grpt=jr_grpt_id) where jr_id=$1)',
		array($_GET['ext_jr_id']));

  /* in jrnx */
  $cn->exec_sql('delete from jrnx where j_grpt in (select jr_grpt_id from jrn where jr_id=$1)',
		array($_GET['ext_jr_id']));
		
  /* in jrn */
  $cn->exec_sql('delete from jrn where jr_id=$1',array($_GET['ext_jr_id']));
  $cn->exec_sql('update jrn set jr_id=$1,jr_internal=$2,jr_pj_number=$3 where jr_internal=$4',
		array($_GET['ext_jr_id'],$_GET['ext_jr_internal'],$pj,$new_internal));
  /* in quant_purchase */
  $cn->exec_sql('delete from quant_purchase where qp_internal=$1',array($_GET['ext_jr_internal']));
  $cn->exec_sql('update quant_purchase set qp_internal=$1 where qp_internal=$2',
		array($_GET['ext_jr_internal'],$new_internal));
  
  $cn->commit();
}
/* ---------------------------------------------------------------------- */
// SOLD
/* ---------------------------------------------------------------------- */
if ( $_GET['jrn_type'] == 'VEN') {
  $jrn=new Acc_Ledger_Sold($cn,$_GET['p_jrn']);
  try {
    $op=new Modop_Operation($cn,$_GET['ext_jr_internal']);
    $op->suspend_receipt();
    $pj=$_GET['e_pj'];
    $_GET['e_pj']=microtime();
    $new_internal=$jrn->insert($_GET);
    $op->activate_receipt();
  } catch(Exception $e) {
    var_dump($e);
  }
  /* we delete the old operation */
  $cn->start();

  /* in stock_goods */
  $cn->exec_sql('delete from stock_goods where j_id in (select j_id from jrnx join jrn on (j_grpt=jr_grpt_id) where jr_id=$1)',
		array($_GET['ext_jr_id']));

  /* in jrnx */
  $cn->exec_sql('delete from jrnx where j_grpt in (select jr_grpt_id from jrn where jr_id=$1)',
		array($_GET['ext_jr_id']));
		
  /* in jrn */
  $cn->exec_sql('delete from jrn where jr_id=$1',array($_GET['ext_jr_id']));
  $cn->exec_sql('update jrn set jr_id=$1,jr_internal=$2,jr_pj_number=$3 where jr_internal=$4',
		array($_GET['ext_jr_id'],$_GET['ext_jr_internal'],$pj,$new_internal));

  /* in quant_sold */
  $cn->exec_sql('delete from quant_sold where qs_internal=$1',array($_GET['ext_jr_internal']));
  $cn->exec_sql('update quant_sold set qs_internal=$1 where qs_internal=$2',
		array($_GET['ext_jr_internal'],$new_internal));
  $cn->commit();

}
/* ---------------------------------------------------------------------- */
// SOLD
/* ---------------------------------------------------------------------- */
if ( $_GET['jrn_type'] == 'ODS') {
  $jrn=new Acc_Ledger($cn,$_GET['p_jrn']);
  try {
    $op=new Modop_Operation($cn,$_GET['ext_jr_internal']);
    $op->suspend_receipt();
    $pj=$_GET['e_pj'];
    $_GET['e_pj']=microtime();
    $jrn->save($_GET);
    $new_internal=$jrn->internal;
    $op->activate_receipt();
  } catch(Exception $e) {
    var_dump($e);
  }
  /* we delete the old operation */
  $cn->start();

  /* in jrnx */
  $cn->exec_sql('delete from jrnx where j_grpt in (select jr_grpt_id from jrn where jr_id=$1)',
		array($_GET['ext_jr_id']));
		
  /* in jrn */
  $cn->exec_sql('delete from jrn where jr_id=$1',array($_GET['ext_jr_id']));
  $cn->exec_sql('update jrn set jr_id=$1,jr_internal=$2,jr_pj_number=$3 where jr_internal=$4',
		array($_GET['ext_jr_id'],$_GET['ext_jr_internal'],$pj,$new_internal));

  $cn->commit();

}


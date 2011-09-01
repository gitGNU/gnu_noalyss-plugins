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
 * \brief contains functions for tools
 */
  /**
   *@brief check that the array jr_id is not empty
   *@return html error string or empty
   */
function check_jrid()
{
  if (isset($_POST['jr_id']))
    {
      if (empty($_POST['jr_id']))
	{
	  return "Erreur : aucune opération n'est sélectionnée";
	}
    }
  else
    return "Erreur : aucune opération n'est sélectionnée";
  
  return "";
}

  /**
   *@brief change the accounting in the selected operation
   *@param $cn database connx
   *@note use the $_POST variables
   * - sposte accounting source
   * - tposte accounting target
   * - jr_id[]
   */
function change_accounting(&$cn)
{
  $msg= check_jrid();
  if( $msg != "")
    {
      echo " <p class=\"error\">$msg</p>"; return;
    }
  $source=$cn->get_value('select pcm_val from tmp_pcmn where pcm_val=$1',array($_POST['sposte']));
  $target=$cn->get_value('select pcm_val from tmp_pcmn where pcm_val=$1',array($_POST['tposte']));
  if ( $source == '' || $target == '')
    {
      echo '<p class="error"> Il manque soit le poste comptable source  soit le poste comptable destination ou l\'un des deux postes n\'existe pas</p>'; 
      return;
    }
  $cn->prepare('update_account','update jrnx set j_poste = $1 where j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and j_poste=$3
		RETURNING j_id ');
  $cn->prepare('retrieve','select jr_date,jr_comment,jr_montant,jr_internal from jrn where jr_id=$1');
  echo h2info('Opération changée');
  echo 'compte : '.$_REQUEST['sposte'].' vers '.$_REQUEST['tposte'];
  $count=0;
  echo '<table class="result">';
  foreach ($_POST['jr_id'] as $id)
    {
      $update=$cn->execute('update_account',array(trim($_POST['tposte']),$id,trim($_POST['sposte'])));
      if ( Database::num_row($update) ==0 ) continue;
      $feedback=$cn->execute('retrieve',array($id));
      if ( Database::num_row($feedback) != 0)
	{
	  $count++;
	  $row=Database::fetch_array($feedback,0);
	  echo '<tr>';
	  echo td(format_date($row['jr_date']));
	  echo td(HtmlInput::detail_op($id,$row['jr_internal']));
	  echo td($row['jr_comment']);
	  echo td(nbm($row['jr_montant'],' class="num"'));
	  echo '</tr>';
	}
    }
  echo '</table>';
  echo '<p> Nombre d\'opérations changée'.$count.'</p>';
}


  /**
   *@brief change the accounting and card in the selected operations
   *@param $cn database connx
   *@note use the $_POST variables
   * - csource accounting source
   * - ctarget accounting target
   * - jr_id[]
   */
function change_card(&$cn)
{
  $msg= check_jrid();
  if( $msg != "")
    {
      echo " <p class=\"error\">$msg</p>"; return;
    }
  $source=$cn->get_value('select f_id from fiche_detail where ad_id=23 and ad_value=trim(upper($1))',array($_POST['csource']));
  $target=$cn->get_value('select f_id from fiche_detail where ad_id=23 and ad_value=trim(upper($1))',array($_POST['ctarget']));

  if ( $source == '' || $target == '')
    {
      echo '<p class="error"> Il manque soit la fiche source  soit le fiche destination ou l\'une des deux fiches n\'existe pas</p>'; 
      return;
    }
  /*
   *retrieve info about card :  accounting
   */
  $source_account=$cn->get_value('select ad_value from fiche_detail where ad_id=5 and f_id=$1',array($source));
  $target_account=$cn->get_value('select ad_value from fiche_detail where ad_id=5 and f_id=$1',array($target));

  /*
   * test if accounting valid
   */
  if ( $source_account == '' || $target_account == '')
    {
      echo '<p class="error"> L\'une des deux fiche n\'a pas de poste comptable</p>'; 
      return;
    }

  /*
   *Prepare stmt
   */
  $cn->prepare('update_account','update jrnx set f_id = $1, j_poste=$2, j_qcode=trim(upper($3 ))
			where j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$4) and j_qcode=upper(trim($5)) RETURNING j_id');
  /*
   * Change also in quant_sold
   */
  $cn->prepare('update_sold_mer','update quant_sold set qs_fiche=$1 where
		j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and qs_fiche=$3');

  $cn->prepare('update_sold_cust','update quant_sold set qs_client=$1 where
		j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and qs_client=$3');

  /*
   * Change also in  quant_purchase 
   */
  $cn->prepare('update_pur_mer','update quant_purchase set qp_fiche=$1 where
		j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and qp_fiche=$3');

  $cn->prepare('update_pur_cust','update quant_purchase set qp_supplier=$1 where
		j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and qp_supplier=$3');
  /*
   * quant_fin
   */
  $cn->prepare('update_fin_bk','update quant_fin set qf_bank=$1 where
		jr_id=$2 and  qf_bank=$3');

  $cn->prepare('update_fin_oth','update quant_fin set qf_other=$1 where
		jr_id=$2 and  qf_other=$3');



  $cn->prepare('retrieve','select jr_date,jr_comment,jr_montant,jr_internal,jrn_def_type from jrn join jrn_def on (jr_def_id=jrn_def_id)  where jr_id=$1');
  echo h2info('Opération changée');
  echo 'Fiche : '.$_REQUEST['csource'].' vers '.$_REQUEST['ctarget'];
  $count=0;
  echo '<table class="result">';
  foreach ($_POST['jr_id'] as $id)
    {
      $feedback=$cn->execute('retrieve',array($id));
      $change_row=Database::num_row($feedback);
      if ($change_row  != 0)
	{
	  $row=Database::fetch_array($feedback,0);
	  switch ( $row['jrn_def_type'])
	    {
	    case 'VEN':
	      $cn->execute('update_sold_mer',array($target,$id,$source));
	      $cn->execute('update_sold_cust',array($target,$id,$source));
	      break;
	    case 'ACH':
	      $cn->execute('update_pur_mer',array($target,$id,$source));
	      $cn->execute('update_pur_cust',array($target,$id,$source));
	      break;
	    case 'FIN':
	      $cn->execute('update_fin_bk',array($target,$id,$source));
	      $cn->execute('update_fin_oth',array($target,$id,$source));
	      break;
	      
	    }
	}

      $update=$cn->execute('update_account',array($target,$target_account,$_POST['ctarget'],$id,$_POST['csource']));

      if ( Database::num_row($update) ==0 ) continue;

      if ( $change_row != 0)
	{
	  $count++;

	  echo '<tr>';
	  echo td(format_date($row['jr_date']));
	  echo td(HtmlInput::detail_op($id,$row['jr_internal']));
	  echo td($row['jr_comment']);
	  echo td(nbm($row['jr_montant'],' class="num"'));
	  echo '</tr>';
	}
    }
  echo '</table>';
  echo '<p> Nombre d\'opérations changée :'.$count.'</p>';
}
  /**
   *@brief change the ledger for the selected operation
   *@param $cn database connx
   *@note use the $_POST variables
   * - csource accounting source
   * - ctarget accounting target
   * - jr_id[]
   */
function change_ledger(&$cn)
{
  $msg= check_jrid();
  if( $msg != "")
    {
      echo " <p class=\"error\">$msg</p>"; return;
    }
  /*
   * Type must be the same
   */
  $type_source=$cn->get_value('select jrn_def_type from jrn_def where jrn_def_id=$1',array($_POST['sledger']));
  $type_target=$cn->get_value('select jrn_def_type from jrn_def where jrn_def_id=$1',array($_POST['tledger']));

  if ( $type_target != $type_source)
    {
      echo " <p class=\"error\">Les journaux doivent être de même type</p>"; return;
    }
  $cn->prepare('update_ledger_x','update jrnx set j_jrn_def = $1 where j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and j_jrn_def=$3
		RETURNING j_id ');
  $cn->prepare('update_ledger','update jrn set jr_def_id = $1 where jr_id = $2 and  jr_def_id=$3
		RETURNING jr_id ');

  $cn->prepare('retrieve','select jr_date,jr_comment,jr_montant,jr_internal from jrn where jr_id=$1');
  echo h2info('Opération changée');
  echo 'compte : '.$_REQUEST['sposte'].' vers '.$_REQUEST['tposte'];
  $count=0;
  echo '<table class="result">';
  foreach ($_POST['jr_id'] as $id)
    {
      $update=$cn->execute('update_ledger_x',array(trim($_POST['tledger']),$id,trim($_POST['sledger'])));
      $cn->execute('update_ledger',array(trim($_POST['tledger']),$id,trim($_POST['sledger'])));

      if ( Database::num_row($update) ==0 ) continue;
      $feedback=$cn->execute('retrieve',array($id));
      if ( Database::num_row($feedback) != 0)
	{
	  $count++;
	  $row=Database::fetch_array($feedback,0);
	  echo '<tr>';
	  echo td(format_date($row['jr_date']));
	  echo td(HtmlInput::detail_op($id,$row['jr_internal']));
	  echo td($row['jr_comment']);
	  echo td(nbm($row['jr_montant'],' class="num"'));
	  echo '</tr>';
	}
    }
  echo '</table>';
  echo '<p> Nombre d\'opérations changée'.$count.'</p>';

}
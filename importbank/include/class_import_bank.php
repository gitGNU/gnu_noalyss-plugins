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
 * \brief Manage import
 */
require_once('class_format_bank_sql.php');
require_once('class_acc_ledger_fin.php');
require_once('class_periode.php');
require_once('class_temp_bank_sql.php');

class Import_Bank
{
  /**
   *@brief for the form we have here all the hidden variables
   *@return html string with the hidden dossier, plugin_code,action(sa)
   */
  static function hidden()
  {
    $r=HtmlInput::extension().Dossier::hidden();
    return $r;
  }
  /**
   *@brief check that there is no duplicate among header and we have at least the date
   * and the amount
   *@param array of header
   *@return empty string if valid, otherwise error message
   */
  static function is_valid_header($array)
  {
    global $aheader;
    $check=$aheader;
    $error='';$amount=$date=false;
    for ($i = 0; $i<count($array);$i++)
      {
	$idx=$array[$i];

	if ( $idx == -1) continue;

	if ( $idx == 0 ) $date=true;
	if ($idx== 1 ) $amount=true;
	if ( isset(	$check[$idx+1]['count']))
	  {
	    $check[$idx+1]['count']++;
	  }
	else
	  {
	    $check[$idx+1]['count']=1;
	  }
      }
    if ( ! $date )
      $error.="Il manque la colonne pour les  dates \n";
    if (! $amount )
      $error.=" Il manque la colonne pour les  montants \n";

    foreach ($check as $row)
      {
	if ( $row['value'] == -1 ) continue;
	if (isset ($row['count']) && $row['count'] > 1 )
	  $error.=$row['label']." a été donné ".$row['count']." fois\n" ;
      }
    return $error;
  }
  /**
   *@brief show the different import
   */
  static function show_import()
  {
    global $cn;

    $ret=$cn->exec_sql('select a.id,to_char(i_date,\'DD.MM.YYYY HH24:MI\') as str_date,format_name,i_date
				from importbank.import as a
				join importbank.format_bank as b on (format_bank_id=b.id)
				order by i_date desc');
    $link='?'.Dossier::get().'&plugin_code='.$_REQUEST['plugin_code'].
			'&sb=list&sa='.$_REQUEST['sa']."&ac=".$_REQUEST['ac'];

    $status=$cn->prepare('status','select count(*) from importbank.temp_bank where import_id=$1  and status=$2');

    require_once('template/show_import.php');
  }
  /**
   *Delete all the selected import
   */
  static function delete ($p_array)
  {
    global $cn;

    $a=$p_array['s_del'];
    for ($i=0;$i<count($a);$i++)
      {
	$cn->exec_sql('delete from importbank.import where id=$1',array($a[$i]));
      }
  }
  /**
   *Show detail
   */
  static function list_record($p_id)
  {
    global $cn;
    $filter=new ISelect('fil_status');
    $filter->value=array(
			 array('value'=>0,'label'=>'Tous'),
			 array('value'=>1,'label'=>'Nouveau'),
			 array('value'=>2,'label'=>'Transfèré'),
			 array('value'=>3,'label'=>'Attente'),
			 array('value'=>4,'label'=>'Erreur'),
			 array('value'=>5,'label'=>'Effacer')
			 );
    $filter->javascript=' onchange="submit(this)"';

    $filter->selected=HtmlInput::default_value('fil_status',0,$_GET);
    $sql_filter='';
    switch( $filter->selected)
      {
      case 1:
	$sql_filter= " and status='N' ";
	break;
      case 2:
	$sql_filter=" and status='T'";
	break;
      case 3:
	$sql_filter=" and status='W'";
	break;
      case 4:
	$sql_filter=" and status='E'";
	break;
      case 5:
	$sql_filter=" and status='D'";
	break;



      }
    $array=$cn->get_array('select a.id as id,to_char(i_date,\'DD.MM.YYYY HH24:MI\') as i_date,format_name,jrn_def_id
				from importbank.import as a
				join importbank.format_bank as b on (format_bank_id=b.id)
			    where a.id=$1',array($p_id));
    echo h1($array[0]['id']." ".$array[0]['i_date']." ".$array[0]['format_name'],'');
    $ret=$cn->exec_sql(" SELECT id ,ref_operation,tp_date, amount,
				case when status='N' then 'Nouveau'
				when status='T' then 'Transfèré'
				when status='W' then 'Attente'
				when status='E' then 'ERREUR'
				when status='D' then 'Effacer'

				end as f_status,
				status,
				libelle,
		       		tp_third, tp_extra
			  FROM importbank.temp_bank
			  where import_id=$1 $sql_filter
			  order by tp_date,ref_operation,amount",array($p_id));
	$jrn_name=$cn->get_value('select jrn_def_name from jrn_def where  jrn_def_id=$1',array($array[0]['jrn_def_id']));
	$jrn_account=$cn->get_value ("select ad_value from fiche_detail
						where ad_id=1 and f_id=(select jrn_Def_bank from jrn_def where jrn_def_id=$1) "
			,array($array[0]['jrn_def_id']));
    require_once('template/show_list.php');


  }
  /**
   * return the HTML style for the status
   * White : new
   * green : transfered
   * red  : error
   */
  static function color_status($id)
  {
    $style="";
    switch($id)
      {
      case 'E':
	$style="background-color:red;color:white";
	break;
      case 'T':
	$style="background-color:darkgreen;color:white";
	break;
      case 'D':
	$style="background-color:grey;color:red";
	break;
      case 'N':
	$style="background-color:white;color:blue";
	break;
      default:
	return "";
      }
    $style='style="'.$style.'"';
    return $style;
  }
  /**
   *@brief delete the record marked as deleted
   *@param $p_array  is normally the request
   */
  static function delete_record($p_array)
  {
    global $cn;
    $cn->exec_sql('delete from importbank.temp_bank where import_id=$1
		   and status=\'D\'',array($p_array['id']));
  }
  /**
   *@brief import row marked to transfer and from the specific import to
   * the database
   *@param $p_array
   */
  static function transfer_record($p_array)
  {
    global $cn;

    try
    {
        $cn->start();
	/*
	 * retrieve banque account, ledger, bank quick code
	 */
	$led_id=$cn->get_value('select jrn_def_id
				from importbank.format_bank as fb
				    join importbank.import as imp on (format_bank_id = fb.id)
				  where imp.id=$1',array($p_array['id']));

	$fin_ledger=new Acc_Ledger_Fin($cn,$led_id);
	$card_bank=$fin_ledger->get_bank();
	$quickcode_bank=$cn->get_value('select ad_value from fiche_detail where f_id=$1 and ad_id=$2',
				     array($card_bank,ATTR_DEF_QUICKCODE));
	$account_bank=$cn->get_value('select ad_value from fiche_detail where f_id=$1 and ad_id=$2',
				     array($card_bank,ATTR_DEF_ACCOUNT));
	$bank_name=$fin_ledger->get_name();
	/*
	 * record each row
	 */
	$sql = "select id from importbank.temp_bank where import_id=$1 and status='W'";

        $ResAll=$cn->exec_sql($sql,array($p_array['id']));
        $Max=Database::num_row($ResAll);

        for ($i = 0;$i < $Max;$i++)
        {
            $val=Database::fetch_array($ResAll,$i);

	    $row=new Temp_Bank_Sql($cn,$val['id']);

	    if ( $row->f_id == null || $row->f_id=='')
	      {
		// error
		self::transfert_error($row->id,'Aucune fiche donnée');
		continue;
	      }

            // Retrieve the account thx the quick code
            $f=new Fiche($cn,$row->f_id);
            $poste_comptable=$f->strAttribut(ATTR_DEF_ACCOUNT);
	    $quick_code=$f->strAttribut(ATTR_DEF_QUICKCODE);

            // Vérification que le poste comptable trouvé existe
            if ( $poste_comptable == NOTFOUND || strlen(trim($poste_comptable))==0)
	      {
		// error
		self::transfert_error($row->id,'Poste comptable de la  fiche est incorrecte');
		continue;
	      }
	    if ( self::check_date ($row->tp_date) == false)
	      {
		// error
		self::transfert_error($row->id,'Date hors des limites');
		continue;
	      }
		    $err=self::is_closed($row->tp_date,$led_id);
	    if ( $err != '')
	      {
		self::transfert_error($row->id,$err.' - Date hors des journaux');
		continue;
	      }

            // Finances

            $seq=$cn->get_next_seq('s_grpt');
            $p_user = $_SESSION['g_user'];

            $acc_op=new Acc_Operation($cn);
            $acc_op->amount=$row->amount;
	    $acc_op->desc="";
            $acc_op->type="d";
            $acc_op->date=$row->tp_date;
            $acc_op->user=$p_user;
            $acc_op->poste=$account_bank;
            $acc_op->grpt=$seq;
            $acc_op->jrn=$led_id;
            $acc_op->periode=0;
	    $acc_op->f_id=$card_bank;
            $acc_op->qcode=$quickcode_bank;
            $acc_op->mt=microtime(true);
            $r=$acc_op->insert_jrnx();


            $acc_op->type="c";
            $acc_op->poste="";
            $acc_op->desc=$row->tp_third." ".$row->libelle." ".$row->tp_extra;
            $acc_op->amount=$row->amount;
	    $acc_op->f_id=$row->f_id;
            $acc_op->qcode=$quick_code;
            $r=$acc_op->insert_jrnx();

            $acc_op->desc=$row->tp_third." ".$row->libelle." ".$row->tp_extra;
            $jr_id=$acc_op->insert_jrn();

            $internal=$fin_ledger->compute_internal_code($seq);

            $Res=$cn->exec_sql("update jrn set jr_internal=$1 where jr_id = $2",array($internal,$jr_id));

	    $fin_ledger->insert_quant_fin($card_bank,$jr_id,$row->f_id,$row->amount);

            // insert rapt
	    if ( $cn->get_value ('select count(jr_id) from jrn where jr_id=$1',array($row->tp_rec)) == 1)
	      {
			$acc_reconc=new Acc_Reconciliation($cn);
			$acc_reconc->set_jr_id($jr_id);
			$acc_reconc->insert($row->tp_rec);
			// lettering
			$a_rec=$cn->get_array('select j_id
										from jrnx join jrn on (j_grpt=jr_grpt_id)
									  where
									  f_id=$1 and jr_id=$2',array($row->f_id,$row->tp_rec));
			if ( count($a_rec) == 1 )
			{
				$a_target=$cn->get_array('select j_id
										from jrnx join jrn on (j_grpt=jr_grpt_id)
									  where
									  f_id=$1 and jr_id=$2',array($row->f_id,$jr_id));
				if ( count($a_target)==1)
				{
					$lc=new Lettering_Card($cn);
					$lc->insert_couple($a_rec[0]['j_id'],$a_target[0]['j_id']);
				}
			}

	      }

            $sql2 = "update importbank.temp_bank set status = 'T',tp_error_msg=null  where id=$1";
            $Res2=$cn->exec_sql($sql2,array($row->id));
        }
    }
    catch (Exception $e)
    {
        $cn->rollback();
        echo '<span class="error">'.
        'Erreur dans '.__FILE__.':'.__LINE__.
        ' Message = '.$e->getMessage().
        '</span>';
    }

    $cn->commit();
  }
  /**
   *Update the row with an error message, and change is status to E
   */
  static function transfert_error($id,$message)
  {
    global $cn;
    $cn->exec_sql('update importbank.temp_bank set status=$1,tp_error_msg=$2 where id=$3',
		  array('E',$message,$id));
  }
  /**
   * check
   * if the date is outside the defined periode
   */
  static function check_date($p_date)
  {
    global $cn;
    $sql="select count(*) from parm_periode where p_start <= to_date($1,'DD.MM.YYYY') and p_end >= to_date($1,'DD.MM.YYYY') ";
    $res=$cn->get_value($sql,array($p_date));
    if ( $res == 0) return false;
    return true;
  }
  /**
   * Check if the date is in a periode and if the ledger
   * is closed or not
   */
  static function is_closed($p_date,$ledger_id)
  {
    global $cn;
    try
      {
	$periode=new Periode($cn);
	$per=$periode->find_periode($p_date);
	$periode->set_jrn($ledger_id);
	$periode->set_periode($per);
	if ( $periode->is_closed() == 1 )
	  return "Période fermée";
	return "";
      }
    catch (Exception $e)
      {
	$err=$e->getMessage();
	return $err;
      }

  }
}
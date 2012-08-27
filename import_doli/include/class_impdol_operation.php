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

/**
 * @file
 * @brief
 *
 */
require_once 'class_sql_impdol.php';

class Impdol_Operation
{

	function save_file()
	{
		if (trim($_FILES['csv_operation']['name']) == '')
		{
			alert('Pas de fichier donné');
			return -1;
		}
		$this->filename = tempnam($_ENV['TMP'], 'upload_');
		move_uploaded_file($_FILES["csv_operation"]["tmp_name"], $this->filename);

		$imp = new Impdol_Import_sql();
		$imp->setp('temp_file', $this->filename);
		$imp->setp('send_file', $_FILES['csv_operation']['name']);
		$imp->insert();
		$this->impid = $imp->getp("id");
	}

	function record()
	{
		$foperation = fopen($this->filename, 'r');
		$this->row_count = 0;
		$max = 0;
		while (($row = fgetcsv($foperation, 0, ";", '"')) !== false)
		{
			if (count($row) != 11)
			{
				$str_row = implode($row, ";");
				echo "Attention " . h($str_row) . " ne contient pas 11 colonnes";
				continue;
			}
			$r = new impdol_Operation_tmp_Sql();
			$r->setp('dolibarr', $row[0]);
			$r->setp('date', $row[1]);
			$r->setp('type', $row[2]);
			$r->setp('qcode', $row[3]);
			$r->setp('desc', $row[4]);
			$r->setp('pj', $row[5]);
			$r->setp('amount_unit', $row[6]);
			$r->setp('amount_vat', $row[7]);
			$r->setp('number_unit', $row[8]);
			$r->setp('rate', $row[9]);
			$r->setp('amount_total', $row[10]);
			$r->setp("import_id", $this->impid);
			$r->insert();
			$this->row_count++;
		}
		echo "Nombre de lignes enregistrées : " . $this->row_count;
		$import = new impdol_import_sql($this->impid);
		$import->setp("nbrow", $this->row_count);
		$import->update();
	}

	/**
	 * Check data contained into impdol.operation_tmp. Update the column o_result (T = ok N no ok)
	 * Put in o_message the reason of the problem
	 */
	function check()
	{
		global $cn;
		try
		{
			$cn->start();
			$array = $cn->get_array("select o_id from impdol.operation_tmp where i_id=$1 order by o_id", array($this->impid));
			$nb_row = count($array);
			for ($i = 0; $i < $nb_row; $i++)
			{
				$msg = "";
				$operation = new impdol_operation_tmp_sql();
				$operation->setp("id", $array[$i]['o_id']);
				$operation->load();
				$operation->setp("code", 'T');
				$dol = $operation->getp("dolibarr");
				/*
				 * detect duplicate
				 */
				$db = $cn->get_value("select count(*) from impdol.operation_tmp where o_doli=$1 and o_id in (select o_id from impdol.operation_transfer)", array($dol));
				if ($db > 0)
				{
					$operation->setp("code", "N");
					$operation->setp("message", " Opération déjà transférée : doublon ");
					$operation->update();
					continue;
				}
				if (trim($dol) == "" || isNumber($dol) == 0)
				{
					$operation->setp("code", 'N');
					$msg.=" le numéro de ligne pour dolibarr est invalide";
				}
				if (isDate($operation->getp("date")) == null)
				{
					$operation->setp("code", 'N');
					$msg.=" La date est invalide, format n'est pas JJ.MM.AAAA";
				}
				$fiche = new Fiche($cn);
				$fiche->get_by_qcode(trim($operation->getp("qcode")));

				if ($fiche->id == 0)
				{
					$operation->setp("code", 'N');
					$msg.=" Cette fiche n'existe pas";
				}
				else
				{
					$operation->setp("fiche", $fiche->id);
				}
				/**
				 * check if card as a valid accounting
				 */
				$poste = $fiche->strAttribut(ATTR_DEF_ACCOUNT);
				if (trim($poste) == '' || $cn->get_value("select count(*) from tmp_pcmn where pcm_val=$1", array($poste)) == 0)
				{
					$operation->setp("code", 'N');
					$msg.=" Cette fiche n'a pas de poste comptable valide";
				}
				$operation->setp("poste", $poste);
				$a = array("rate" => " Taux de TVA", "amount_total" => "Montant total", "number_unit" => 'Nombre d\'unité', "amount_vat" => "Montant TVA");

				foreach ($a as $key => $value)
				{
					$v = $operation->getp($key);
					$v = str_replace(",", ".", $v);

					if (trim($v) != "" && isNumber($v) == 0)
					{
						$operation->setp("code", 'N');
						$msg.=" $value n'est pas un nombre";
						continue;
					}

					$operation->setp($key, $v);
				}

				if ($operation->getp("type") != "T")
				{
					$tva_id = $cn->get_array("select tva_id from impdol.parameter_tva where pt_rate/100=$1", array($operation->getp("rate")));
					if (count($tva_id) > 1)
					{
						$operation->setp("code", 'N');
						$msg.=" Plusieurs code TVA correspondent à ce taux";
					}
					elseif (empty($tva_id))
					{
						$operation->setp("code", 'N');
						$msg.=" Aucun code TVA ne correspond à ce taux";
					}
					else
					{
						$operation->setp("tva_id", $tva_id[0]['tva_id']);
					}
				}
				// a supplier and one service at least is needed
				$code_op = $operation->getp("dolibarr");
				$nb_customer = $cn->get_value("select count(*) from impdol.operation_tmp where o_type='T' and o_doli=$1 and i_id=$2", array($code_op, $this->impid));
				$nb_good = $cn->get_value("select count(*) from impdol.operation_tmp where o_type='S' and o_doli=$1 and i_id=$2", array($code_op, $this->impid));
				if ($nb_customer == 0)
				{
					$operation->setp("code", 'N');
					$msg.=" Aucun client ou fournisseur";
				}
				if ($nb_good == 0)
				{
					$operation->setp("code", 'N');
					$msg.=" Aucune marchandise ou service";
				}

				// check if in a opened period
				$op_date = $operation->getp('date');
				$periode = new Periode($cn);
				try
				{
					$periode->find_periode($op_date);
				}
				catch (Exception $e)
				{
					$msg.=$e->getMessage();
					$operation->setp('code', 'N');
				}

				$operation->setp("message", $msg);
				$operation->update();
			}
			/*
			 *  If a part is N then the whole operation is N
			 */
			$sql = "update impdol.operation_tmp  set o_result='N' where i_id=$1 and
				o_doli in (select o_doli from impdol.operation_tmp  where o_result='N' and i_id=$1)";
			$cn->exec_sql($sql, array($this->impid));
			$cn->commit();
		}
		catch (Exception $e)
		{
			print_r($e->getTraceAsString());
			$cn->rollback;
		}
	}

	/**
	 * Show the result in a table
	 */
	function result()
	{
		require_once 'class_html_table.php';
		global $cn, $g_failed, $g_succeed;
		$sql = " select o_doli,o_date,o_qcode,o_label,o_pj,amount_unit,
			amount_vat,
			number_unit,
			vat_rate,
			amount_total,
			case when o_result='T' then '" . $g_succeed . "' else '" . $g_failed . "' end as result,
			o_message
			from impdol.operation_tmp where i_id=" . $this->impid . " order by o_id";
		echo Html_Table::sql2table($cn, array(
			array('name' => 'n° ligne',
				'style' => 'style="text-align:right"'),
			array('name' => 'Date',
				'style' => 'text-align:right'),
			array('name' => 'QuickCode'),
			array('name' => 'Libellé'),
			array('name' => 'n° pj'),
			array('name' => 'Montant / unité', 'style' => 'style="text-align:right"'),
			array('name' => 'Montant Total TVA', 'style' => 'style="text-align:right"'),
			array('name' => 'Nbre unités', 'style' => 'style="text-align:right"'),
			array('name' => 'taux TVA', 'style' => 'style="text-align:right"'),
			array('name' => 'Montant total TVAC', 'style' => 'style="text-align:right"'),
			array('name' => 'Transfert', 'style' => 'style="text-align:right"', 'raw' => 1),
			array('name' => 'Message')
				)
				, $sql, 'style="width:100%" class="result"'
		);
	}

	function transfer()
	{
		global $cn;
		$jrn = $_POST['p_jrn'];
		$ledger = new Acc_Ledger($cn, $jrn);
		$type = $ledger->get_type();
		switch ($type)
		{
			case 'ACH':
				$ledger = new Acc_Ledger_Purchase($cn, $jrn);
				$tiers_side = 'c';
				$oth_side = 'd';
				break;
			case 'VEN':
				$ledger = new Acc_Ledger_Sold($cn, $jrn);
				$tiers_side = 'd';
				$oth_side = 'c';
				break;
			default:
				die('Erreur ce type journal n\' est pas encore supporté');
		}
		/**
		 * Loop in table operation_tmp, get all the record to transfer
		 */
		$array = $cn->get_array("select
					distinct o_doli
				from impdol.operation_tmp
				where i_id=$1 and o_result='T'
				order by o_doli  asc", array($this->impid));
		$nb_row = count($array);
		bcscale(2);
		try
		{
			$cn->start();
			for ($i = 0; $i < $nb_row; $i++)
			{
				/*
				 * For each operation (same o_doli code)
				 */
				$adetail = $cn->get_array("select o_id from impdol.operation_tmp where o_doli=$1 and i_id=$2 and o_type='S'", array($array[$i]['o_doli'], $this->impid));
				$atiers = $cn->get_array("select o_id from impdol.operation_tmp where o_doli=$1 and i_id=$2  and o_type='T'", array($array[$i]['o_doli'], $this->impid));
				if (count($atiers) > 1)
				{
					echo "Plusieurs clients pour l' opération, code " . $array[$i]['o_doli'];
					continue;
				}
				if (count($atiers) == 0)
				{
					echo "Pas de client pour une opération, code " . $array[$i]['o_doli'];
					continue;
				}

				$oper_tiers = new Impdol_Operation_Tmp_Sql($atiers[0]['o_id']);
				$nb_detail = count($adetail);
				$sum = 0;
				$grpt = $cn->get_value("select nextval('s_grpt');");
				$internal = $ledger->compute_internal_code($grpt);

				$tva = array();
				/* record all S record */
				for ($e = 0; $e < $nb_detail; $e++)
				{
					/* Record service */
					$oper = new Impdol_Operation_Tmp_Sql($adetail[$e]['o_id']);
					$oper->from_array($array[$i]);
					$date = format_date($oper->getp("date"), "YYYY-MM-DD", "DD.MM.YYYY");
					$oper->setp("date", $date);
					$jrnx = new Acc_Operation($cn);
					$jrnx->date = $date;
					$amount_tva = $oper->getp("amount_vat");
					$amount_tvac = $oper->getp("amount_total");
					$jrnx->amount = bcsub($amount_tvac, $amount_tva);
					$jrnx->poste = $oper->getp('poste');
					$jrnx->grpt = $grpt;
					$jrnx->type = $oth_side;
					$jrnx->jrn = $jrn;
					$jrnx->user = $_SESSION['g_user'];
					$jrnx->periode = 0;
					$jrnx->qcode = $oper->getp("qcode");
					$jrnx->desc = mb_substr($oper->getp("desc"),0,80,'UTF8');
					$id = $jrnx->insert_jrnx();

					$transfer = new impdol_operation_transfer_sql();
					$transfer->setp("j_id", $id);
					$transfer->setp("o_id", $oper->getp("id"));
					$transfer->insert();

					$tva_id = $oper->getp("tva_id");

					/*
					 * Save into quant_purchase or quant_sale
					 */
					switch ($type)
					{
						case 'ACH':
							$sql = "insert into quant_purchase(qp_internal,j_id,qp_fiche,qp_quantite,qp_price,qp_vat,qp_vat_code,qp_supplier)
							values($1,$2,$3,$4,$5,$6,$7,$8)";
							$cn->exec_sql($sql, array(null, $id, $oper->getp("fiche"), $oper->getp("number_unit"), $jrnx->amount, $amount_tva, $tva_id, $oper_tiers->getp("fiche")));
							break;
						case 'VEN':
							$cn->exec_sql("insert into quant_sold
                                        (qs_internal,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client,j_id,qs_vat_sided,qs_valid)
                                        values
                                        ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)", array(null, /* 1 qs_internal */
								$oper->getp("fiche"), /* 2 qs_fiche */
								$oper->getp("number_unit"), /* 3 qs_quantite */
								$jrnx->amount, /* 4 qs_price */
								$amount_tva, /* 5 qs_vat */
								$tva_id, /* 6 qs_vat_code */
								$oper_tiers->getp('fiche'), /* 7 qs_client */
								$id, /* 8 j_id */
								0, /* 9 qs_vat_sided */
								'Y' /* 10 qs_valid */
							));

							break;
					}
					/* save VAT into an array */
					if (isset($tva[$tva_id]))
					{
						$tva[$tva_id] = bcadd($tva[$tva_id], $amount_tva);
					}
					else
					{
						$tva[$tva_id] = $amount_tva;
					}
					$sum = bcadd($sum, $amount_tvac);
				}  // loop e
				// Record the tiers

				$jtiers = new Acc_Operation($cn);
				$jtiers->date = $date;
				$jtiers->amount = $sum;
				$jtiers->poste = $oper_tiers->getp('poste');
				$jtiers->grpt = $grpt;
				$jtiers->type = $tiers_side;
				$jtiers->jrn = $jrn;
				$jtiers->user = $_SESSION['g_user'];
				$jtiers->periode = 0;
				$jtiers->qcode = $oper_tiers->getp("qcode");
				$jtiers->desc = mb_substr($oper_tiers->getp("desc"),0,80,'UTF8');
				$jtiers->insert_jrnx();

				/* Record the vat 1 */
				foreach ($tva as $key => $value)
				{
					$tva = new Acc_TVA($cn, $key);
					$tva->load();
					$poste = $tva->get_side($oth_side);
					$op_tva = new Acc_Operation($cn);
					$op_tva->date = $date;
					$op_tva->amount = $value;
					$op_tva->poste = $poste;
					$op_tva->grpt = $grpt;
					$op_tva->type = $oth_side;
					$op_tva->jrn = $jrn;
					$op_tva->user = $_SESSION['g_user'];
					$op_tva->periode = 0;
					$op_tva->qcode = null;
					$op_tva->desc = $tva->tva_label;
					$op_tva->insert_jrnx();
				}

				/* record into jrn */
				$acc_jrn = new Acc_Operation($cn);
				$acc_jrn->jrn = $jrn;
				$acc_jrn->amount = $sum;
				$acc_jrn->desc = mb_substr($oper_tiers->getp("desc"),0,80,'UTF8');
				$acc_jrn->date = $date;
				$acc_jrn->grpt = $grpt;
				$acc_jrn->periode = 0;
				$acc_jrn->insert_jrn();
				$cn->exec_sql('update jrn set jr_pj_number=$1 where jr_id=$2',array($oper->getp('pj'),$acc_jrn->jr_id));

				/* Update info */
				$ledger->grpt_id = $grpt;
				$ledger->update_internal_code($internal);
				/*
				 * Update internal code in quant_*
				 */
				switch ($type)
				{
					case 'ACH':
						$cn->exec_sql('update quant_purchase set qp_internal = $1 where j_id in (select j_id from jrnx where j_grpt=$2)', array($internal, $grpt));
						break;
					case 'VEN':
						$cn->exec_sql('update quant_sold set qs_internal = $1 where j_id in (select j_id from jrnx where j_grpt=$2)', array($internal, $grpt));
						break;
				}
			}// loop i
			$cn->commit();
		}
		catch (Exception $e)
		{
			print_r($e->getTraceAsString());
			$cn->rollback();
		}
	}

	function result_transfer()
	{
		require_once 'class_html_table.php';
		global $cn, $g_failed, $g_succeed;
		$sql = " select distinct jr_id, jr_pj,jr_date, jr_comment,jr_internal

		from jrn
		where jr_grpt_id in (
		select j_grpt
		from impdol.operation_tmp as otmp join impdol.operation_transfer as ot on (ot.o_id = otmp.o_id) join jrnx on (jrnx.j_id = ot.j_id)
		and i_id=$1  ) order by jr_date ";
		$arow=$cn->get_array($sql,array($this->impid));
		echo h2("Opérations sauvées",'info');
		echo '<table class="result">';
		echo '<tr>';
		echo th("Date");
		echo th("Libellé");
		echo th("Pièce");
		echo th("N° opération");
		echo '</tr>';
		for ($i=0;$i<count($arow);$i++)
		{
			echo '<tr>';
			echo td($arow[$i]['jr_date']);
			echo td($arow[$i]['jr_comment']);
			echo td($arow[$i]['jr_pj']);
			echo '<td>'.HtmlInput::detail_op($arow[$i]['jr_id'],$arow[$i]['jr_internal']).'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

}

?>

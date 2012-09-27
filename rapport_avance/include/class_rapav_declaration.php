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
 * @brief compute all the formulaire_row and save them into rappport_advanced.declaration
 *
 */
require_once 'class_rapav_formulaire.php';
require_once 'class_formulaire_param_detail.php';
require_once 'class_rapport_avance_sql.php';
/**
 * @brief compute, save and display a declaration
 */
class Rapav_Declaration extends RAPAV_Declaration_SQL
{

	function __construct()
	{
		$this->form=new RAPAV_Formulaire();
		parent::__construct();
	}
	function compute($p_id,$p_start, $p_end)
	{
		global $cn;
		$cn->start();
		// Load the parameter from formulaire_param_detail
		// create object_rapav_declaration
		//   compute
		// save the parameter
		$this->form->f_id=$p_id;
		$this->form->load();
		$this->d_title=$this->form->f_title;
		$this->d_start=$p_start;
		$this->d_end=$p_end;
		$this->to_keep='N';
		$this->insert();
		/*
		 * First we compute the formula and tva_code
		 */
		$array = $cn->get_array("select fp.p_id,p_code,p_libelle,p_type,p_order,f_id,p_info,t_id
			from rapport_advanced.formulaire_param as fp
			 join rapport_advanced.formulaire_param_detail as fpd on (fp.p_id=fpd.p_id)
			where
			f_id=$1
			and type_detail in (1,2)",
				array($p_id));
		for ($i = 0; $i < count($array); $i++)
		{
			$row = new Rapav_Declaration_Param();
			$row->d_id=$this->d_id;
			$row->dr_id=$cn->get_next_seq('rapport_advanced.declaration_param_seq');
			$row->from_array($array[$i]);
			$row->compute($p_start, $p_end);
			$row->insert();
		}
		/*
		 * Secundo we compute the compute code
		 */
		$array = $cn->get_array("select fp.p_id,p_code,p_libelle,p_type,p_order,f_id,p_info,t_id
			from rapport_advanced.formulaire_param as fp
			 join rapport_advanced.formulaire_param_detail as fpd on (fp.p_id=fpd.p_id)
			where
			f_id=$1
			and type_detail =3
			order by p_order",
				array($p_id));
		/**
		 * @note order is important !!!
		 *
		 */
		for ($e = 0; $e < count($array); $e++)
		{
			$row = new Rapav_Declaration_Param();
			$row->d_id=$this->d_id;
			$row->dr_id=$cn->get_next_seq('rapport_advanced.declaration_param_seq');
			$row->from_array($array[$e]);
			$row->compute($p_start, $p_end);
			$row->insert();
		}
		/**
		 * Add the lines without definition
		 */
		$array = $cn->get_array("select fp.p_id,p_code,p_libelle,p_type,p_order,f_id,p_info,t_id
			from rapport_advanced.formulaire_param as fp
			left join rapport_advanced.formulaire_param_detail as fpd on (fp.p_id=fpd.p_id)
			where
			f_id=$1
			and type_detail is null",
				array($p_id));
		for ($i = 0; $i < count($array); $i++)
		{
			$row = new Rapav_Declaration_Param();
			$row->d_id=$this->d_id;
			$row->dr_id=$cn->get_next_seq('rapport_advanced.declaration_param_seq');
			$row->from_array($array[$i]);
			$row->amount=0;
			$row->insert();
		}

		$cn->commit();
	}

}

class Rapav_Declaration_Param
{
	function insert()
	{
		$data=new RAPAV_Declaration_Row_SQL();
		$data->dr_code=$this->param->p_code;
		$data->dr_libelle=$this->param->p_libelle;
		$data->dr_order=$this->param->p_order;
		$data->dr_amount=$this->amount;
		$data->d_id=$this->d_id;
		$data->dr_id=$this->dr_id;
		$data->insert();
	}
	function from_array($p_array)
	{
		$this->param = new Formulaire_Param();
		foreach (array('p_id', 'p_code', 'p_libelle', 'p_type', 'p_order', 'f_id', 'p_info', 't_id') as $e)
		{
			$this->param->$e = $p_array[$e];
		}
		$this->param->load();
	}

	/**
	 * @brief compute the date following the attribute t_id (match rapport_advanced.periode_type and
	 * store the result into $this->start and $this-> end
	 *   - 1 date from the FORM
	 *   - 2 N
	 *   - 3 N-1
	 *   - 4 N-2
	 *   - 5 N-3
	 */
	function compute_date($p_start, $p_end)
	{
		global $g_user;
		switch ($this->param->t_id)
		{
			case 1:
				$this->start = $p_start;
				$this->end = $p_end;
				return;
				break;
			case 2:
				list($this->start, $this->end) = $g_user->get_limit_current_exercice();
				return;
				break;
			case 3:
				$exercice = $g_user->get_exercice();
				$exercice--;
				break;
			case 4:
				$exercice = $g_user->get_exercice();
				$exercice-=2;
				break;
			case 5:
				$exercice = $g_user->get_exercice();
				$exercice-=3;
				break;
			default:
				throw new Exception('compute_date : t_id est incorrect');
		}
		global $cn;

		// If exercice does not exist then
		// set the date end and start to 01.01.1900

		$exist_exercice = $cn->get_value('select count(p_id) from parm_periode where p_exercice=$1', array($exercice));
		if ($exist_exercice == 0)
		{
			$this->start = '01.01.1900';
			$this->end = '01.01.1900';
			return;
		}
		// Retrieve start & end date
		$periode = new Periode($cn);
		list($per_start, $per_end) = $periode->get_limit($exercice);
		$this->start = $per_start->first_day();
		$this->end = $per_end->last_day();
	}

	function compute($p_start, $p_end)
	{
		global $cn;
		bcscale(2);
		$this->amount=0;
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Compute first the formula and the account_tva
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$array = $cn->get_array("select fp_id,p_id,tmp_val,tva_id,fp_formula,fp_signed,jrn_def_type,tt_id,type_detail
			from rapport_advanced.formulaire_param_detail where p_id=$1", array($this->param->p_id));
		$this->compute_date($p_start, $p_end);
		for ($e = 0; $e < count($array); $e++)
		{
			$row_detail = Rapav_Declaration_Detail::factory($array[$e]);
			$row_detail->dr_id=$this->dr_id;
			$tmp_amount=$row_detail->compute($this->start, $this->end);
			$this->amount=bcadd($tmp_amount,$this->amount);
			$row_detail->insert();
		}

	}

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Compute the detail for each row
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class Rapav_Declaration_Detail extends RAPAV_Declaration_Row_Detail_SQL
{
	/**
	 * @brief create an object RAPAV_dd_Formula, RAPAV_dd_Account_Tva or Rapav_dd_compute following the idx type_detail
	 * @param type $p_array
	 * @return object RAPAV_dd_Formula, RAPAV_dd_Account_Tva or Rapav_dd_compute
	 * @throws if the type is not known
	 */
	static function factory($p_array)
	{
		switch ($p_array['type_detail'])
		{
			case '1':
				$ret = new Rapav_dd_Formula();
				break;
			case '2':
				$ret = new Rapav_dd_Account_Tva();
				break;
			case '3':
				$ret = new Rapav_dd_Compute();
				break;
			default:
				throw new Exception("Type inconnu");
		}

		$ret->from_array($p_array);
		$ret->ddr_amount=0;
		return $ret;
	}
	/**
	 * @brief the p_array contains a row from formulaire_param_detail
	 * it will be copied into this->form.
	 * @param type $p_array match formulaire_param_detail table structure
	 */
	function from_array($p_array)
	{
		$this->form = new Formulaire_Param_Detail();
		$attribute = explode(',', 'fp_id,p_id,tmp_val,tva_id,fp_formula,fp_signed,jrn_def_type,tt_id,type_detail');
		foreach ($attribute as $e)
		{
			$this->form->$e = $p_array[$e];
		}
	}

}

/**
 * @brief compute a formula
 * @see Impress::parse_formula RAPAV_Formula
 */
class Rapav_dd_Formula extends Rapav_Declaration_Detail
{

	function compute($p_start, $p_end)
	{
		global $cn;
		$amount = Impress::parse_formula($cn, "", $this->form->fp_formula, $p_start, $p_end, false, 1);
		return $amount;
	}

}
/**
 * @brief handle the param_detail type Compute
 * @see RAPAV_Compute
 */
class Rapav_dd_Compute extends Rapav_Declaration_Detail
{

	function compute($p_start, $p_end)
	{
		return 0;

	}

}

/**
 * @brief handle the param_detail type Account_Tva
 * The t_id gives the type of total
 *   - 0 TVA + Account
 *   - 1 TVA
 *   - 2 Account
 * the jrn_def_type is either ACH or VEN
 *
 * @see RAPAV_Account_Tva
 */
class Rapav_dd_Account_Tva extends Rapav_Declaration_Detail
{

	/**
	 * compute the amount of tva using the given account in either the ledger ACH or VEN
	 * following the $this->form->jrn_def_type.
	 * set the $this->errcode if something wrong has happened
	 * @param  $p_start start date
	 * @param $p_end end date
	 * @return amount
	 */
	private  function compute_tva($p_start, $p_end)
	{
		if ($this->form->jrn_def_type == 'ACH')
		{
			$sql = "select coalesce(sum(qs_vat),0) as amount
						from quant_sold join jrnx using (j_id)
						where qs_vat_code=$1 and
						(j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
						and j_poste::text like ($4)";
			$amount = $this->cn->get_value($sql, array($this->form->tva_id,
														$p_start,
														$p_end,
														$this->form->tmp_val));
			return $amount;
		}
		if ($this->form->jrn_def_type == 'VEN')
		{
			$sql = "select coalesce(sum(qs_vat),0) as amount
						from quant_sold join jrnx using (j_id)
						where qs_vat_code=$1
						and (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
						and j_poste::text like ($4)";
				$amount = $this->cn->get_value($sql, array($this->form->tva_id,
														$p_start,
														$p_end,
														$this->form->tmp_val));
				return $amount;
		}
		$this->errcode = 'Erreur dans le journal';
		return 0;
	}
	/**
	 * compute the amount of account using the given tva_id in either the ledger ACH or VEN
	 * following the $this->form->jrn_def_type.
	 * Set the $this->errcode if something wrong has happened
	 * @param  $p_start start date
	 * @param $p_end end date
	 * @return amount
	 * @param type $p_start
	 * @param type $p_end
	 * @return \amount|int
	 */
	private function compute_amount($p_start, $p_end)
	{
		if ($this->form->jrn_def_type == 'ACH')
		{
			$sql = "select coalesce(sum(qs_price),0) as amount from quant_sold
					join jrnx using (j_id)
					where qs_vat_code=$1 and (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
					and j_poste::text like ($4)";
			$amount = $this->cn->get_value($sql, array($this->form->tva_id,
														$p_start,
														$p_end,
														$this->form->tmp_val));
			return $amount;
		}
		if ($this->form->jrn_def_type == 'VEN')
		{
			$sql = "select coalesce(sum(qp_price),0) as amount from quant_purchase join jrnx using (j_id)
					where qp_vat_code=$1 and (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
					and j_poste::text like ($4)";
			$amount = $this->cn->get_value($sql, array($this->form->tva_id,
				$p_start,
				$p_end,
				$this->form->tmp_val));
			return $amount;
		}
		$this->errcode = 'Erreur dans le journal';
		return 0;
	}
	/**
	 * Compute the amount of TVA or Account, call internally private functions
	 * @see Rapav_dd_Account_Tva::computa_amount Rapav_dd_Account_Tva::compute_tva
	 * @param $p_start start date
	 * @param $p_end end date
	 * @return amount computed
	 * @throws Exception
	 */
	function compute($p_start, $p_end)
	{
		bcscale(2);
		// Retrieve the account for the tva_id, we need the DEB for VEN and CRED for ACH
		//
		// tt_id gives the type of total
		//  - 0 TVA + Account
		//  - 1 TVA
		//  - 2 Account
		switch ($this->form->tt_id)
		{
			case 0:
				$t1_amount = $this->compute_amount($p_start, $p_end);
				$t2_amount = $this->compute_tva($p_start, $p_end);
				$amount = bcadd($t1_amount, $t2_amount);
				break;
			case 1:
				$amount = $this->compute_tva($p_start, $p_end);
				$amount = bcadd($amount, 0);
				break;

			case 2:
				$amount = $this->compute_amount($p_start, $p_end);
				$amount = bcadd($amount, 0);
				break;

			default:
				throw new Exception('Type de total invalide');
				break;
		}
		return $amount;
	}

}
?>

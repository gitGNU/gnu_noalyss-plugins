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
 * @brief handle the data: database level
 *
 */
require_once 'class_phpcompta_sql.php';
class formulaire_param_sql extends phpcompta_sql
{

	function __construct($p_id=-1)
	{


		$this->table = "rapport_advanced.formulaire_param";
		$this->primary_key = "p_id";

		$this->name = array(
			"p_id" => "p_id",
			"p_code" => "p_code",
			"p_libelle" => "p_libelle",
			"p_type" => "p_type",
			"p_order" => "p_order",
			"f_id" => "f_id",
			"p_info" => "p_info",
			"t_id" => "t_id"
		);

		$this->type = array(
			"p_id" => "numeric",
			"p_code" => "text",
			"p_libelle" => "text",
			"p_type" => "numeric",
			"p_order" => "numeric",
			"f_id" => "numeric",
			"p_info" => "text",
			"t_id" => "numeric"
		);

		$this->default = array(
			"p_id" => "auto"
		);
		global $cn;

		parent::__construct($cn, $p_id);
	}

}
class formulaire_sql extends phpcompta_sql
{

	function __construct($p_id=-1)
	{


		$this->table = "rapport_advanced.formulaire";
		$this->primary_key = "f_id";

		$this->name = array(
			"f_id" => "f_id",
			"f_title" => "f_title",
			"f_description" => "f_description",
			"f_lob"=>"f_lob",
			"f_mimetype"=>"f_mimetype",
			"f_filename"=>"f_filename",
			"f_size"=>"f_size"
		);

		$this->type = array(
			"f_id" => "numeric",
			"f_title" => "text",
			"f_description" => "text",
			"f_lob"=>"oid",
			"f_mimetype"=>"text",
			"f_filename"=>"text",
			"f_size"=>"numeric"
		);

		$this->default = array(
			"f_id" => "auto"
		);
		global $cn;

		parent::__construct($cn, $p_id);
	}

}
class Formulaire_Param_Detail_SQL extends phpcompta_sql
{

	function __construct($p_id=-1)
	{


		$this->table = "rapport_advanced.formulaire_param_detail";
		$this->primary_key = "fp_id";

		$this->name = array(
			"fp_id" => "fp_id",
			"p_id" => "p_id",
			"tmp_val" => "tmp_val",
			"tva_id"=>"tva_id",
			"fp_formula"=>"fp_formula",
			"fp_signed"=>"fp_signed",
			"jrn_def_type"=>"jrn_def_type",
			"tt_id"=>"tt_id",
			"type_detail"=>"type_detail",
			"with_tmp_val"=>"with_tmp_val",
			"type_sum_account"=>"type_sum_account",
			"operation_pcm_val"=>"operation_pcm_val"
		);

		$this->type = array(
			"fp_id" => "numeric",
			"p_id" => "numeric",
			"tmp_val" => "text",
			"tva_id"=>"numeric",
			"fp_formula"=>"text",
			"fp_signed"=>"numeric",
			"jrn_def_type"=>"text",
			"tt_id"=>"numeric",
			"type_detail"=>"numeric",
			"with_tmp_val"=>"text",
			"type_sum_account"=>"numeric",
			"operation_pcm_val"=>"text"

		);

		$this->default = array(
			"fp_id" => "auto"
		);
		global $cn;

		parent::__construct($cn, $p_id);
	}

}
class RAPAV_Declaration_SQL extends phpcompta_sql
{
	function __construct($p_id=-1)
	{


		$this->table = "rapport_advanced.declaration";
		$this->primary_key = "d_id";

		$this->name = array(
			"d_id"=>"d_id",
			"d_title"=>"d_title",
			"d_start"=>"d_start",
			"d_end"=>"d_end",
			"to_keep"=>"to_keep",
			"d_generated"=>"d_generated",
			"f_id"=>"f_id"
		);

		$this->type = array(
				"d_id"=>"numeric",
			"d_title"=>"text",
			"d_start"=>"date",
			"d_end"=>"date",
			"to_keep"=>"text",
			"d_generated"=>"date",
			"f_id"=>"numeric"
		);

		$this->default = array(
			"d_id" => "auto",
			"d_generated"=>"auto"
		);
		global $cn;

		$this->date_format = "DD.MM.YYYY";
		parent::__construct($cn, $p_id);
	}

}
class RAPAV_Declaration_Row_SQL extends phpcompta_sql
{
function __construct($p_id=-1)
	{


		$this->table = "rapport_advanced.declaration_row";
		$this->primary_key = "dr_id";

		$this->name = array(
			"dr_id"=>"dr_id",
			"d_id"=>"d_id",
			"dr_libelle"=>"dr_libelle",
			"dr_order"=>"dr_order",
			"dr_code"=>"dr_code",
			"dr_amount"=>"dr_amount",
			"dr_type"=>"dr_type",
			"dr_info"=>"dr_info"
		);

		$this->type = array(
			"dr_id"=>"numeric",
			"d_id"=>"numeric",
			"dr_libelle"=>"text",
			"dr_order"=>"text",
			"dr_code"=>"numeric",
			"dr_amount"=>"numeric",
			"dr_type"=>"numeric",
			"dr_info"=>'text'

		);

		$this->default = array(
		);
		global $cn;

		parent::__construct($cn, $p_id);
	}
}
class RAPAV_Declaration_Row_Detail_SQL extends phpcompta_sql
{
	function __construct($p_id=-1)
	{


		$this->table = "rapport_advanced.declaration_row_detail";
		$this->primary_key = "ddr_id";

		$this->name = array(
			"ddr_id"=>"ddr_id",
			"ddr_amount"=>"ddr_amount",
			"dr_id"=>"dr_id"
		);

		$this->type = array(
			"ddr_id"=>"numeric",
			"ddr_amount"=>"numeric",
			"dr_id"=>"numeric"
		);

		$this->default = array(
			"ddr_id" => "auto"
		);
		global $cn;

		parent::__construct($cn, $p_id);
	}
}
?>

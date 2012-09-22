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
			"f_description" => "f_description"
		);

		$this->type = array(
			"f_id" => "numeric",
			"f_title" => "text",
			"f_description" => "text"
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
			"type_detail"=>"type_detail"
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
			"type_detail"=>"numeric"
		);

		$this->default = array(
			"fp_id" => "auto"
		);
		global $cn;

		parent::__construct($cn, $p_id);
	}

}
?>

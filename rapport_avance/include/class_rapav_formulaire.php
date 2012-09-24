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
 * @brief Manage les formulaires
 *
 */
require_once 'class_rapport_avance_sql.php';
require_once 'class_formulaire_param.php';

class RAPAV_Formulaire extends Formulaire_Sql
{

	function __construct($f_id = -1)
	{
		$this->f_id = $f_id;
		$this->definition = array();
		parent::__construct($f_id);
	}

	/**
	 *  show a list of all existing declaration
	 * @global type $cn database connection
	 */
	static function listing()
	{
		global $cn;
		$alist = $cn->get_array("select f_id,f_title,f_description from rapport_advanced.formulaire order by 2");
		require 'template/formulaire_listing.php';
	}

	/**
	 * Get data from database, from the table rapport_advanced.formulaire_param
	 */
	function load_definition()
	{
		$f = new Formulaire_Param_Sql();
		$ret = $f->seek(" where f_id=".sql_string($this->f_id) ." order by p_order ");
		$max = Database::num_row($ret);

		for ($i = 0; $i < $max; $i++)
		{
			$o = new Formulaire_Param_Sql();
			$o = $f->next($ret, $i);
			$this->definition[] = clone $o;
		}
	}

	function input_formulaire()
	{
		$this->load();
		require_once 'template/formulaire_titre.php';
	}

	/**
	 * input the definition
	 */
	function input_definition()
	{
		$max = count($this->definition);
		global $cn;

		require 'template/formulaire_definition.php';
	}

	/**
	 * save the definition
	 * $p_array contains
	 *   - f_id id of the formulaire
	 *   - f_title title of the formulaire
	 *   - f_description description of the formulaire
	 *   - p_id array of the row in formulaire_param
	 *   - p_code array of the row in formulaire_param
	 *   - p_libelle array of the row in formulaire_param
	 *   - p_type array of the row in formulaire_param
	 *   - t_id array of the row in formulaire_param
	 *   - p_order array of the row in formulaire_param
	 *
	 */
	static function save_definition($p_array)
	{
		if ($p_array['f_id'] == -1)
		{
			self::insert_definition($p_array);
			return;
		}
		else
		{
			self::update_definition($p_array);
			return;
		}
	}

	/**
	 *
	 * @see save_definition
	 * @param type $p_array
	 */
	static function update_definition($p_array)
	{
		$rapav = new RAPAV_Formulaire($p_array['f_id']);
		// save into table formulaire
		$rapav->f_title = $p_array['f_title'];
		$rapav->f_description = $p_array['f_description'];
		$rapav->update();

		for ($i = 0; $i < count($p_array['p_id']); $i++)
		{
			$form_param = new formulaire_param_sql($p_array['p_id'][$i]);
			$form_param->p_code = $p_array['p_code'][$i];
			$form_param->p_libelle = $p_array['p_libelle'][$i];
			$form_param->p_type = $p_array['p_type'][$i];
			$form_param->p_order = $p_array['p_order'][$i];
			$form_param->t_id = $p_array['t_id'][$i];
			$form_param->f_id = $p_array['f_id'];
			// update or insert the row
			if ($p_array['p_id'][$i] == -1)
				$form_param->insert();
			else
				$form_param->update();
		}
	}

	function echo_formulaire()
	{
		echo '<h2>' . h($this->f_title) . '</h2>';
		echo '<p>' . h($this->f_description) . '<p>';
	}

	function input_parameter()
	{
		$max = count($this->definition);
		for ($i = 0; $i < $max; $i++)
		{
			$obj = Formulaire_Param::factory($this->definition[$i]);

			$obj->input();
		}
	}


}

?>

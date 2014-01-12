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
	static	function listing()
	{
		global $cn;
		$alist = $cn->get_array("select f_id,f_title,f_description
			from rapport_advanced.formulaire order by 2");
		require 'template/formulaire_listing.php';
	}
	/**
	 * Anchor to the template
	 * @return html anchor string
	 */
	function anchor_document()
	{
		$url=HtmlInput::request_to_string(array('gDossier','ac','plugin_code'));
		$url='extension.raw.php'.$url.'&amp;act=export_definition_modele&amp;id='.$this->f_id;
		return HtmlInput::anchor($this->f_filename,$url);
	}
	/**
	 * Get data from database, from the table rapport_advanced.formulaire_param
	 */
	function load_definition()
	{
		$f = new Formulaire_Param_Sql();
		$ret = $f->seek(" where f_id=" . sql_string($this->f_id) . " order by p_order ");
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
		self::verify_definition($p_array);
		self::update_definition($p_array);
		return;
	}
	/**
	 * @brief Check data and change them if needed
	 * @global database connection $cn
	 * @param array $p_array normally $_POST
	 */
	static function verify_definition(&$p_array)
	{
		global $cn;
                $count_code=count($p_array['p_code']);
		for ($i=0;$i<$count_code;$i++)
		{
			$c=$cn->get_value('select count(*) from rapport_advanced.formulaire_param
				where p_code=$1 and p_id <> $2 and f_id=$3',
					array($p_array['p_code'][$i],$p_array['p_id'][$i],$p_array['f_id']));

			if ( $c > 0 ) {
				$p_array['p_code'][$i]='C'.$i.microtime(false);
			}
		}
                
                for ($i=0;$i<$count_code;$i++)
                {
                    for ($e=0;$e<$count_code;$e++) {
                        if ($p_array['p_code'][$i] == $p_array['p_code'][$e] && $i != $e)
                        {
                            $p_array['p_code'][$e]='C'.$i.microtime(false);
                        }
                    }
                }
	}

	/**
	 *
	 * @see save_definition
	 * @param type $p_array
	 */
	static function update_definition($p_array)
	{
		global $cn;
		$rapav = new RAPAV_Formulaire($p_array['f_id']);
		// save into table formulaire
		$rapav->f_title = $p_array['f_title'];
		$rapav->f_description = $p_array['f_description'];
		$rapav->update();
		$nb_line=count($p_array['p_id']);
		for ($i = 0; $i < $nb_line ; $i++)
		{
			$form_param = new formulaire_param_sql($p_array['p_id'][$i]);
			$form_param->p_code = (trim($p_array['p_code'][$i])!="")?$p_array['p_code'][$i]:'C'.$i.microtime();
			// remove space from p_code
			$form_param->p_code=str_replace(' ', "",$form_param->p_code);

			$form_param->p_libelle = $p_array['p_libelle'][$i];
			$form_param->p_type = $p_array['p_type'][$i];
			$form_param->p_order = (isNumber($p_array['p_order'][$i]) == 0) ?  ($i+1) * 10 : $p_array['p_order'][$i];
			$form_param->t_id = $p_array['t_id'][$i];
			$form_param->f_id = $p_array['f_id'];
			// update or insert the row
			if ($p_array['p_id'][$i] == -1)
				$form_param->insert();
			else
				$form_param->update();
		}
		// delete checked rows
		if ( isset ($p_array["del_row"]))
		{
			for ($i=0;$i<count($p_array['del_row']);$i++)
			{
				if (isNumber($p_array['del_row'][$i]) == 1 &&  $p_array['del_row'][$i]!=-1) {
					$cn->exec_sql('delete from rapport_advanced.formulaire_param where p_id=$1',array($p_array['del_row'][$i]));
				}
			}
		}
		self::load_file($rapav);
	}

	static function load_file(RAPAV_Formulaire $p_rapav)
	{
		global $cn;
		// nothing to save
		if (sizeof($_FILES) == 0)
			return;

		// Start Transaction
		$cn->start();
		$name = $_FILES['rapav_template']['name'];
		$new_name = tempnam($_ENV['TMP'], 'rapav_template');
		// check if a file is submitted
		if (strlen($_FILES['rapav_template']['tmp_name']) != 0)
		{
			// upload the file and move it to temp directory
			if (move_uploaded_file($_FILES['rapav_template']['tmp_name'], $new_name))
			{
				$oid = $cn->lo_import($new_name);
				// check if the lob is in the database
				if ($oid == false)
				{
					$cn->rollback();
					return 1;
				}
			}
			// the upload in the database is successfull
			$p_rapav->f_lob = $oid;
			$p_rapav->f_filename = $_FILES['rapav_template']['name'];
			$p_rapav->f_mimetype = $_FILES['rapav_template']['type'];
			$p_rapav->f_size= $_FILES['rapav_template']['size'];

			// update rapav
			$p_rapav->update();
		}
		$cn->commit();
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
	/**
	 * @brief remove a doc template
	 */
	function remove_doc_template()
	{
		global $cn;
		$cn->lo_unlink($this->f_lob);
		$this->f_filename=null;
		$this->f_size=null;
		$this->f_mimetype=null;
		$this->f_lob=null;
		$this->update();
	}
}

?>

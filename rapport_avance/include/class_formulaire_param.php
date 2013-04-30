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
 * @brief factory display the definition and parameters of a form
 *
 */
require_once 'class_rapport_avance_sql.php';
require_once 'class_formulaire_param_detail.php';

/**
 * @brief manage the table rapport_avance.formulaire_param
 */
class Formulaire_Param extends Formulaire_Param_Sql
{

	/**
	 * Factory, create an object following the $form->p_type,
	 * @param Formulaire_Param_Sql $form
	 * @return \Formulaire_Title1|\Formulaire_Title2|\Formulaire_Title3|\Formulaire_Formula
	 */
	static function factory(Formulaire_Param_Sql $form)
	{
		switch ($form->p_type)
		{
			case 1:
				return new Formulaire_Title1($form);
			case 2:
				return new Formulaire_Title2($form);
			case 6:
				return new Formulaire_Title3($form);
			case 3:
				return new Formulaire_Formula($form);
			case 7:
				return new Formulaire_Text($form);
			case 8:
				return new Formulaire_Notice($form);
		}
	}

	/**
	 * @brief export a form to CSV to stdout
	 * @global type $cn database connx
	 * @param type $p_id the formulaire.f_id
	 */
	static function to_csv($p_id)
	{
		global $cn;
		$form = new formulaire_sql($p_id);
		$form->load();
		$title = mb_strtolower($form->f_title, 'UTF-8');
		$title = str_replace(array('/', '*', '<', '>', '*', '.', '+', ':', '?', '!', " ", ";"), "_", $title);

		$out = fopen("php://output", "w");
		header('Pragma: public');
		header('Content-type: application/bin');
		header('Content-Disposition: attachment;filename="' . $title . '.bin"', FALSE);
		fputcsv($out, array("RAPAV", '3'), ";");
		fputcsv($out, array($form->f_title, $form->f_description), ";");
		$array = $cn->get_array("select p_id,p_code, p_libelle, p_type, p_order, f_id,  t_id
			from rapport_advanced.formulaire_param where f_id=$1", array($p_id));
		for ($i = 0; $i < count($array); $i++)
		{
			fputcsv($out, $array[$i], ";");
		}
		fputcsv($out, array('RAPAV_DETAIL'), ";");
		$array = $cn->get_array("select
			fp_id, p_id, tmp_val, tva_id, fp_formula, fp_signed, jrn_def_type,
			tt_id, type_detail, with_tmp_val, type_sum_account, operation_pcm_val
			from rapport_advanced.formulaire_param_detail where p_id in (select p_id from rapport_advanced.formulaire_param where f_id=$1)", array($p_id));
		for ($i = 0; $i < count($array); $i++)
		{
			fputcsv($out, $array[$i], ";");
		}
	}

	static function from_csv($filename)
	{
		global $cn;
		$in = fopen($filename, "r");
		$cn->start();
		try
		{
			$a = fgetcsv($in, 0, ";");
			if ($a[0] != "RAPAV")
			{
				throw new Exception('Formulaire invalide');
			}
			// $a[1] contains the version
			$rapav_version=$a[1];
			// first line is the title and description
			$form = new formulaire_sql();
			$first = fgetcsv($in, 0, ";");
			$form->f_title = $first[0];
			if (isset($first[1]))
				$form->f_description = $first[1];
			$form->insert();
			// now come the formulaire_param until the keyword RAPAV_DETAIL is met
			while (($csv = fgetcsv($in, 0, ";")) != FALSE)
			{
				if ($csv[0] != "RAPAV_DETAIL")
				{
					if ( $rapav_version == 2 ) {
						unset($csv[6]);
					}
					$csv[5]=$form->f_id;
					$cn->get_array("INSERT INTO rapport_advanced.restore_formulaire_param(
						    p_id, p_code, p_libelle, p_type, p_order, f_id, t_id)
								VALUES ($1, $2, $3, $4, $5, $6, $7)", $csv);
				} else
					break;
			}
			while (($csv = fgetcsv($in, 0, ";")) != FALSE)
			{
				$t = array();
				for ($o = 0; $o < count($csv); $o++)
				{
					if ($csv[$o] == "")
						$t[$o] = null;
					else
					{
						$t[$o] = $csv[$o];
					}
				}
				$cn->get_array("INSERT INTO rapport_advanced.restore_formulaire_param_detail(
            fp_id, p_id, tmp_val, tva_id, fp_formula, fp_signed, jrn_def_type,
            tt_id, type_detail, with_tmp_val, type_sum_account, operation_pcm_val)
				VALUES ($1, $2, $3, $4, $5, $6, $7,$8, $9, $10, $11, $12)", $t);
			}
			/// Update now the table  rapport_advanced.restore_formulaire_param and set the correct pk
			$cn->exec_sql("update rapport_advanced.restore_formulaire_param set p_id=nextval('rapport_advanced.formulaire_param_p_id_seq')");
			$cn->exec_sql("update rapport_advanced.restore_formulaire_param_detail set fp_id=nextval('rapport_advanced.formulaire_param_detail_fp_id_seq')");

			$cn->exec_sql('insert into rapport_advanced.formulaire_param select  p_id, p_code, p_libelle, p_type, p_order, f_id, t_id
				from rapport_advanced.restore_formulaire_param where f_id=$1',array($form->f_id));

			$cn->exec_sql('insert into rapport_advanced.formulaire_param_detail select fp_id, p_id, tmp_val, tva_id, fp_formula, fp_signed, jrn_def_type,
            tt_id, type_detail, with_tmp_val, type_sum_account, operation_pcm_val from  rapport_advanced.restore_formulaire_param_detail where p_id in (
			select p_id from rapport_advanced.restore_formulaire_param where f_id=$1)',array($form->f_id));

			$cn->exec_sql('delete from  rapport_advanced.restore_formulaire_param where f_id=$1',array($form->f_id));
			$cn->commit();
		}
		catch (Exception $exc)
		{
			echo $exc->getTraceAsString();
			throw $exc;
		}
	}

}

/**
 * @brief mother class of \Formulaire_Title1|\Formulaire_Title2|\Formulaire_Title3|\Formulaire_Formula
 */
class Formulaire_Row
{

	function __construct(formulaire_param_sql $e)
	{
		$this->obj = $e;
	}

	function display()
	{

	}

	function input()
	{

	}

	/**
	 * @brief load all the row from formulaire_param_detail, children of formulaire_param
	 *  return an array of objects Formulaire_Param_Detail
	 * @param type $p_id
	 */
	static function load_all($p_id)
	{
		global $cn;
		$a_value = $cn->get_array("select fp_id,type_detail from rapport_advanced.formulaire_param_detail where p_id=$1", array($p_id));
		return $a_value;
	}

}

/**
 * @brief display title level 1
 */
class formulaire_title1 extends Formulaire_Row
{

	function display()
	{
		echo h1($this->obj->p_libelle, "");
	}

	function input()
	{
		echo h1($this->obj->p_libelle, ' class="title"');
	}

}
/**
 * @brief display title level 1
 */
class formulaire_text extends Formulaire_Row
{

	function display()
	{
		echo '<p>'.$this->obj->p_libelle.'<p>';
	}

	function input()
	{
		echo '<p>'.$this->obj->p_libelle.'<p>';
	}

}
/**
 * @brief display title level 1
 */
class formulaire_notice extends Formulaire_Row
{

	function display()
	{
		echo span($this->obj->p_libelle, ' class="notice" ');
	}

	function input()
	{
		echo span($this->obj->p_libelle, ' class="notice"');
	}

}

/**
 * @brief display title level 2
 */
class formulaire_title2 extends Formulaire_Row
{

	function display()
	{
		echo h2($this->obj->p_libelle, 'class="title"');
	}

	function input()
	{
		echo h2($this->obj->p_libelle, 'class="title"');
	}

}

/**
 * @brief display title level 3
 */
class formulaire_title3 extends Formulaire_Row
{

	function display()
	{
		echo "<h3>" . $this->obj->p_libelle . "</h3>";
	}

	function input()
	{
		echo "<h3 class=\"title\">" . $this->obj->p_libelle . "</h3>";
	}

}

/**
 * @brief display the formula : depending of the type of formula, a factory is used and an object RAPAV_Formula, RAPAV_Account_TVA
 * or RAPAV_compute will be used for the display of the details
 */
class Formulaire_Formula extends Formulaire_Row
{

	function __construct(formulaire_param_sql $e)
	{
		$this->obj = $e;
		$this->id = $e->p_id;
		$this->parametre = Formulaire_Row::load_all($this->id);
	}

	function display()
	{
		echo $this->obj->p_libelle;
	}

	/**
	 * @brief return an object following the key type_detail of the array passed in parameter
	 *
	 * @param type $p_index
	 * @return \RAPAV_Formula|\RAPAV_Account_Tva|\RAPAV_Compute
	 */
	function make_object($p_index)
	{
		$elt = $this->parametre[$p_index]['type_detail'];
		switch ($elt)
		{
			case '1':
				return new RAPAV_Formula($this->parametre[$p_index]['fp_id']);
				break;
			case '2':
				return new RAPAV_Account_Tva($this->parametre[$p_index]['fp_id']);
				break;
			case '3':
				return new RAPAV_Compute($this->parametre[$p_index]['fp_id']);
				break;
			case '4':
				return new RAPAV_Account($this->parametre[$p_index]['fp_id']);
				break;
			case '5':
				return new RAPAV_Reconcile($this->parametre[$p_index]['fp_id']);
				break;
		}
	}

	/**
	 * @brief input value
	 */
	function input()
	{
		echo '<h4 class="title">' . $this->obj->p_libelle . "(" . $this->obj->p_code . ")" . '</h4>';
		echo HtmlInput::hidden('p_id[]', $this->obj->p_id);
		$max = count($this->parametre);
		echo HtmlInput::hidden("count_" . $this->id, $max);
		//echo '<h5 class="title">' . 'code ' . $this->obj->p_code . '</h5>';
		echo '<p>';
		echo '<table id="table_' . $this->id . '">';
		for ($i = 0; $i < $max; $i++)
		{
			$formula = $this->make_object($i);

			echo '<tr id="tr_' . $formula->fp_id . '">';
			echo '<td>';
			echo $formula->display_row();
			echo '</td>';
			echo "<td id=\"del_" . $formula->fp_id . "\">";
			echo HtmlInput::anchor("Effacer", "", sprintf("onclick=\"delete_param_detail('%s','%s','%s','%s')\""
							, $_REQUEST['plugin_code'], $_REQUEST['ac'], $_REQUEST['gDossier'], $formula->fp_id));
			echo '</td>';
			echo '</tr>';
		}
		if ($max == 0)
			echo '<tr></tr>';
		echo "</table>";
		echo '</p>';
		echo HtmlInput::button_anchor(
				"Ajout d'une ligne", "javascript:void(0)", "add_row" . $this->id, sprintf("onclick=\"add_param_detail('%s','%s','%s','%s');\"", $_REQUEST['plugin_code'], $_REQUEST['ac'], $_REQUEST['gDossier'], $this->id)
		);
	}

}

?>

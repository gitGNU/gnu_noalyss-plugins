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
require_once 'class_rapport_avance_sql.php';
require_once 'class_rapav_formulaire.php';

class Formulaire_Param_Detail extends Formulaire_Param_Detail_SQL
{

	function input_new($p_id)
	{
		$parent = new Formulaire_Param($p_id);
		echo HtmlInput::title_box($parent->p_code . " " . $parent->p_libelle, 'param_detail_div');
		require_once 'template/param_detail_new.php';
	}

}

class RAPAV_Formula extends Formulaire_Param_Detail
{

	function display_row()
	{
		printf("Résultat de la formule %s", $this->fp_formula);
	}

	static function new_row()
	{
		$account = new IPoste("formula_new", "", "form_id");
		$account->size = 50;
		$account->label = _("Recherche poste");
		$account->set_attribute('gDossier', dossier::id());
		$account->set_attribute('bracket', 1);
		$account->set_attribute('no_overwrite', 1);
		$account->set_attribute('noquery', 1);
		$account->set_attribute('account', $account->id);
		echo $account->input();
	}

	function verify()
	{
		if (Impress::check_formula($this->fp_formula)==false)
		{
			$this->errcode="Erreur dans votre formule";
			return 1;
		}
		if ( trim($this->fp_formula)=="")
		{
			$this->errcode=" Aucune formule trouvée";
			return 1;
		}
		return 0;
	}
}

class RAPAV_Account_Tva extends Formulaire_Param_Detail
{

	function display_row()
	{
		global $cn;
		$type_total=$cn->get_value("select tt_label from rapport_advanced.total_type where tt_id=$1",array($this->tt_id));
		printf("Poste comptable %s avec le code tva %s (%s) dans le journal %s [ %s ]",
				$this->tmp_val, $this->tva_id, $this->tva_id, $this->jrn_def_type,$type_total);
	}

	static function new_row()
	{
		global $cn;
		$account = new IPoste("formtva", "", "formtva_id");
		$account->size = 20;
		$account->label = _("Recherche poste");
		$account->set_attribute('gDossier', dossier::id());
		$account->set_attribute('noquery', 1);
		$account->set_attribute('account', $account->id);

		$tva = new ITva_Popup("code_tva");
		$tva->id = HtmlInput::generate_id("code_tva");
		$tva->set_attribute('gDossier', dossier::id());

		// Jrn type
		$select = new ISelect('code_jrn');
		$select->value = array(
			array('value' => 'VEN', 'label' => 'journaux Vente'),
			array('value' => 'ACH', 'label' => 'journaux Achat')
		);
		echo '<table>';
		echo '<tr><td>Poste comptable</td>';
		echo td($account->input());
		echo '</tr>';
		echo td('TVA') . td($tva->input());
		echo '</tr>';
		echo td(_('Choix du type de journal ')) . td($select->input());
		// Base or VAT
		echo '</tr>';
		$code_base = new ISelect('code_base');
		$code_base->value = $cn->make_array("select tt_id,tt_label from rapport_advanced.total_type order by 2");
		echo td("Type de total");
		echo td($code_base->input());
		echo '</tr>';
		echo '</table>';
	}

	function verify()
	{
		global $cn;
		if (trim($this->tmp_val) == "")
		{
			$this->errcode = 'Poste comptable est vide';
			return 1;
		}
		$count = $cn->get_value("select count(*) from tva_rate where tva_id=$1", array($this->tva_id));
		if ($count == 0)
		{
			$this->errcode = 'Code TVA inexistant';
			return 1;
		}
	}

}

class RAPAV_Compute extends Formulaire_Param_Detail
{

	function display_row()
	{
		printf("Total des codes du formulaire %s", $this->fp_formula);
	}

	static function new_row($p_id)
	{
		global $cn;
		$f_id = $cn->get_value("select f_id from rapport_advanced.formulaire_param where p_id=$1", array($p_id));
		$account = new IText("form_compute");
		$account->size = 50;
		echo $account->input();
		echo HtmlInput::button('rapav_search_code_bt', 'Cherche codes', sprintf(" onclick=\"rapav_search_code('%s','%s','%s','%s')\"", $_REQUEST['ac'], $_REQUEST['plugin_code'], $_REQUEST['gDossier'], $f_id));
	}
	function verify()
	{

		if ( trim($this->fp_formula)=="")
		{
			$this->errcode=" Aucune formule trouvée";
			return 1;
		}
		return 0;
	}

}

?>

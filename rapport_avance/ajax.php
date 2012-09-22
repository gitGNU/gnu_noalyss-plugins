<?php
extract($_GET);
global $cn;
switch ($act)
{
	/*	 * ******************************************************************************************************* */
	// Modifie une definition de formulaire
	/*	 * ******************************************************************************************************* */

	case 'mod_form':
		require_once 'include/class_rapav_formulaire.php';
		if (!isset($_GET['f_id']) && isNum($_GET['f_id']) == 0)
			exit();
		echo '<h1>Définition </h1>';
		$form = new RAPAV_formulaire($_REQUEST['f_id']);
		$form->load_definition();
		echo '<form method="POST" class="print">';
		$form->input_formulaire();
		$form->input_definition();
		echo HtmlInput::submit('form_def_sub', 'Sauve');
		echo '</form>';
		break;
	/*	 * **************************************************************************************************************
	 * Ajoute une ligne dans la definition
	 * *************************************************************************************************************** */
	case 'add_row_definition':
		$type_row = $cn->make_array("select p_type,p_description from rapport_advanced.type_row order by p_description");
		$type_periode = $cn->make_array("select t_id,t_description from rapport_advanced.periode_type order by t_description");
		?>
		<td>
			<?= HtmlInput::hidden('p_id[]', -1)?>
			<?
			$p_code = new IText('p_code[]');
			$p_code->size = "10";
			echo $p_code->input();
			?>
		</td>
		<td>
			<?
			$p_libelle = new IText('p_libelle[]');
			$p_libelle->css_size = "100%";
			echo $p_libelle->input();
			?>
		</td>
		<td>
			<?
			$p_type = new ISelect('p_type[]');
			$p_type->value = $type_row;
			echo $p_type->input();
			?>
		</td>
		<td>
			<?
			$p_type_periode = new ISelect('t_id[]');
			$p_type_periode->value = $type_periode;
			echo $p_type_periode->input();
			?>
		</td>
		<td>
			<?
			$p_order = new INum('p_order[]');
			$p_order->prec = 0;
			$p_order->size = 4;
			echo $p_order->input();
			?>
		</td>
		<td>
			<?
			$p_info = new IText('p_info[]');
			$p_info->css_size = "100%";
			echo $p_info->input();
			?>
		</td>'
		<?
		break;
	/*	 * **************************************************************************************************************
	 * Montre le résultat et permet de changer les paramètrages d'un formulaire
	 * uniquement pour ceux ayant un champs de calcul (formule, code tva+poste comptable + totaux intermédiare
	 * *************************************************************************************************************** */
	case 'mod_param':
		require_once 'include/class_rapav_formulaire.php';
		if (!isset($_GET['f_id']) && isNum($_GET['f_id']) == 0)
			exit();
		echo '<h1>Paramètre </h1>';
		$form = new RAPAV_formulaire($_REQUEST['f_id']);
		$form->load_definition();
		echo '<form method="POST" class="print">';
		echo HtmlInput::hidden('f_id', $_REQUEST['f_id']);
		$form->echo_formulaire();
		$form->input_parameter();
		echo HtmlInput::submit('form_param_sub', 'Sauve');
		echo '</form>';
		break;

	/******************************************************************************************************************
	 * Montre un écran pour ajouter une ligne de formulaire dans les paramètre de formulaires
	 */
	case 'add_param_detail':
		include 'ajax_add_param_detail.php';
		break;
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//rapav_search_code cherche les codes du formulaires courants
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	case 'rapav_search_code':
		include 'ajax_search_code.php';
		break;
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Sauve résultat et renvoie un xml
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	case 'save_param_detail':
		include 'ajax_save_param_detail.php';
		break;
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Delete un formulaire_param_detail
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	case 'delete_param_detail':
		$cn->exec_sql("delete from rapport_advanced.formulaire_param_detail where fp_id=$1",array($fp_id));
	default:
		break;

}
?>

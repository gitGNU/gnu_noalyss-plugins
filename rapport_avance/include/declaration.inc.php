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
 * @brief Déclaration
 *
 */
require_once 'class_rapav_declaration.php';
global $cn;

/*
 * Save the date (update them)
 */
if (isset($_POST['save']))
{
	$decl = new Rapav_Declaration();
	$decl->d_id = $_POST['d_id'];
	$decl->load();
	$decl->to_keep='Y';
	$decl->save();
	$decl->display();
	echo '<h2 class="notice"> Sauvé</h2>';
	exit();
}
/*
 * compute and propose to modify and save
 */
if (isset($_GET['compute']))
{
	$decl = new Rapav_Declaration();
	if (isDate($_GET['p_start']) == 0 || isDate($_GET['p_end']) == 0)
	{
		alert('Date invalide');
	}
	else
	{
		$decl->compute($_GET['p_form'], $_GET['p_start'], $_GET['p_end']);
		echo '<form class="print" method="POST">';
		$decl->display();
		echo HtmlInput::submit('save', 'Sauver');
		echo '</form>';
		exit();
	}
}
$date_start = new IDate('p_start');
$date_end = new IDate('p_end');
$hidden = HtmlInput::array_to_hidden(array('gDossier', 'ac', 'plugin_code', 'sa'), $_GET);
$select = new ISelect('p_form');
$select->value = $cn->make_array('select f_id,f_title from rapport_advanced.formulaire order by 2');
?>
<form id="declaration_form_id" method="GET" onsubmit="return validate()">
	<?= $hidden?>
	<table>
		<tr>
			<td>
				Déclaration
			</td>
			<td>
				<?= $select->input()?>
			</td>
		<tr>
			<td>
				Date de début
			</td>
			<td>
				<?= $date_start->input()?>
			</td>
		</tr>
		<tr>
			<td>
				Date de fin
			</td>
			<td>
				<?= $date_end->input()?>
			</td>
		</tr>
	</table>
	<?= HtmlInput::submit('compute', 'Générer')?>
</form>
<script charset="UTF8" lang="javascript">
	function validate() {
		if ( check_date_id('<?=$date_start->id?>') == false ) {alert('Date de début incorrecte');return false;}
		if ( check_date_id('<?=$date_end->id?>') == false ) {alert('Date de fin incorrecte');return false;}
	}
</script>
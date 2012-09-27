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
if (isset($_GET['compute']))
{
	$decl=new Rapav_Declaration();
	/**
	 * @todo verifier date
	 */
	$decl->compute($_GET['p_form'],$_GET['p_start'],$_GET['p_end']);
}
$date_start=new IDate('p_start');
$date_end=new IDate('p_end');
$hidden=HtmlInput::array_to_hidden(array('gDossier','ac','plugin_code','sa'),$_GET);
$select=new ISelect('p_form');
$select->value=$cn->make_array('select f_id,f_title from rapport_advanced.formulaire order by 2');
?>
<form method="GET">
	<?=$hidden?>
	<table>
		<tr>
			<td>
				Déclaration
			</td>
			<td>
				<?=$select->input()?>
			</td>
		<tr>
			<td>
				Date de début
			</td>
			<td>
				<?=$date_start->input()?>
			</td>
		</tr>
		<tr>
			<td>
			Date de fin
			</td>
			<td>
				<?=$date_end->input()?>
			</td>
		</tr>
	</table>
	<?=HtmlInput::submit('compute','Générer')?>
</form>
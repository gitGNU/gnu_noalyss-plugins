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
 * @brief gestion des clefs de répartitions
 *
 */
global $cn,$gDossier;
$sql="select cr_id,cr_name,cr_note,cr_start,cr_end from coprop.clef_repartition ";
/**
 * @todo ajouter tri
 */

$a_key=$cn->get_array($sql);
?>
<table class="result">
	<tr>
		<th>
			Nom
		</th>
		<th>
			Note
		</th>
		<th>
			Date début
		</th>
		<th>
			Date Fin
		</th>
	</tr>
<?
for ($i=0;$i < count($a_key);$i++):
	$js=sprintf("mod_key(\'%s\',\'%s\',\'%s\')",$gDossier,$a_key[$i]['cr_id'],$_REQUEST['plugin_code']);
	$mod_key=HtmlInput::anchor($a_key[$i]['cr_name'],"",$js);
?>
	<tr>
		<td>
			<?=$mod_key?>
		</td>
		<td>
			<?=$a_key[$i]['cr_note']?>
		</td>
		<td>
			<?=format_date($a_key[$i]['cr_date'])?>
		</td>
		<td>
			<?=format_date($a_key[$i]['cr_end'])?>
		</td>
	</tr>
<?
endfor;
?>
</table>
<?=HtmlInput::button("add_key","Ajout clef","onclick=\"add_key('".$gDossier."')\"")?>
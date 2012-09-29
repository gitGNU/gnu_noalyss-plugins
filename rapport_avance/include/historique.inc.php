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
 * @brief show the history of the saved declaration
 * take data from rapport_avance.declaration and display via ajax
 */
global $cn;
$data=$cn->get_array("select d_id,d_title,
		d_start,d_end
		from rapport_advanced.declaration
		where to_keep='Y'
		order by d_start,d_title");
?>
<div id="declaration_list_div">
<table class="sortable">
	<tr>
		<th>
			Date début
		</th>
		<th>
			Date Fin
		</th>
		<th>
			Déclaration
		</th>
		<th>

		</th>
		<th>
			
		</th>
	</tr>
	<? for ($i=0;$i<count($data);$i++) :?>
	<tr id="tr_<?=$data[$i]['d_id']?>">
		<td>
			<?=format_date($data[$i]['d_start'])?>
		</td>
		<td>
			<?=format_date($data[$i]['d_end'])?>
		</td>
		<td>
			<?=h($data[$i]['d_title'])?>
		</td>
		<td id="mod_<?=$data[$i]['d_id']?>">
			<?=HtmlInput::anchor("Afficher","",sprintf("onclick=\"rapav_declaration_display('%s','%s','%s','%s')\"",$_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$data[$i]['d_id']))?>
		</td>
		<td id="del_<?=$data[$i]['d_id']?>">
			<?=HtmlInput::anchor("Efface","",sprintf("onclick=\"rapav_declaration_delete('%s','%s','%s','%s')\"",$_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$data[$i]['d_id']))?>
		</td>
	</tr>
	<? endfor; ?>
</table>
</div>
<div id="declaration_display_div">

</div>
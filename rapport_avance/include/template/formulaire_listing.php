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
 * @brief display list of formulaire + button
 *
 */
?>
<div id="form_list_div">
<table class="result">
	<tr>
		<th>Nom du formulaire</th>
		<th>Description</th>
		<th></th>
		<th></th>
	</tr>
<?
	for ($i=0;$i<count($alist);$i++):
?>
	<tr>
		<td>
			<?=h($alist[$i]['f_title'])?>
		</td>
		<td>
			<?=h($alist[$i]['f_description'])?>
		</td>
		<td>
			<?=HtmlInput::anchor('Définition', "",sprintf('onclick="rapav_form_def(\'%s\',\'%s\',\'%s\',\'%s\')"',$_REQUEST['plugin_code'],$_REQUEST['ac'],dossier::id(),$alist[$i]['f_id']))?>
		</td>
		<td>
			<?=HtmlInput::anchor('Paramètre', "",sprintf('onclick="rapav_form_param(\'%s\',\'%s\',\'%s\',\'%s\')"',$_REQUEST['plugin_code'],$_REQUEST['ac'],dossier::id(),$alist[$i]['f_id']))?>
		</td>
	</tr>

<?	endfor; ?>
</table>
</div>
<div id="form_mod_div">

</div>
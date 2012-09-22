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
 * @brief show the list of code of the form
 *
 */
global $cn;
$array=$cn->get_array("select p_id,p_code,p_libelle from rapport_advanced.formulaire_param where p_type=3 and f_id=$1 order by 2",array($f_id));
echo HtmlInput::title_box('Code Formulaire','search_code_div');
?>
<table>
	<tr>
		<th>
			Code
		</th>
		<th>
			Libellé
		</th>
	</tr>
	<? for ($i=0;$i<count($array);$i++): ?>
	<tr>
		<td>
			<?=HtmlInput::anchor(h($array[$i]['p_code']),"",'onclick="$(\'form_compute\').value+=\'['.$array[$i]['p_code'].']\';removeDiv(\'search_code_div\');"')?>
		</td>
		<td>
			<?=h($array[$i]['p_libelle'])?>
		</td>
	</tr>
	<?endfor;?>
</table>
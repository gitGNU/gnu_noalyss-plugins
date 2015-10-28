<?php

/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief show the list of code of the form
 *
 */
global $cn;
$array=$cn->get_array("select p_id,p_code,p_libelle from rapport_advanced.formulaire_param where p_type=3 and f_id=$1 order by 2",array($f_id));
echo HtmlInput::title_box('Code Formulaire','search_code_div');
echo _('Cherche').':'.HtmlInput::filter_table('code_tb','0,1',1);
?>
<table id='code_tb'>
	<tr>
		<th>
			Code
		</th>
		<th>
			Libellé
		</th>
	</tr>
	<?php for ($i=0;$i<count($array);$i++): ?>
	<tr>
		<td>
			<?php echo HtmlInput::anchor(h($array[$i]['p_code']),"",'onclick="$(\'form_compute\').value+=\'['.$array[$i]['p_code'].']\';removeDiv(\'search_code_div\');"')?>
		</td>
		<td>
			<?php echo h($array[$i]['p_libelle'])?>
		</td>
	</tr>
	<?php endfor;?>
</table>

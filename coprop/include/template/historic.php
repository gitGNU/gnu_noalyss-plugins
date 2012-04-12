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
 * @brief display history
 *
 */
?>
<table class="result">
	<tr>
		<th>
			Date
		</th>
		<th>
			Budget
		</th>
		<th>
			Clef de répartition (appel par montant)
		</th>

		<th>
			Montant
		</th>
		<th>
			Opération
		</th>
		<th>
			Pourcentage
		</th>
		<th>
			Exercice
		</th>

	</tr>
<? for ($i=0;$i<count($array);$i++): ?>
	<tr>
		<td>
			<?=$array[$i]['str_date']?>
		</td>
		<td>
			<?=$array[$i]['b_name']?>
		</td>
		<td>
			<?=$array[$i]['cr_name']?>
		</td>
		<td>
			<?=nb($array[$i]['af_amount'])?>
		</td>
		<td>
			<?=HtmlInput::detail_op($array[$i]['jr_id'], $array[$i]['jr_internal'])?>
		</td>
		<td>
			<?=$array[$i]['af_percent']?>
		</td>
		<td>
			<?=$array[$i]['b_exercice']?>
		</td>
	</tr>
<? endfor; ?>

</table>
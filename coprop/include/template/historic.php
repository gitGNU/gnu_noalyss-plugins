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
<?php for ($i=0;$i<count($array);$i++): ?>
	<tr>
		<td>
			<?php echo $array[$i]['str_date']?>
		</td>
		<td>
			<?php echo $array[$i]['b_name']?>
		</td>
		<td>
			<?php echo $array[$i]['cr_name']?>
		</td>
		<td>
			<?php echo nb($array[$i]['af_amount'])?>
		</td>
		<td>
			<?php echo HtmlInput::detail_op($array[$i]['jr_id'], $array[$i]['jr_internal'])?>
		</td>
		<td>
			<?php echo $array[$i]['af_percent']?>
		</td>
		<td>
			<?php echo $array[$i]['b_exercice']?>
		</td>
	</tr>
<?php endfor; ?>

</table>
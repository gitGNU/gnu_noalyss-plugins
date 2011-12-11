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
 * @brief demande info pour générer appel de fond
 *
 */
?>
<table>
	<tr>

		<td>Journal pour l'appel de fond</td>
		<td>
			<?=$led_appel_fond->input()?>
		</td>
	</tr>
	<tr>
		<td>
			Date de l'opération
		</td>
		<td>
			<?=$date->input()?>
		</td>
	</tr>
	<tr>

		<td>
			Montant à répartir
		</td>
		<td>
			<?=$amount->input()?>
		</td>
	</tr>
	<tr>

		<td>
			Poste comptable pour l'appel de fond
		</td>
		<td>
			<?=$poste_appel->input()?>
		</td>
	</tr>
	<tr>

		<td>
			Clef de répartiton à utiliser
		</td>
		<td>
			<?=$key->input()?>
		</td>
	</tr>

</table>
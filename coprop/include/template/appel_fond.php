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

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief demande info pour générer appel de fond
 *
 */
?>
<table>
	<tr>
		<td>
			Date de l'opération
		</td>
		<td>
			<?php echo $date->input()?>
		</td>
	</tr>
		<td>Journal pour l'appel de fond</td>
		<td>
			<?php echo $led_appel_fond->input()?>
		</td>
        </tr>
        <tr>
            <td> Fiche pour l'appel</td>
            
            <td><?php echo $f_categorie_appel_bt?><?php echo $categorie_appel->input()?></td>
		<td><?php echo $f_categorie_appel_label?></td>
		<td><?php echo $str_add_appel?></td>
	</tr>

</table>
appel de fond par <?php echo $appel_fond_type->input()?>
<div style="margin-left:150px">
<table id="appel_fond_budget">
        	<tr >
                    
		<td>
			Budget à utiliser
		</td>
		<td>
			<?php echo $budget_sel->input()?>
		</td>
        </tr>
        <tr>
		<td>
			Pourcentage du budget
		</td>
		<td>
			<?php echo $budget_pct->input()?>
		</td>
	</tr>
</table>
<table id="appel_fond_amount">
	<tr>

		<td>
			Montant à répartir
		</td>
		<td>
			<?php echo $amount->input()?>
		</td>
        </tr>
        <tr>
		<td>
			Clef de répartiton à utiliser
		</td>
		<td>
			<?php echo $key->input()?>
		</td>
	</tr>

</table>
</div>
<script> appel_fond_show()</script>

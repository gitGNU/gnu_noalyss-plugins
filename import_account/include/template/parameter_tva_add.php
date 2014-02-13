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
 * @brief add a match between tva and rate
 *
 */
?>
<div id="dtvaadd" class="inner_box" style="display:none">
   <h2 class="info"><?php echo _("Ajout d'un taux de tva")?></h2>
	<form method="POST">

		<table>
			<tr>
				<td>
					TVA
				</td>
				<td>
					<?php $tva_id = new ITva_Popup('tva_id');
					echo $tva_id->input()?>
				</td>
			</tr>
			<tr>
				<td>
					Taux dans le fichier <?php echo  HtmlInput::infobulle(50)?>
				</td>
				<td>
					<?php $w = new INum('pt_rate');echo $w->input();?>
				</td>
			</tr>
		</table>
   <?php echo  HtmlInput::submit("ftvaadd", _("Ajout"));?>
		<?php 
		$bt = new IButton("but_tva_close");
$bt->label = _("Fermer");
		$bt->javascript = " $('dtvaadd').hide()";
		echo $bt->input();
		?>
	</form>

</div>
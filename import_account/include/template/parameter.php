<?php
/*
 * Copyright 2010 De Bontridder Dany <dany@alchimerys.be>
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
?>
<?php 
	$bt=new IButton("but_tva_add");
	$bt->label="Ajout d'une correspondance";
	$bt->javascript=" $('dtvaadd').show()";
	echo $bt->input();
?>

<table class="result" style="width:60%;margin-left:20%">
	<tr>
		<th>Fiche TVA</th>
		<th>Taux correspondant <?php echo HtmlInput::infobulle(50)?></th>
	</tr>
	<?php
	for ($i = 0; $i < count($atva); $i++):
		?>
		<tr>
			<td>
				<?php
				$wtva = new ITva_Popup("tva_" . $atva[$i]['pt_id']);
				$wtva->value = $atva[$i]['tva_id'];
				echo $wtva->input();
				?>
			</td>
			<td>
				<?php
				$wrate = new Itext('rate' . $atva[$i]['pt_id']);
				$wrate->value=$atva[$i]['pt_rate'];
				echo $wrate->input();
				?>
			</td>
		</tr>



	<?php endfor;?>

</table>


<?
	$bt=new IButton("but_tva_add");
	$bt->label="Ajout d'une correspondance";
	$bt->javascript=" $('dtvaadd').show()";
	echo $bt->input();
?>

<table class="result" style="width:60%;margin-left:20%">
	<tr>
		<th>Fiche TVA</th>
		<th>Taux correspondant <?=HtmlInput::infobulle(50)?></th>
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
				$wrate = new INum('rate' . $atva[$i]['pt_id']);
				$wrate->value=$atva[$i]['pt_rate'];
				echo $wrate->input();
				?>
			</td>
		</tr>



	<?php endfor;?>

</table>

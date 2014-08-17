<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<h2>Etape 2 / 4: introduire le fichier</h2>

<form method="POST"   enctype="multipart/form-data" >
<table>
<tr>
	<td>
	Nom du format
	</td>
	<td>
	<?php echo $format->input()?>
	</td>
</tr>
<tr>
	<td>
	A importer dans le journal de banque
	</td>
	<td>
	<?php echo $jrn_def->input()?>
	</td>
</tr>
<tr>
	<td>
Format de date
	</td>
	<td>
	<?php echo $format_date->input()?>
	</td>
</tr>

<tr>
	<td>
	Séparateur de champs
	</td>
	<td>
	<?php echo $sep_field->input()?>
	</td>
</tr>

<tr>
	<td>
	Séparateur de millier
	</td>
	<td>
	<?php echo $sep_thousand->input()?>
	</td>
</tr>

<tr>
	<td>
	Séparateur décimal
	</td>
	<td>
	<?php echo $sep_decimal->input()?>
	</td>
</tr>
<tr>
	<td>
	Ligne d'en-tête à ne pas prendre en considération
	</td>
	<td>
	<?php echo $skip->input()?>
	</td>
</tr>

<tr>
	<td>
	Les lignes ayant ce nombre de colonnes sont valides, laissez à vide si vous ne savez pas
	</td>
	<td>
	<?php echo $nb_col->input()?>
	</td>
</tr>



</table>

Fichier à importer <?php echo $file->input()?>

<?php 
echo HtmlInput::submit('input_file','Valider');
echo HtmlInput::get_to_hidden(array('format','gDossier','sa','plugin_code','format'));
echo HtmlInput::hidden('sb',$sb);

?>
</form>
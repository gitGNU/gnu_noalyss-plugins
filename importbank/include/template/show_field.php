<h2>Etape 2 / 4: introduire le fichier</h2>

<form method="POST"   enctype="multipart/form-data" action="extension.php">
<table>
<tr>
	<td>
	Nom du format
	</td>
	<td>
	<?=$format->input()?>
	</td>
</tr>
<tr>
	<td>
	A importer dans le journal de banque
	</td>
	<td>
	<?=$jrn_def->input()?>
	</td>
</tr>
<tr>
	<td>
Format de date
	</td>
	<td>
	<?=$format_date->input()?>
	</td>
</tr>

<tr>
	<td>
	Séparateur de champs
	</td>
	<td>
	<?=$sep_field->input()?>
	</td>
</tr>

<tr>
	<td>
	Séparateur de millier
	</td>
	<td>
	<?=$sep_thousand->input()?>
	</td>
</tr>

<tr>
	<td>
	Séparateur décimal
	</td>
	<td>
	<?=$sep_decimal->input()?>
	</td>
</tr>
<tr>
	<td>
	Les lignes ayant ce nombre de colonnes sont valides, laissez à vide si vous ne savez pas
	</td>
	<td>
	<?=$nb_col->input()?>
	</td>
</tr>



</table>

Fichier à importer <?=$file->input()?>

<? 
echo HtmlInput::submit('input_file','Valider');
echo HtmlInput::get_to_hidden(array('format','gDossier','sa','plugin_code','format'));
echo HtmlInput::hidden('sb',$sb);

?>
</form>
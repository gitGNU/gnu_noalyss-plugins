<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<h2> 
<?php
echo _('Importation de données');
?>
</h2>

<p>Pour importer des données, c'est-à-dire transformer des fichiers CSV (Valeur séparé par des virgules) en des fiches. Vous devez choisir, un fichier et donner une catégorie de fiche existante. Ensuite, il suffit d'indiquer quelles colonnes correspondent à quelle attribut. 
</p>
<p>
   Par défaut, correspond à un export CSV depuis Calc (OpenOffice.org ou libreoffice);
</p>
<form method="POST" enctype="multipart/form-data">
<?php echo $hidden?>
<table>
<tr>
<td>Délimiteur </td><TD> <?php echo $delimiter->input()?></td>
</tr>
<tr>
<td>Fichier à charger</td><TD> <?php echo $file->input()?></td>
</tr>
<tr>
<td>Catégorie de fiche</td><TD> <?php echo $fd->input();?></td>
</tr>
<tr>
<td>Encodage unicode</td><TD> <?php echo $encodage->input()?></td>
</tr>
<tr>
<td>  Texte entouré du signe </td><TD><input type="text" name="rsurround" value='"' size="1"></td>
</tr>
</table>
<?php echo HtmlInput::submit('test_import','Valider');?>

</form>

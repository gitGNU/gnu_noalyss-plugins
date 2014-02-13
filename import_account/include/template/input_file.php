<h2> 
<?php
echo _('Importation de données');
?>
</h2>

<p><?php echo _("Pour importer des données, c'est-à-dire transformer des fichiers CSV (Valeur séparé par des virgules) en des fiches. Vous devez choisir, un fichier et donner une catégorie de fiche existante. Ensuite, il suffit d'indiquer quelles colonnes correspondent à quelle attribut.");?>
</p>
<p>
 <?php echo _("Par défaut, correspond à un export CSV depuis Calc (OpenOffice.org ou libreoffice)")?>;
</p>
<form method="POST" enctype="multipart/form-data">
<?php echo $hidden?>
<table>
<tr>
   <td><?php echo _("Délimiteur");?> </td><TD> <?php echo $delimiter->input()?></td>
</tr>
<tr>
   <td><?php echo _("Fichier à charger")?></td><TD> <?php echo $file->input()?></td>
</tr>
<tr>
   <td><?php echo _("Catégorie de fiche")?></td><TD> <?php echo $fd->input();?></td>
</tr>
<tr>
   <td><?php echo _("Encodage unicode")?></td><TD> <?php echo $encodage->input()?></td>
</tr>
<tr>
   <td><?php echo _("Texte entouré du signe")?> </td><TD><input type="text" name="rsurround" value='"' size="1"></td>
</tr>
</table>
   <?php echo HtmlInput::submit('test_import',_('Valider'));?>

</form>

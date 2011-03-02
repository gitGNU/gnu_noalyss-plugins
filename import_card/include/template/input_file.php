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
<?=$hidden?>
Délimiteur = <? echo $delimiter->input()?>
Fichier à charger <? echo $file->input()?>
Catégorie de fiche <? echo $fd->input();?>
Encodage unicode <? echo $encodage->input()?>
  Texte entouré du signe <input type="text" name="rsurround" value='"' size="1">
<? echo HtmlInput::submit('test_import','Valider');?>

</form>

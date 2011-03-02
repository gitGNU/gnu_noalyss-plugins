
<h2> 
<?php
echo _('Importation de données');
?>
</h2>
<? var_dump($_POST);?>
<p>Pour importer des données, c'est-à-dire transformer des fichiers CSV (Valeur séparé par des virgules) en des fiches. Vous devez choisir, un fichier et donner une catégorie de fiche existante. Ensuite, il suffit d'indiquer quelles colonnes correspondent à quelle attribut. 
</p>

<form method="POST" >
<?=$hidden?>
Délimiteur = <? echo $_POST['rdelimiter']?>
Fichier à charger <? echo $_FILES['csv_file']['name']?>
Catégorie de fiche <? echo $file_cat;?>
Encodage  <? echo $encoding?>
Texte entouré par <? echo $_POST['rsurround'];?>
<?
foreach (array('rfichedef','rdelimiter','encodage') as $e)
{
  if ( isset($_POST[$e])) echo HtmlInput::hidden($e,$_POST[$e]);
}
echo HtmlInput::hidden('filename',$filename);

 echo HtmlInput::submit('record_import','Valider');
?>
<input type="hidden" name="rsurround" value='<?=$_POST['rsurround']?>'>



<?
   global $cn;
   ob_start();
  /**
   * Open the file and parse it
   */
$fcard=fopen($filename,'r');
$row_count=0;
$max=0;
while (($row=fgetcsv($fcard,0,$_POST['rdelimiter'],$_POST['rsurround'])) !== false)
  {
    $row_count++;
    echo '<tr style="border:solid 1px black">';
    echo td($row_count);
    $count_col=count($row);
    $max=($count_col>$max)?$count_col:$max;
    for ($i=0;$i<$count_col;$i++)
      {
	echo td($row[$i],'style="border:solid 1px black"');
      }
      echo '</tr>';
  }
$table=ob_get_contents();
ob_clean();


echo '<table style="border:solid 1px black;width:100%">
<tr>';

/**
 *create widget column header
 */
$header=new ISelect('head_col[]');

$sql=sprintf('select ad_id,ad_text from jnt_fic_attr join attr_def using(ad_id) where fd_id=%d order by ad_text ',$_POST['rfichedef']);
$header->value=$cn->make_array($sql);
$header->value[]=array('value'=>-1,'label'=>'-- Non Utilisé --');
$header->selected=-1;
echo th('Numéro de ligne');
for ($i=0;$i<$max;$i++)
  {
    echo '<th>'.$header->input().'</th>';
  }
echo '</tr>';
echo $table;
echo '</table>';
echo '</form>';
?>
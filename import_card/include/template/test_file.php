
<h2> 
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
echo _('Importation de données');
?>
</h2>

<p><?php echo _("Pour importer des données, c'est-à-dire transformer des fichiers CSV (Valeur séparé par des virgules) en des fiches. Vous devez choisir, un fichier et donner une catégorie de fiche existante. Ensuite, il suffit d'indiquer quelles colonnes correspondent à quelle attribut.");?>
</p>

<form method="POST" >
<?php echo $hidden?>
<table>
<tr>
<td><?php echo _("Délimiteur ");?></td>
<td> <?php echo $_POST['rdelimiter']?></td>
</tr>
<tr>
<td><?php echo _("Fichier à charger");?></td><td> <?php echo $_FILES['csv_file']['name']?></td>
</tr>
<tr>
<td><?php echo _("Catégorie de fiche");?></td><td> <?php echo $file_cat;?></td>
</tr>
<tr>
<td><?php echo _("Encodage");?> </td><td> <?php echo $encoding?></td>
</tr>
<tr>
<td><?php echo _("Texte entouré par");?></td><td> <?php echo $_POST['rsurround'];?></td>
</tr>
<tr>
    <td>
        <?php echo _("Suppression première ligne");?>
    </td>
    <td>
        <?php $msg=($skip_row==0)?"non":"oui"; echo $msg;?>
    </td>
</tr>
</table>
<?php 
foreach (array('rfichedef','rdelimiter','encodage') as $e)
{
  if ( isset($_POST[$e])) echo HtmlInput::hidden($e,$_POST[$e]);
}
echo HtmlInput::hidden('filename',$filename);

 echo HtmlInput::submit('record_import','Valider');
?>
<input type="hidden" name="rsurround" value='<?php echo $_POST['rsurround']?>'>



<?php 
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
    if ( $skip_row == 1 && $row_count==1) echo td(_('Supprimé')); else echo td($row_count); 
    $count_col=count($row);
    $max=($count_col>$max)?$count_col:$max;
    for ($i=0;$i<$count_col;$i++)
      {
	echo td($row[$i],'style="border:solid 1px black"');
      }
      echo '</tr>';
  }
$table=ob_get_contents();
ob_end_clean();


echo '<table style="border:solid 1px black;width:100%">
<tr>';

/**
 *create widget column header
 */
$header=new ISelect('head_col[]');
$fiche_def=HtmlInput::default_value_post('rfichedef','');
if ( $fiche_def == '' || isNumber($fiche_def) == 0)
{
    throw new Exception(_('Catégorie invalide'));
}
$sql=sprintf('select ad_id,ad_text from jnt_fic_attr join attr_def using(ad_id) where fd_id=%d order by ad_text ',$fiche_def);
$a_attribute=$cn->get_array(
        "select ad_id,ad_text from jnt_fic_attr join attr_def using(ad_id) where fd_id=$1 order by jnt_order ",
        array($fiche_def)
        );
$header->value=$cn->make_array($sql);
$header->value[]=array('value'=>-1,'label'=>'-- Non Utilisé --');
$header->selected=-1;
echo th('Numéro de ligne');
$nb_attribute = count($a_attribute);
for ($i=0;$i<$max;$i++)
  {
    if ($i >= $nb_attribute) 
        $header->selected=-1;
    else
        $header->selected=$a_attribute[$i]['ad_id'];
    echo '<th>'.$header->input().'</th>';
  }
echo '</tr>';
echo $table;
echo '</table>';
echo '</form>';
?>

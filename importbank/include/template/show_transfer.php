<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<?php
global $g_succeed,$g_failed;
$cn->start();
$row_count=0;$max=0;
switch ($sep_field->selected)
{
	case '1':
		$sp=',';
		break;
	case '2':
		$sp=';';
		break;
	default :
	  echo _("Séparateur de champs inconnu");
	  exit();
}

$str_date=$format_date->display();


$id=$cn->get_value('insert into importbank.import(format_bank_id) values ($1) returning id',
		  array($format_bank->id));
ob_start();
$skip_row=$imported_row=0;
echo '<table>';
while( ($row=fgetcsv($fbank,0,$sp)) !== false)
{
    $row_count++;

    $array_row=$row;
    $count_col=count($array_row);
    if ($row_count<=$_POST['skip'])
        continue;
    
    if ($count_col==$_POST['nb_col'])
    {
        echo '<tr style="border:solid 1px black">';
        echo td($row_count);

        $tp_date=$amount=$libelle=$operation_nb=$third=$extra=null;
        $status='N';
        for ($i=0; $i<$count_col; $i++)
        {
            switch ($_POST['header'][$i])
            {
                case 0:
                    $tp_date=$array_row[$i];
                    
                    break;
                case 1:

                    $amount=$array_row[$i];
                    break;
                case 2:
                    $libelle=utf8_encode($array_row[$i]);
                    break;
                case 3:
                    $operation_nb=preg_replace('/[^[:print:]]/', '',
                            $array_row[$i]);
                    break;
                case 4:
                    $third=utf8_encode($array_row[$i]);
                    break;
                case 5:
                    $extra=utf8_encode(preg_replace('/[^[:print:]]/', '',
                                    $array_row[$i]));
                    break;
            }
            if ($_POST['header'][$i]!='-1')
            {
                echo td(utf8_encode($array_row[$i]),
                        'style="border:solid 1px black;color:green"');
            }
        }
        /* insert into importbank.temp_bank
          Check for duplicate, valid date, amount ....
         */
        // replace + sign
        $amount=str_replace('+', '', $amount);

        // remove space
        $amount=str_replace(' ', '', $amount);

        if ($format_bank->sep_thousand!='')
            $amount=str_replace($athousand[$format_bank->sep_thousand]['label'],
                    '', $amount);
        if ($adecimal[$format_bank->sep_decimal]<>'.')
            $amount=str_replace($adecimal[$format_bank->sep_decimal]['label'],
                    '.', $amount);
        try
        {
            /**
             * if amount not valid we don't insert
             */
            if (isNumber($amount) == 0 ) {
                throw new Exception(_('Montant invalide'),10);
            }
            /**
             * if date not valid we don't insert
             */
            $test = DateTime::createFromFormat($aformat_date[$format_bank->format_date-1]['format'], trim($tp_date));
            if ( $test === false )
            {
                throw new Exception (_('Date invalide').' '.$str_date,20);
            }
            $cn->exec_Sql('insert into importbank.temp_bank(tp_date,jrn_def_id,libelle,amount,ref_operation,status,import_id,tp_third,tp_extra)'.
                    ' values (to_date($1,\''.$str_date.'\'),$2,$3,$4,$5,$6,$7,$8,$9)',
                    array($tp_date, $format_bank->jrn_def_id, $libelle, $amount,
                $operation_nb, $status, $id, $third, $extra));
            $imported_row++;
            echo '<td>'.$g_succeed.'</td>';
        }
        catch (Exception $ex)
        {
            $skip_row++;
            $exc_code=$ex->getCode();
            if ( $exc_code = 10 || $exc_code == 20 ) {
                // date or amount is invalid
                echo '<td>'.$g_failed.$ex->getMessage().'</td>';
            }else {
            echo _("Echec import");
            alert($ex->getMessage());
            if ( DEBUG ) {
                var_dump($ex->getTraceAsString());
            }
            $cn->rollback();
            }
        }
    } // end if


    echo '</tr>';
}

echo '</table>';
$table=ob_get_contents();
ob_end_clean();
$cn->commit();
$nb_col->value=($nb_col->value=='')?$max:$nb_col->value;
?>

<h2><?php echo _("Etape 4/4 : les données sont sauvegardées")?></h2>
<form method="POST"   enctype="multipart/form-data">
<table>
<tr>
	<td>
	<?php echo _("Nom du format")?>
	</td>
	<td>
	<?php echo $format->input()?>
	</td>
</tr>
<tr>
	<td>
	<?php echo _("A importer dans le journal de banque")?>
	</td>
	<td>
	<?php echo $jrn_def->input()?>
	</td>
</tr>
<tr>
	<td>
<?php echo _("Format de date")?>
	</td>
	<td>
	<?php echo $format_date->input()?>
	</td>
</tr>

<tr>
	<td>
	<?php echo _("Séparateur de champs")?>
	</td>
	<td>
	<?php echo $sep_field->input()?>
	</td>
</tr>

<tr>
	<td>
	<?php echo _("Séparateur de millier")?>
	</td>
	<td>
	<?php echo $sep_thousand->input()?>
	</td>
</tr>

<tr>
	<td>
	<?php echo _("Séparateur décimal")?>
	</td>
	<td>
	<?php echo $sep_decimal->input()?>
	</td>
</tr>
<tr>
	<td>
	<?php echo _("Ligne d'en-tête à ne pas prendre en considération")?>
	</td>
	<td>
	<?php echo $skip->input()?>
	</td>
</tr>

<tr>
	<td>
	<?php echo _("Les lignes ayant ce nombre de colonnes sont valides")?>
	</td>
	<td>
	<?php echo $nb_col->input()?>
	</td>
</tr>





</table>

<table>
<tr>
<?php
$header=new ISelect('header[]');
$header->value=$aheader;

echo th(_('Ligne n°'));
echo th(_('Nbre de colonnes'));
for ( $i=0;$i<$max;$i++)
{
  $header->selected=-1;
	switch ($i)
	{
	case $pos_date:
	  $header->selected=0;
	  break;

	case $pos_amount:
	  $header->selected=1;
	  break;

	case $pos_lib:
	  $header->selected=2;
	  break;

	case $pos_operation_nb:
	  $header->selected=3;
	  break;
	 case 4:
	 $third=utf8_encode($array_row[$i]);
	 break;
	 case 5:
	  $extra=utf8_encode($array_row[$i]);
	 break;

	}
	echo '<th>'.$header->input()."</th>";

}
echo '</tr>';

?>
</table>
<?php echo $table?>
    <ul style="list-style: none">
        <li><?php echo _('Importés')," ",$imported_row?></li>
        <li><?php echo _('Echec')," ",$skip_row?></li>
    </ul>
            
</form>

<h2 class="info"> Journal cible = <?=$jrn_name?> Compte de ce journal <?=$jrn_account?></h2>
<form method="get">

Filtrer : <?=$filter->input()?>
<?=HtmlInput::request_to_hidden(array('gDossier','plugin_code','ac','sb','sa','id'))?>
<?=HtmlInput::submit('refresh','Recharger')?>
</form>
<form method="get" onsubmit="return confirm ('Vous confirmez ?')">
<?=HtmlInput::request_to_hidden(array('gDossier','plugin_code','ac','sb','sa','id',$filter->name))?>
<?=HtmlInput::submit('delete_record','Effacer')?>
<?=HtmlInput::submit('transfer_record','Transfèrer')?>
</form>

<table class="result">
	<TR>
	<th></th>
	<TH>N° opération</TH>
	<th>Date</th>
	<th>Montant</th>
	<th>Etat</th>
	<th>Tiers</th>
	<th>Libellé</th>
	<th>Extra</th>
</TR>
<?
	$gdossier=Dossier::id();
	$plugin_code=$_REQUEST['plugin_code'];
	for ($i=0;$i<Database::num_row($ret);$i++):
		$row=Database::fetch_array($ret,$i);
		$class=($i%2==0)?' class="even"':'class="odd"';

		$javascript="onclick=\"reconcilie('div${row['id']}','$gdossier','${row['id']}','$plugin_code')\"";

?>
<tr <?=$class?> >
<td>
	<?=HtmlInput::button('bt'.$row['id'],'Reconcilie',$javascript)?>
</td>
<TD>
<?=$row['ref_operation']?>
</TD>

<TD>
<?=format_date($row['tp_date'])?>
</TD>

<td class="num">
<?=nbm($row['amount'])?>
</td>
<td id="<?='st'.$row['id']?>" <?=Import_Bank::color_status($row['status'])?>  >
<?=$row['f_status']?>
</td>

<td>
<?=h($row['tp_third'])?>
</td>

<td>
<?=h($row['libelle'])?>
</td>



<td >
<?=h($row['tp_extra'])?>
</td>
</tr>
<?
	endfor;
?>

</table>
<form method="get" onsubmit="return confirm ('Vous confirmez?')">
<?=HtmlInput::request_to_hidden(array('gDossier','plugin_code','ac','sb','sa','id',$filter->name))?>
<?=HtmlInput::submit('delete_record','Effacer')?>
<?=HtmlInput::submit('transfer_record','Transfèrer')?>
</form>

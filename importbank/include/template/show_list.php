<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<h2 class="info"> Journal cible = <?php echo $jrn_name?> Compte de ce journal <?php echo $jrn_account?></h2>
<form method="get">

Filtrer : <?php echo $filter->input()?>
<?php echo HtmlInput::request_to_hidden(array('gDossier','plugin_code','ac','sb','sa','id'))?>
<?php echo HtmlInput::submit('refresh','Recharger')?>
</form>
<form method="get" id="show_list_frm2" onsubmit="return confirm_box('show_list_frm2','Vous confirmez?')">
<?php echo HtmlInput::request_to_hidden(array('gDossier','plugin_code','ac','sb','sa','id',$filter->name))?>
<?php echo HtmlInput::hidden('form_action2','');?>
<?php echo HtmlInput::submit('delete_record',_('Effacer'),
        ' onclick="$(\'form_action2\').value=\'delete_record\';"');?>
<?php echo HtmlInput::submit('transfer_record',_('Transfèrer'),
        ' onclick="$(\'form_action2\').value=\'transfer_record\';"');?>
</form>
<?php echo HtmlInput::filter_table('record_tb_id','1,2,3,4,5,6,7',1); ?>
<table id="record_tb_id" class="sortable" >
	<TR>
	<th></th>
	<TH>N° opération</TH>
	<th  class=" sorttable_sorted_reverse" >Date <span id="sorttable_sortrevind">&nbsp;&blacktriangle;</span></th>
	<th>Montant</th>
	<th>Etat</th>
	<th>Tiers</th>
	<th>Libellé</th>
	<th>Extra</th>
</TR>
<?php 
	$gdossier=Dossier::id();
	$plugin_code=$_REQUEST['plugin_code'];
	for ($i=0;$i<Database::num_row($ret);$i++):
		$row=Database::fetch_array($ret,$i);
		$class=($i%2==0)?' class="even"':'class="odd"';

		$javascript="onclick=\"reconcilie('div${row['id']}','$gdossier','${row['id']}','$plugin_code')\"";

?>
<tr <?php echo $class?> >
<td sorttable_customkey="1">
	<?php echo HtmlInput::button('bt'.$row['id'],'Reconcilie',$javascript)?>
</td>
<TD>
<?php echo $row['ref_operation']?>
</TD>

<TD sorttable_customkey="<?php echo $row['tp_date']; ?>">
<?php echo format_date($row['tp_date'])?>
</TD>

<td class="num" sorttable_customkey="<?php echo $row['amount']; ?>">
<?php echo nbm($row['amount'])?>
</td>
<td id="<?php echo 'st'.$row['id']?>" <?php echo Import_Bank::color_status($row['status'])?>  >
<?php echo $row['f_status']?>
</td>

<td>
<?php echo h($row['tp_third'])?>
</td>

<td>
<?php echo h($row['libelle'])?>
</td>



<td >
<?php echo h($row['tp_extra'])?>
</td>
</tr>
<?php 
	endfor;
?>

</table>
<form method="get" id="show_list_frm" onsubmit="return confirm_box('show_list_frm','Vous confirmez?')">
<?php echo HtmlInput::request_to_hidden(array('gDossier','plugin_code','ac','sb','sa','id',$filter->name))?>
<?php echo HtmlInput::hidden('form_action','');?>
<?php echo HtmlInput::submit('delete_record',_('Effacer'),
        ' onclick="$(\'form_action\').value=\'delete_record\';"');?>
<?php echo HtmlInput::submit('transfer_record',_('Transfèrer'),
        ' onclick="$(\'form_action\').value=\'transfer_record\';"');?>
</form>

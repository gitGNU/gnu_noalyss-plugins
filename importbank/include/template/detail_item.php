<div id="<?=$ctl?>">
<?=HtmlInput::anchor_close($ctl)?>

<?php
echo h2info('Détail opération');
	if ($bi->id=='')
	{
		echo h2('Opération effacée','class="notice"');
		return;
	}
echo "<span style=\"float:right\" class=\"notice\">$msg</span>";
?>
<form method="get" onsubmit="save_bank_info(this);return false;">
<?php
echo HtmlInput::request_to_hidden(array('id','ctl','gDossier','plugin_code','act'));
echo HtmlInput::hidden('p_jrn',$bi->jrn_def_id);
?>

<table>
<TR>
<TD>Date</TD><td><?=$date->input()?></td></tr>
<tr><td>n° opération </td><td><?=h($bi->ref_operation)?></td></tr>
<tr><TD>
	Tiers
    </TD>
    <td>
	<?=$third->input()?>
    </td>
</tr>
<tr>
	<TD>Montant
	</TD>
	<td><?=$amount->input()?>
	</td>
</tr>
<tr>
	<TD>Libelle
	</TD>
	<td><?=$libelle->input()?>
	</td>
</tr>
<tr>
	<TD>Autre information
	</TD>
	<td><?=$extra->input()?>
	</td>
</tr>
<tr>
	<TD>Journal
	</TD>
	<td><?=$jrn?>
	</td>
</tr>
<tr>
	<TD>Contrepartie
	</TD>
	<td><?=$w->input()?><?=$w->search()?><span id="e_third"><?=h($name)?></span>
	</td>
</tr>
<tr>
	<TD>reconciliation
	</TD>
	<td><?=$wConcerned->input();?>
	</td>
</tr>
<? $style=($bi->status == 'E') ? 'style="color:red;font-weight:bold"' : '';?>

<tr>
	<TD>statut</TD>
	<td <?=$style?> ><?=$status?></td>
</tr>
<? if ($bi->status != 'D') : ?>
<tr>
	<TD>A effacer</TD>
	<td><?=$remove->input();?>
</td>
</tr>
<? else :?>
<tr>
	<TD>A ne pas effacer</TD>
	<td><?=$recup->input();?>
</td>
</tr>
<? endif; ?>
</table>
<?=HtmlInput::submit('save','Sauve');?>
<?=HtmlInput::button_close($ctl)?>
</form>
</div>

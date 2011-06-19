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
<TD>Date</TD><td><?=$bi->tp_date?></td></tr>
<tr><td>n° opération </td><td><?=h($bi->ref_operation)?></td></tr>
<tr><TD>
	Tiers
    </TD>
    <td>
	<?=h($bi->tp_third)?>
    </td>
</tr>
<tr>
	<TD>Montant
	</TD>
	<td class="num"><?=nbm($bi->amount)?>
	</td>
</tr>
<tr>
	<TD>Libelle
	</TD>
	<td><?=h($bi->libelle)?>
	</td>
</tr>
<tr>
	<TD>Autre information
	</TD>
	<td><?=h($bi->tp_extra)?>
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
<tr>
	<TD>statut</TD>
	<td><?=$status?></td>
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

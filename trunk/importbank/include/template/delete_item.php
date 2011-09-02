<div id="<?=$ctl?>">
<?=HtmlInput::anchor_close($ctl)?>

<?php
echo h2info('Détail opération');
	if ($bi->id=='')
	{
		echo h2('Opération effacée','class="notice"');
		return;
	}
echo 		h2('Opération effacée','class="notice"');

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
	<td><?=nbm($bi->amount)?>
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
<!--
<tr>
	<TD>Contrepartie
	</TD>
<?php $w->readOnly=true;$wConcerned->readOnly=true;?>
	<td><? /* $w->input() */ ?><? /* $w->search() */?><span id="e_third"></span>
	</td>
</tr>
<tr>
	<TD>reconciliation
	</TD>
	<td><? /* $wConcerned->input();*/ ?>
	</td>
</tr>
-->
</table>

</form>
</div>
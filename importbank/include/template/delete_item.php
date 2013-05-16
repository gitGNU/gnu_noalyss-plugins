<div id="<?php echo $ctl?>">
<?php echo HtmlInput::anchor_close($ctl)?>

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
<TD>Date</TD><td><?php echo $bi->tp_date?></td></tr>
<tr><td>n° opération </td><td><?php echo h($bi->ref_operation)?></td></tr>
<tr><TD>
	Tiers
    </TD>
    <td>
	<?php echo h($bi->tp_third)?>
    </td>
</tr>
<tr>
	<TD>Montant
	</TD>
	<td><?php echo nbm($bi->amount)?>
	</td>
</tr>
<tr>
	<TD>Libelle
	</TD>
	<td><?php echo h($bi->libelle)?>
	</td>
</tr>
<tr>
	<TD>Autre information
	</TD>
	<td><?php echo h($bi->tp_extra)?>
	</td>
</tr>
<tr>
	<TD>Journal
	</TD>
	<td><?php echo $jrn?>
	</td>
</tr>
<!--
<tr>
	<TD>Contrepartie
	</TD>
<?php $w->readOnly=true;$wConcerned->readOnly=true;?>
	<td><?php /* $w->input() */ ?><?php /* $w->search() */?><span id="e_third"></span>
	</td>
</tr>
<tr>
	<TD>reconciliation
	</TD>
	<td><?php /* $wConcerned->input();*/ ?>
	</td>
</tr>
-->
</table>

</form>
</div>
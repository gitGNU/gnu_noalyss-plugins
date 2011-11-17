<FORM METHOD="GET">
<?=$hidden?>
<table>
	<TR>
	<TD>Choississez le journal</TD>
	<td><?=$iledger->input()?></td>
	</TR>
	<tr>
	<TD>Date début</TD>
	<td><?=$idate_start->input()?></td>
	</tr>
	<tr>
	<TD>Date fin</TD>
	<td><?=$idate_end->input()?></td>
	</tr>
</table>
	<?=HtmlInput::request_to_hidden(array('ac','plugin_code'));?>
<?=$submit?>
</FORM>
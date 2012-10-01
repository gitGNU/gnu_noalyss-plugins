<FORM METHOD="GET" onsubmit="return validate();">
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
<script charset="UTF8" lang="javascript">
	function validate() {
		if ( check_date_id('<?=$idate_start->id?>') == false ) {
			alert('Date de début incorrecte');
			$('<?=$idate_start->id?>').style.borderColor='red';
			return false;}
		if ( check_date_id('<?=$idate_end->id?>') == false ) {
			alert('Date de fin incorrecte');
			$('<?=$idate_end->id?>').style.borderColor='red';
			return false;}
	}
</script>
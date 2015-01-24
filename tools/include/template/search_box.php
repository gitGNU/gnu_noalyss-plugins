<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<FORM METHOD="GET" onsubmit="return validate();">
<?php echo $hidden?>
<table>
	<TR>
	<TD>Choisissez le journal</TD>
	<td><?php echo $iledger->input()?></td>
	</TR>
	<tr>
	<TD>Date début</TD>
	<td><?php echo $idate_start->input()?></td>
	</tr>
	<tr>
	<TD>Date fin</TD>
	<td><?php echo $idate_end->input()?></td>
	</tr>
</table>
	<?php echo HtmlInput::request_to_hidden(array('ac','plugin_code'));?>
<?php echo $submit?>
</FORM>
<script charset="UTF8" lang="javascript">
	function validate() {
		if ( check_date_id('<?php echo $idate_start->id?>') == false ) {
			alert('Date de début incorrecte');
			$('<?php echo $idate_start->id?>').style.borderColor='red';
			return false;}
		if ( check_date_id('<?php echo $idate_end->id?>') == false ) {
			alert('Date de fin incorrecte');
			$('<?php echo $idate_end->id?>').style.borderColor='red';
			return false;}
	}
</script>
<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<table class="result">
<tr>
	<Th>Date</Th>
	<th>internal</th>
	<th>Libelle</th>
	<th>Pièce</th>
	<th>Montant</th>
	<TH><INPUT TYPE="CHECKBOX" onclick="toggle_checkbox('form1')"></TH>
</tr>
<?php
  for ($i=0;$i<$nb_row;$i++):
	$row=Database::fetch_array($ret,$i);
	$class=($i%2==0)?' class="even" ':' class="odd" ';
	$checkbox->value=$row['jr_id'];
?>
<tr <?php echo $class?> >
	<TD><?php echo $row['str_date']?></TD>
	<td><?php echo HtmlInput::detail_op($row['jr_id'],$row['jr_internal'])?></td>
	<td><?php echo h($row['jr_comment'])?>
	<td><?php echo $row['jr_pj_number']?></td>
	<td class="num"><?php echo nbm($row['jr_montant'])?></td>
	<td><?php echo $checkbox->input();?></td>
	</tr>
<?php 
endfor;
?>
</table>
</form>

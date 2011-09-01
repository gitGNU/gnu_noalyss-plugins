<table class="result">
<tr>
	<Th>Date</Th>
	<th>internal</th>
	<th>Libelle</th>
	<th>Pièce</th>
	<th>Montant</th>
</tr>
<?php
  for ($i=0;$i<$nb_row;$i++):
	$row=Database::fetch_array($ret,$i);
	$class=($i%2==0)?' class="even" ':' class="odd" ';
?>
<tr <?=$class?> >
	<TD><?=$row['str_date']?></TD>
	<td><?=HtmlInput::detail_op($row['jr_id'],$row['jr_internal'])?></td>
	<td><?=h($row['jr_comment'])?>
	<td><?=$row['jr_pj_number']?></td>
	<td class="num"><?=nbm($row['jr_montant'])?></td>
	</tr>
<?
endfor;
?>
</table>
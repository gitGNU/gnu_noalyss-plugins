
<form id="purge" method="POST">
<table class="result">
	<TR>
	<th> num transfert</th>
	<Th>Date </Th>
	<Th>Nom format</Th>
	<Th>Supprimer </Th>
</TR>
<?php
	for ($i=0;$i<Database::num_row($ret);$i++):

	if ($i%2 == 0 )
		$class='class="even"';
	else
		$class='class="odd"';
	$row=$cn->fetch($i);
?>
<tr <?=$class?>>
<td><?=$row['id']?></td>
<td>
<?=HtmlInput::hidden('id[]',$row['id']);?>
<?=format_date($row['i_date'])?>
</td>
<td>
<?=h($row['format_name'])?>
</td>
<td>
<?
    $select=new ICheckBox('s_del[]',$row['id']);

echo $select->input()
?>
</td>
<TD>
<?php
// list
echo HtmlInput::button_anchor('Détail',$link.'&id='.$row['id']);
?>
</TD>

</tr>
<?
	endfor;
?>
</table>

<?php
$action="onclick=\"select_checkbox('purge')\"";
echo HtmlInput::button('s','Tous cochez',$action);

$action="onclick=\"unselect_checkbox('purge')\"";
echo HtmlInput::button('u','Tous décochez',$action);
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
$action=" onclick=\"return confirm('Vous confirmez ?');\"";
echo HtmlInput::submit('delete','Supprimer la sélection',$action);
?>

</form>
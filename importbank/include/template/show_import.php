
<form id="purge" method="POST">
<table class="table_large">
	<TR>
	<th> num transfert</th>
	<Th>Date </Th>
	<Th>Nom format</Th>
	<th>Nouveau</th>
	<th>Transfèré</th>
	<th>Attente</th>
	<th>Erreur</th>
	<th>Effacé</th>
	<Th>Supprimer </Th>
	<th></th>
</TR>
<?php
	for ($i=0;$i<Database::num_row($ret);$i++):

	if ($i%2 == 0 )
		$class='class="even"';
	else
		$class='class="odd"';
	$row=$cn->fetch_array($ret,$i);
$delete=$cn->execute('status',array($row['id'],'D'));
$ndelete=Database::fetch_array($delete,0);

$new=$cn->execute('status',array($row['id'],'N'));
$nnew=Database::fetch_array($new,0);

$error=$cn->execute('status',array($row['id'],'E'));
$nerror=Database::fetch_array($error,0);

$transf=$cn->execute('status',array($row['id'],'T'));
$ntransf=Database::fetch_array($transf,0);

$rec=$cn->execute('status',array($row['id'],'W'));
$nrec=Database::fetch_array($rec,0);


?>
<tr <?=$class?>>
<td><?=$row['id']?></td>
<td>
<?=HtmlInput::hidden('id[]',$row['id']);?>
<?=format_date($row['str_date'])?>
</td>
<td>
<?=h($row['format_name'])?>
</td>

<td><?=$nnew[0]?></td>
<td><?=$ntransf[0]?></td>
<td><?=$nrec[0]?></td>

<td><?=$nerror[0]?></td>
<td><?=$ndelete[0]?></td>

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
</tr>

</tr>
<?
	endfor;
?>
</table>

<?php
$action="onclick=\"select_checkbox('purge')\"";
echo HtmlInput::button('s','Tout cocher',$action);

$action="onclick=\"unselect_checkbox('purge')\"";
echo HtmlInput::button('u','Tout décocher',$action);
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
$action=" onclick=\"return confirm('Vous confirmez ?');\"";
echo HtmlInput::submit('delete','Supprimer la sélection',$action);
?>

</form>
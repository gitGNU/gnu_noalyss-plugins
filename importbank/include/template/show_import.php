
<form id="purge" method="POST">
<table class="result">
	<TR>
	<th> num transfert</th>
	<Th>Date </Th>
	<Th>Nom format</Th>
	<th>Nouveau</th>
	<th>Transfèrer</th>
	<th>A transfèrer</th>
	<th>Erreur</th>
	<th>A effacer</th>
	<Th>Supprimer </Th>
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
<?=format_date($row['i_date'])?>
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
echo HtmlInput::button('s','Tous cochez',$action);

$action="onclick=\"unselect_checkbox('purge')\"";
echo HtmlInput::button('u','Tous décochez',$action);
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
$action=" onclick=\"return confirm('Vous confirmez ?');\"";
echo HtmlInput::submit('delete','Supprimer la sélection',$action);
?>

</form>
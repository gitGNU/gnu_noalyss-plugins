<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
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
<tr <?php echo $class?>>
<td><?php echo $row['id']?></td>
<td>
<?php echo HtmlInput::hidden('id[]',$row['id']);?>
<?php echo format_date($row['str_date'])?>
</td>
<td>
<?php echo h($row['format_name'])?>
</td>

<td><?php echo $nnew[0]?></td>
<td><?php echo $ntransf[0]?></td>
<td><?php echo $nrec[0]?></td>

<td><?php echo $nerror[0]?></td>
<td><?php echo $ndelete[0]?></td>

<td>
<?php 
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
<?php 
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
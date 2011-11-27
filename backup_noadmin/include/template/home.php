<h2> Backup du dossier courant </h2>
<form method="get" ACTION="extension.raw.php">
Voulez-vous avoir une copie de ce dossier
<?=HtmlInput::submit('backup','backup')?>
<?=dossier::hidden()?>
<?=HtmlInput::extension();?>
<?=HtmlInput::hidden('ac',$_REQUEST['ac']);?>
</form>
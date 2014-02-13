<h2><?php echo _("Backup du dossier courant")?> </h2>
<form method="get" ACTION="extension.raw.php">
   <?php echo _("Voulez-vous avoir une copie de ce dossier")?>
<?php echo HtmlInput::submit('backup','backup')?>
<?php echo dossier::hidden()?>
<?php echo HtmlInput::extension();?>
<?php echo HtmlInput::hidden('ac',$_REQUEST['ac']);?>
</form>
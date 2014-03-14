<fieldset>
<legend><?php echo $f_legend ?> </legend>
   <?php echo _('Date').' '.$f_date ?><br>
<?php echo $f_jrn?><br>
<?php echo _('Banque')?><?php echo $ibank->input(); ?><?php echo $ibank->search()?> <span id='e_bank_account_label'><?$f_bank_label?></span>
</fieldset>

<fieldset>
<legend><?php echo $f_legend_detail?></legend>
   <fieldset><legend><?php echo _('Extrait de compte')?></legend>
   <?php echo _('Numéro extrait')?> <?php echo $f_extrait?>
<input type="text" disabled  id="first_sold"></span>
</fieldset>
<?php echo $str_add_button?>
   <fieldset><legend><?php echo _('Opérations')?></legend>
<table id="fin_item" width="100%" border="0">
<tr>
<th colspan="2">code<?HtmlInput::infobulle(0)?></TH>
   <th><?php echo _('Commentaire')?></TH>
   <th><?php echo _('Montant')?></TH>
  </tr>

<?php foreach ($array as $item) {
echo '<tr>';
echo td($item['search'].$item['qcode']);
echo td($item['span']);
echo td($item['comment']);
echo td($item['amount']);
echo '</tr>';
}
?>
</table>
</fieldset>
</fieldset>



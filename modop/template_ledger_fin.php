<fieldset>
<legend><?=$f_legend ?> </legend>
   <?=_('Date').' '.$f_date ?><br>
<?=$f_jrn?><br>
<?=_('Banque')?><?=$ibank->input(); ?><?=$ibank->search()?> <span id='e_bank_account_label'><?$f_bank_label?></span>
</fieldset>

<fieldset>
<legend><?=$f_legend_detail?></legend>
   <fieldset><legend><?=_('Extrait de compte')?></legend>
   <?=_('Numéro extrait')?> <?=$f_extrait?>
</fieldset>
<?=$str_add_button?><?=$str_cal_button?>
   <fieldset><legend><?=_('Opérations')?></legend>
<table id="fin_item" width="100%" border="0">
<tr>
<th colspan="2">code<?HtmlInput::infobulle(0)?></TH>
   <th><?=_('Commentaire')?></TH>
   <th><?=_('Montant')?></TH>
  </tr>

<? foreach ($array as $item) {
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



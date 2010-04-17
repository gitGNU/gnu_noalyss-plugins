<fieldset>
<legend>
   <?=_('Entrez la période de la déclaration')?>
</legend>
<form method="GET">
<?=_('Année')?> <?=$str_year?><br/>
<?=$str_monthly?>   <?=_('Mois')?> : <?=$str_month?><br/>
<?=$str_quaterly?>   <?=_('Trimestre')?> : <?=$str_quater?><br/>
<?=$str_hidden?>
<?php
if (isset ($by_year) && $by_year == true ) :
?>
<?=_('Par année')?> <?=$str_byyear?><br/>
<?php
endif;
?>
<br>
<?=$str_submit?>
</form>
</fieldset>
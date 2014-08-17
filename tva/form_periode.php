<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<fieldset>
<legend>
   <?php echo _('Entrez la période de la déclaration')?>
</legend>
<form class="print" method="GET">
<?php echo _('Année')?> <?php echo $str_year?><br/>
<?php echo $str_monthly?>   <?php echo _('Mois')?> : <?php echo $str_month?><br/>
<?php echo $str_quaterly?>   <?php echo _('Trimestre')?> : <?php echo $str_quater?><br/>
<?php echo $str_hidden?>
<?php
if (isset ($by_year) && $by_year == true ) :
?>
<?php echo _('Par année')?> <?php echo $str_byyear?><br/>
<?php
echo HtmlInput::request_to_hidden(array('ac'));
endif;
?>
<br>
<?php echo $str_submit?>
</form>
</fieldset>
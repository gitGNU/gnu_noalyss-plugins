<?php 
//This file is part of NOALYSS and is under GPL 
//see licence.txt
echo  HtmlInput::button('receipt_bt','Renuméroter les pièces','onclick="$(\'div_receipt\').show();"');
?>
<form method="POST" id="form1" onsubmit="return confirm_box(this,'Vous confirmez ?')">
    <input type="hidden" name="act" id="act" value="">
<div id="div_receipt" class="inner_box" style="top:230;margin:5;overflow:visible;display:none;">
<h2 class="info" >Rénuméroter les pièces, donner le préfixe puis le numéro</h2>
<p class="notice">Il vaut mieux que le préfixe se termine par autre chose qu'un chiffre</p>
<?php echo $hidden?>
<table>
	<TR>
	<TD>Préfixe</TD>
	<td><?php echo $prefix->input()?></td>
	</TR>
	<TR>
	<TD>A partir de </TD>
	<td><?php echo $number->input()?></td>
	</TR>
	<TR>
	<TD>Les opérations ont le même numéro (ex:relevé de banque) </TD>
	<td><?php echo $with_step->input()?></td>
	</TR>
	
</table>
<?php echo $submit?>
<?php 
   echo  HtmlInput::button('receipt_hide_bt','Annuler','onclick="$(\'div_receipt\').hide();"');
?>

</div>
<script type="text/javascript">
new Draggable('div_receipt');
</script>
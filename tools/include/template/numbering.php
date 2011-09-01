<?
echo  HtmlInput::button('receipt_bt','Renuméroter les pièces','onclick="$(\'div_receipt\').show();"');
?>
<div id="div_receipt" class="op_detail" style="top:230;margin:5;overflow:visible;display:none;">
<h2 >Rénuméroter les pièces, donner le préfixe puis le numéro</h2>
<p class="notice">Il vaut mieux que le préfixe se termine par autre chose qu'un chiffre</p>
<form method="POST" id="form1" onsubmit="return confirm('Vous confirmez ?')">
<?=$hidden?>
<table>
	<TR>
	<TD>Préfixe</TD>
	<td><?=$prefix->input()?></td>
	</TR>
	<TR>
	<TD>A partir de </TD>
	<td><?=$number->input()?></td>
	</TR>
	<TR>
	<TD>Les opérations ont le même numéro (ex:relevé de banque) </TD>
	<td><?=$with_step->input()?></td>
	</TR>
	
</table>
<?=$submit?>
<? 
   echo  HtmlInput::button('receipt_hide_bt','Annuler','onclick="$(\'div_receipt\').hide();"');
?>

</div>

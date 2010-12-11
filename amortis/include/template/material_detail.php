<h2 class="info">Modification de matériel</h2>

<span style="text-align:center;display:block;font-size:2em" id="p_card_label"  ><?= $card->strAttribut(ATTR_DEF_NAME)?></span>
<table>
<tr>
	<td>Fiche</td>
	<td><?=$card->strAttribut(ATTR_DEF_QUICKCODE)?></td>
</tr>

<tr>
	<td>Montant à amortir</td>
	<td><?=$p_amount->input()?></td>
</tr>

<tr>
	<td>Année comptable d'achat</td>
	<td> <?=$p_year->input();?></td>
</tr>
<tr>
	<td>Poste de charge dotations amortissement (débit)</td>
	<td><?=$p_deb->input()?></td>
	<td><?=$deb_span->input()?></td>
</tr>
<tr>
	<td>Poste amortissement en contrepartie</td>
	<td><?=$p_cred->input();?></td>
	<td><?=$cred_span->input();?></td>
</tr>
<tr>
	<td>Nombre d'années amortissement</td>
	<td><?=$p_number->input()?></td>
</tr>
<tr>
	<td></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
</tr>
</table>
<fieldset><legend>Annuités</legend>
<table class="result">
<th>Année</th>
<th>Montant</th>
<th>Pourcent</th>
<?
echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);
echo dossier::hidden();
$annuite=0;
for ($i=0;$i<count($array);$i++):

?>
<tr>
	<td><?=$array[$i]->ad_year?>
	</td>
	<td>
	<?
	echo HtmlInput::hidden("ad_id[]",$array[$i]->ad_id);
	$amount=new INum("amount[]");
	$amount->value=$array[$i]->ad_amount;
	echo $amount->input();	
?>

</td>
	<?
	$annuite=bcadd($annuite,$array[$i]->ad_amount);
	/*
	* COMPUTE PERCENTAGE
	*/
	bcscale(4);
	$total=bcdiv($array[$i]->ad_amount,$p_amount->value);
	$total=bcmul($total,100);
	echo td($total." %" );
	?>
</tr>


<?
endfor;
?>
</table>
Total = <?=nbm($annuite)?>
<?
if ( $annuite !=  $p_amount->value)
 {
 	echo '<h2 class="error">Différence entre le montant à amortir et le montant amorti</h2>';
 }
 ?>
</fieldset> 

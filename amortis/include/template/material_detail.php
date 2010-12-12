<h2 class="info">Modification de matériel</h2>
<form onsubmit="save_modify(this);return false">
<?=$p_card?>
<?=$a_id?>
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
	<td> <? echo $p_year->input();?></td>
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
	<td>Nombre d'années amortissement (non modifiable)</td>
	<td><? echo $p_number->input()?></td>
</tr>
<tr>
	<td>Visible <span class="notice">Y pour oui ou N pour non</span></td>
	<td><? echo $p_visible->input();?></td>
</tr>
<tr>
	<td> </td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
</tr>
</table>
<span class="notice"> En changeant le montant à amortir, l'année ou le nombre d'années, les annuités seront recalculées</span>
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
	       $pct=new INum('pct[]');
	       $pct->value=$array[$i]->ad_percentage;
               $year=new INum('ad_year[]');
               $year->value=$array[$i]->ad_year;
?>
<tr>
	<td><?=$year->input()?>
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

	echo td($pct->input() );
	?>
</tr>


<?
endfor;
?>
</table>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic">Total = <?=nbm($annuite)?></span>
<?
if ( $annuite !=  $p_amount->value)
 {
 	echo '<h2 class="error">Différence entre le montant à amortir et le montant amorti =';
	echo nbm($annuite - $p_amount->value);
	echo '<h2>';
 }
 ?>
</fieldset> 
<?
   echo HtmlInput::submit('sauver','Sauver',"onclick=\"return confirm('Vous confirmez ?')\" ");
   $rm=sprintf("remove_mat(%d,'%s',%d)",dossier::id(),$_REQUEST['plugin_code'],$value_a_id);
   echo HtmlInput::button('remove','Effacer',"onclick=\"$rm\" ");
   echo HtmlInput::button('close','Fermer',"onclick=\"removeDiv('bxmat');refresh_window()\" ");

?>
</FORM>

<h2 class="info">Détail de matériel</h2>
<span style="text-align:center;display:block;font-size:2em" id="p_card_label"  ><?= $card->strAttribut(ATTR_DEF_NAME)?></span>
<table>
<tr>
	<td>Fiche</td>
	<td><?=$card->strAttribut(ATTR_DEF_QUICKCODE)?></td>
</tr>

<tr>
	<td>Montant à amortir</td>
	<td><?=$p_amount?></td>
</tr>

<tr>
	<td>Année comptable d'achat</td>
	<td> <? echo $p_year;?></td>
</tr>
<tr>
	<td>Poste de charge dotations amortissement (débit)</td>
	<td><?=$p_deb?></td>
	<td><?=$deb_span?></td>
</tr>
<tr>
	<td>Poste amortissement en contrepartie</td>
	<td><?=$p_cred?></td>
	<td><?=$cred_span?></td>
</tr>
<tr>
	<td>Nombre d'années amortissement (non modifiable)</td>
	<td><? echo $p_number?></td>
</tr>
</table>

<fieldset><legend>Annuités</legend>
<table class="result">
<th>Année</th>
<th>Montant</th>
<th>Amortissement acté</th>
<th>Pièce </th>
<th>n°  interne</th>


<th>Pourcent</th>

<?
bcscale(2);
$annuite=0;
$done=0;
for ($i=0;$i<count($array);$i++):
?>
<tr>
	<td>
	  <?=$array[$i]->ad_year?>
	</td>
	<td>
	<?
	echo $array[$i]->ad_amount;
        ?>

</td>
	<?
	$annuite=bcadd($annuite,$array[$i]->ad_amount);

	$x=$cn->get_array('select ha_id,h_pj,jr_internal,h_amount from amortissement.amortissement_histo where a_id=$1 and h_year=$2',
	                   array($amort->a_id,$array[$i]->ad_year));
	if ( count ($x) == 1) 
	{
	$done=bcadd($done,$x[0]['h_amount']);

	echo td($x[0]['h_amount']);

	echo td($x[0]['h_pj']);

	if ( $x[0]['jr_internal'] != '' ) { 
	$jr_id=$cn->get_value('select jr_id from jrn where jr_internal=$1',array($x[0]['jr_internal']));
	echo td(HtmlInput::detail_op($jr_id,$x[0]['jr_internal']));
	} else {
	echo td();
	}
	}
	echo td($array[$i]->ad_percentage);
	?>
</tr>


<?
endfor;
?>
</table>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%">Total = <?=nbm($annuite)?></span>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%">Amorti = <?=nbm($done)?></span>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%">Reste = <?=nbm($p_amount-$done)?></span>

<?
if ( $annuite !=  $p_amount)
 {
 	echo '<h2 class="error">Différence entre le montant à amortir et le montant amorti =';
	echo nbm($annuite - $p_amount);
	echo '<h2>';
 }
 ?>
</fieldset> 
<? echo "Date ".date ('d.m.Y');?>
<? echo HtmlInput::print_window()?>
   

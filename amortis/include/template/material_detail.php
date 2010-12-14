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
<th>Amortissement acté</th>
<th>Pièce </th>
<th>n°  interne</th>


<th>Pourcent</th>

<?
bcscale(2);
echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);
echo dossier::hidden();
$annuite=0;
$done=0;
for ($i=0;$i<count($array);$i++):
	       $pct=new INum('pct[]');
	       $pct->value=$array[$i]->ad_percentage;
?>
<tr>
	<td><?=HtmlInput::hidden('ad_year[]',$array[$i]->ad_year)?>
	  <?=$array[$i]->ad_year?>
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

	$x=$cn->get_array('select ha_id,h_pj,jr_internal,h_amount from amortissement.amortissement_histo where a_id=$1 and h_year=$2',
	                   array($value_a_id,$array[$i]->ad_year));
	if ( count ($x) == 1) 
	{
	echo HtmlInput::hidden('h[]',$x[0]['ha_id']);
	
	$done=bcadd($done,$x[0]['h_amount']);
	$acte=new INum('p_histo[]');
        $acte->value=$x[0]['h_amount'];
	echo td($acte->input());

	$pj=new IText('p_pj[]');
	$pj->value=$x[0]['h_pj'];
	echo td($pj->input());

	if ( $x[0]['jr_internal'] != '' ) { 
	$jr_id=$cn->get_value('select jr_id from jrn where jr_internal=$1',array($x[0]['jr_internal']));
	echo td(HtmlInput::detail_op($jr_id,$x[0]['jr_internal']));
	} else {
	echo td();
	}
	}
	echo td($pct->input() );
	?>
</tr>


<?
endfor;
?>
</table>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%">Total = <?=nbm($annuite)?></span>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%">Amorti = <?=nbm($done)?></span>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%">Reste = <?=nbm($p_amount->value-$done)?></span>

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

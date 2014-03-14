<h2 class="title">Modification de matériel</h2>
<form onsubmit="save_modify(this);return false">
<?php echo $p_card?>
<?php echo $a_id?>
<span style="text-align:center;display:block;font-size:2em" id="p_card_label"  ><?php echo  $card->strAttribut(ATTR_DEF_NAME)?></span>
<table>
<tr>
	<td>Fiche</td>
	<td><?php echo HtmlInput::card_detail($card->strAttribut(ATTR_DEF_QUICKCODE))?></td>
</tr>

<tr>
	<td>Date Acquisition</td>
	<td><?php echo $p_date->input()?></td>
</tr>
<tr>
	<td>Montant à amortir</td>
	<td><?php echo $p_amount->input()?></td>
</tr>

<tr>
	<td>Année comptable d'achat</td>
	<td> <?php echo $p_year->input();?></td>
</tr>
<tr>
	<td>Poste de charge dotations amortissement (débit)</td>
	<td><?php echo $p_deb->input()?></td>
	<td><?php echo $deb_span->input()?></td>
</tr>
<tr>
	<td>Poste amortissement en contrepartie</td>
	<td><?php echo $p_cred->input();?></td>
	<td><?php echo $cred_span->input();?></td>
</tr>
<tr>
	<td>Nombre d'années amortissement (non modifiable)</td>
	<td><?php echo $p_number->input()?></td>
</tr>
<tr>
	<td>Visible <span class="notice">Y pour oui ou N pour non</span></td>
	<td><?php echo $p_visible->input();?></td>
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
<span class="notice"> En changeant le montant à amortir, l'année ou le nombre d'années, les annuités seront recalculées et l'historique effacé</span>
<fieldset><legend>Annuités</legend>
<table class="result">
<th>Année</th>
<th>Montant</th>
<th>Amortissement acté</th>
<th>Pièce </th>
<th>n°  interne</th>


<th>Pourcent</th>

<?php 
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
	<td><?php echo HtmlInput::hidden('ad_year[]',$array[$i]->ad_year)?>
	  <?php echo $array[$i]->ad_year?>
	</td>
	<td>
	<?php 
	echo HtmlInput::hidden("ad_id[]",$array[$i]->ad_id);
	$amount=new INum("amount[]");
	$amount->value=$array[$i]->ad_amount;
	echo $amount->input();
        ?>

</td>
	<?php 
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


<?php 
endfor;
?>
</table>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%">Total = <?php echo nbm($annuite)?></span>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%">Amorti = <?php echo nbm($done)?></span>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%">Reste = <?php echo nbm($p_amount->value-$done)?></span>

<?php 
if ( $annuite !=  $p_amount->value)
 {
 	printf ('<h2 class="error">'._("Différence entre le montant à amortir et le montant amorti = %d'".'</h2>'),nbm($annuite - $p_amount->value));
 }
 ?>
</fieldset>
<?php 
   echo HtmlInput::submit('sauver',_('Sauver'),"onclick=\"return confirm('Vous confirmez ?')\" ");
   $rm=sprintf("remove_mat(%d,'%s',%d)",dossier::id(),$_REQUEST['plugin_code'],$value_a_id);
   echo HtmlInput::button('remove',_('Effacer'),"onclick=\"$rm\" ");
   echo HtmlInput::button('close',_('Fermer'),"onclick=\"removeDiv('bxmat');refresh_window()\" ");

?>
</FORM>

<h2 class="info"><?php echo _("Détail de matériel")?></h2>
<span style="text-align:center;display:block;font-size:2em" id="p_card_label"  ><?php echo  $card->strAttribut(ATTR_DEF_NAME)?></span>
<table>
<tr>
   <td><?php echo _("Fiche")?></td>
	<td><?php echo $card->strAttribut(ATTR_DEF_QUICKCODE)?></td>
</tr>

<tr>
   <td><?php echo _("Date d'acquisition")?></td>
	<td><?php echo $p_date?></td>
</tr>

<tr>
   <td><?php echo _("Montant à amortir")?></td>
	<td><?php echo $p_amount?></td>
</tr>

<tr>
   <td><?php echo _("Année comptable d'achat")?></td>
	<td> <?php echo $p_year;?></td>
</tr>
<tr>
	<td><?php echo _("Poste de charge dotations amortissement (débit)")?></td>
	<td><?php echo $p_deb?></td>
	<td><?php echo $deb_span?></td>
</tr>
<tr>
	<td><?php echo _("Poste amortissement en contrepartie")?></td>
	<td><?php echo $p_cred?></td>
	<td><?php echo $cred_span?></td>
</tr>
<tr>
	<td><?php echo _("Nombre d'années amortissement (non modifiable)")?></td>
	<td><?php echo $p_number?></td>
</tr>
</table>

<fieldset><legend><?php echo _("Annuités")?></legend>
<table class="result">
   <th><?php echo _("Année")?></th>
   <th><?php echo _("Montant")?></th>
   <th style="text-align:right" ><?php echo _("Amortissement acté")?></th>
   <th style="text-align:center"><?php echo _("Pièce")?> </th>
   <th><?php echo _("n° interne")?></th>


   <th><?php echo _("Pourcent")?></th>

<?php 
bcscale(2);
$annuite=0;
$done=0;
for ($i=0;$i<count($array);$i++):
?>
<tr>
	<td>
	  <?php echo $array[$i]->ad_year?>
	</td>
	<td>
	<?php 
	echo nbm($array[$i]->ad_amount);
        ?>

</td>
	<?php 
	$annuite=bcadd($annuite,$array[$i]->ad_amount);

	$x=$cn->get_array('select ha_id,h_pj,jr_internal,h_amount from amortissement.amortissement_histo where a_id=$1 and h_year=$2',
	                   array($amort->a_id,$array[$i]->ad_year));
	if ( count ($x) == 1)
	{
	$done=bcadd($done,$x[0]['h_amount']);

	echo td(nbm($x[0]['h_amount']),' class="num"');

	echo td($x[0]['h_pj'],' style="text-align:center"');

	if ( $x[0]['jr_internal'] != '' ) {
	$jr_id=$cn->get_value('select jr_id from jrn where jr_internal=$1',array($x[0]['jr_internal']));
	echo td(HtmlInput::detail_op($jr_id,$x[0]['jr_internal']));
	} else {
	echo td();
	}
	}
	echo td(nbm($array[$i]->ad_percentage).'%');
	?>
</tr>


<?php 
endfor;
?>
</table>
<span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%"><?php echo _("Total")?> = <?php echo nbm($annuite)?></span>
	  <span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%"><?php echo _("Amorti")?> = <?php echo nbm($done)?></span>
	  <span style="font-size:120%;font-weight:bold;font-family:arial;font-style:italic;margin-right:10%"><?php echo _("Reste")?> = <?php echo nbm($p_amount-$done)?></span>

<?php 
if ( $annuite !=  $p_amount)
 {
   echo '<h2 class="error">'._("Différence entre le montant à amortir et le montant amorti =");
	echo nbm($annuite - $p_amount);
	echo '<h2>';
 }
 ?>
</fieldset>
<?php echo _("Date ").date ('d.m.Y');?>
<?php echo HtmlInput::print_window()?>


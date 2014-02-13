<h2 class="info">Liste  pour <?php echo $year?></h2>
<style>
@PAGE landscape {size: landscape;}
TABLE {PAGE: landscape;}
</style>
<table class="result">
<tr>
  <th><?php echo_("Code")?></th>
  <th><?php echo_("Description")?></th>
  <th><?php echo_("Date d'acquisition")?></th>
  <th><?php echo_("Année d'achat")?></th>
  <th style="text-align:right"><?php echo_("Montant à l'achat")?></th>
  <th style="text-align:right"><?php echo_("Nombre amortissement")?></th>
  <th style="text-align:right"><?php echo_("Montant à amortir")?></th>
  <th style="text-align:right"><?php echo_("Amortissement")?></th>
  <th style="text-align:right"><?php echo_("Pourcentage")?></th>
  <th style="text-align:right"><?php echo_("Reste à amortir")?></th>
</tr>
<?php 
$tot_amort=0;$tot_net=0;bcscale(2);
for ($i=0;$i < count($array) ; $i++):
	echo '<tr>';
	$fiche=new Fiche($cn,$array[$i]['f_id']);
	echo td($fiche->strAttribut(ATTR_DEF_QUICKCODE));
	echo td($fiche->strAttribut(ATTR_DEF_NAME));
	echo td(format_date($array[$i]['a_date']));

	echo td($array[$i]['a_start']);

	echo td(nbm($array[$i]['a_amount']),'style="text-align:right"');
	echo td($array[$i]['a_nb_year'],'style="text-align:right"');



	$remain=$cn->get_value("select coalesce(sum(ad_amount),0) from amortissement.amortissement_detail
			where a_id=$1 and ad_year >= $2",
		array($array[$i]['a_id'],$year));
	$amortize=$cn->get_value("select ad_amount from amortissement.amortissement_detail
			where a_id=$1 and ad_year = $2",
		array($array[$i]['a_id'],$year));
        $toamortize=bcsub($remain,$amortize);
	$tot_amort=bcadd($tot_amort,$amortize);
	$tot_net=bcadd($tot_net,$toamortize);
	$pct=$cn->get_value("select  ad_percentage from amortissement.amortissement_detail
			where a_id=$1 and ad_year = $2",
					array($array[$i]['a_id'],$year));


	echo td(nbm($remain),'style="text-align:right"');
	echo td(nbm($amortize),'style="text-align:right"');
		echo td(nbm($pct),'style="text-align:right"');
	echo td(nbm($toamortize),'style="text-align:right"');
echo '</tr>';
endfor;
?>
</table>
<hr>
<table class="result" style="width:50%;margin-left:25%">
<tr>
<?php 
  echo td(_("Acquisition de l'année"));
   $tot=$cn->get_value(" select coalesce(sum(a_amount),0) from amortissement.amortissement where a_start=$1",
			array($year));
echo td(nbm($tot),"align=\"right\"");
?>
</tr>
<tr>
<?php 
echo td(_("Amortissement"));
echo td(nbm($tot_amort),"align=\"right\"");
?>
</tr>
<tr>
<?php 
echo td(_("Valeur net "));
echo td(nbm($tot_net),"align=\"right\"");

?>
</tr>
<?php echo date('d.m.Y')?>
<form method="GET" action="extension.raw.php">
<?php echo dossier::hidden()?>
<?php echo HtmlInput::hidden('list_year',$year);?>
<?php echo HtmlInput::hidden('ac',$_REQUEST['ac']);?>
<?php echo HtmlInput::extension()?>
<?php echo HtmlInput::submit('csv','Export CSV');?>
</form>


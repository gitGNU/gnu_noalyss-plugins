<h2 class="info">Liste  pour <?=$year?></h2>
<style>
@PAGE landscape {size: landscape;}
TABLE {PAGE: landscape;}
</style> 
<table class="result">
<tr>
  <th>Code</th>
  <th>Description</th>
  <th>Année d'achat</th>
  <th style="text-align:right">Montant à l'achat</th>
  <th style="text-align:right">Nombre amortissement</th>
  <th style="text-align:right">Montant à amortir</th>
  <th style="text-align:right">Amortissement</th>
  <th style="text-align:right">Reste à amortir</th>
</tr>
<?
for ($i=0;$i < count($array) ; $i++):
	echo '<tr>';
	$fiche=new Fiche($cn,$array[$i]['f_id']);
	echo td($fiche->strAttribut(ATTR_DEF_QUICKCODE));
	echo td($fiche->strAttribut(ATTR_DEF_NAME));
	echo td($array[$i]['a_start']);

	echo td(nbm($array[$i]['a_amount']),'style="text-align:right"');
	echo td($array[$i]['a_nb_year'],'style="text-align:right"');



	$remain=$cn->get_value("select sum(ad_amount) from amortissement.amortissement_detail
			where a_id=$1 and ad_year < $2",
		array($array[$i]['a_id'],$year));
	$amortize=$cn->get_value("select ad_amount from amortissement.amortissement_detail
			where a_id=$1 and ad_year = $2",
		array($array[$i]['a_id'],$year));
        $toamortize=bcsub($remain,$amortize);
	echo td(nbm($remain),'style="text-align:right"');
	echo td(nbm($amortize),'style="text-align:right"');
	echo td(nbm($toamortize),'style="text-align:right"');
echo '</tr>';
endfor;
?>
</table>
<hr>
<?=date('d.m.Y')?>

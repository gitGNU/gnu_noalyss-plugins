<table class="result" style="width:80%;margin-left:10%">
<tr>
<th></th>
<th>Quickcode</th>
<th>Nom</th>
<th>Date acquisition</th>
<th>Année achat</th>
<th style="text-align:right">Montant Initial</th>
<th style="text-align:right">Montant Amorti</th>
<th style="text-align:right">Montant à amortir</th>

</tr>

<?
bcscale(2);
for ($i =0 ;$i < count($ret);$i++) :

  echo '<tr>';
	$fiche=new Fiche($cn,$ret[$i]->f_id);

        $detail=detail_material($ret[$i]->f_id,$fiche->strAttribut(ATTR_DEF_QUICKCODE));
        echo td($detail);
	echo td($fiche->strAttribut(ATTR_DEF_QUICKCODE));
	echo td($fiche->strAttribut(ATTR_DEF_NAME));
	echo td(format_date($ret[$i]->a_date));
	echo td($ret[$i]->a_start);
        echo td(nbm($ret[$i]->a_amount),'style="text-align:right"');
        $amortized=$cn->get_value("select sum(h_amount) from amortissement.amortissement_histo where a_id=$1",array($ret[$i]->a_id));
        $remain=bcsub($ret[$i]->a_amount,$amortized);
         echo td(nbm($amortized),'style="text-align:right"');
         echo td(nbm($remain),'style="text-align:right"');

  echo '</tr>';
endfor;
?>

</table>


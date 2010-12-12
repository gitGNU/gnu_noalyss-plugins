<table class="result" style="width:80%;margin-left:10%">
<tr>
<th></th>
<th>Quickcode</th>
<th>Nom</th>
<th>Année achat</th>
<th style="text-align:right">Montant</th>
</tr>

<?
for ($i =0 ;$i < count($ret);$i++) :

  echo '<tr>';
	$fiche=new Fiche($cn,$ret[$i]->f_id);

        $detail=detail_material($ret[$i]->f_id,$fiche->strAttribut(ATTR_DEF_QUICKCODE));
        echo td($detail);
	echo td($fiche->strAttribut(ATTR_DEF_QUICKCODE));
	echo td($fiche->strAttribut(ATTR_DEF_NAME));
	echo td($ret[$i]->a_start);
        echo td(nbm($ret[$i]->a_amount),'style="text-align:right"');
  echo '</tr>';
endfor;
?>

</table>

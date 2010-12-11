<table class="result" style="width:80%;margin-left:10%">
<tr>
<th>Quickcode</th>
<th>Nom</th>
<th>Année achat</th>
<th>Montant</th>
</tr>
<tr>
<?
for ($i =0 ;$i < count($ret);$i++) :
	$fiche=new Fiche($cn,$ret[$i]->f_id);
	echo td($fiche->strAttribut(ATTR_DEF_QUICKCODE));
	echo td($fiche->strAttribut(ATTR_DEF_NAME));
	echo td($ret[$i]->a_start);
	echo td($ret[$i]->a_amount);
endfor;
?>
</tr>
</table>
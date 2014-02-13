<br>
Filtre <?php echo HtmlInput::infobulle(400)?> <?php echo HtmlInput::filter_table("list_amortissement","0,1,2,3,4",1)?>

<table id="list_amortissement" class="sortable" style="width:80%;margin-left:10%">
<tr>
   <th><?php echo _("Quickcode")?></th>
<th class=" sorttable_sorted">
	Nom
<span id="sorttable_sortfwdind">&nbsp;&nbsp;&#x25BE;</span>
</th>
<th><?php echo _("Date acquisition")?></th>
<th><?php echo _("Année achat")?></th>
<th style="text-align:right"><?php echo _("Montant Initial")?></th>
<th style="text-align:right"><?php echo _("Montant Amorti")?></th>
<th style="text-align:right"><?php echo _("Montant à amortir")?></th>

</tr>

<?php 
bcscale(2);
for ($i =0 ;$i < count($ret);$i++) :

  echo '<tr>';
	$fiche=new Fiche($cn,$ret[$i]->f_id);

        $detail=detail_material($ret[$i]->f_id,$fiche->strAttribut(ATTR_DEF_QUICKCODE));
        echo td($detail);
	echo td($fiche->strAttribut(ATTR_DEF_NAME));
	// <td sorttable_customkey="<?php echo $row_bank['b_date']
	echo '<td sorttable_customkey="'.$ret[$i]->a_date.'">'.format_date($ret[$i]->a_date).'</td>';
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


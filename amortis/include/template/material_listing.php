<?php
/*
 * Copyright 2010 De Bontridder Dany <dany@alchimerys.be>
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>
<br>
Cherche <?php echo HtmlInput::infobulle(400)?> <?php echo HtmlInput::filter_table("list_amortissement","0,1,2,3,4",1)?>

<table id="list_amortissement" class="sortable" style="width:80%;margin-left:10%">
<tr>
<th>Quickcode</th>
<th class=" sorttable_sorted">
	Nom
<span id="sorttable_sortfwdind">&nbsp;&nbsp;&#x25BE;</span>
</th>
<th>Date acquisition</th>
<th>Année achat</th>
<th style="text-align:right">Montant Initial</th>
<th style="text-align:right">Montant Amorti</th>
<th style="text-align:right">Valeur Net Comptable</th>

</tr>

<?php 
bcscale(2);
$tot_purchase=0;$tot_amorti=0;$tot_remain=0;
        
for ($i =0 ;$i < Database::num_row($ret);$i++) :
  echo '<tr>';
        $row=$amort->next($ret,$i);
	$fiche=new Fiche($cn,$row->f_id);

        $detail=detail_material($row->f_id,$fiche->strAttribut(ATTR_DEF_QUICKCODE));
        echo td($detail);
	echo td($fiche->strAttribut(ATTR_DEF_NAME));
	// <td sorttable_customkey="<?php echo $row_bank['b_date']
	echo '<td   sorttable_customkey="'.$row->a_date.'">'.format_date($row->a_date).'</td>';
	echo td($row->a_start);
        echo td(nbm($row->a_amount),' sorttable_customkey="'.$row->a_amount.'"   style="text-align:right"');
        $amortized=$cn->get_value("select coalesce(sum(h_amount),0) from amortissement.amortissement_histo where a_id=$1",array($row->a_id));
        $remain=bcsub($row->a_amount,$amortized);
         echo td(nbm($amortized),'sorttable_customkey="'.$amortized.'" style="text-align:right"');
         echo td(nbm($remain),'sorttable_customkey="'.$remain.'"  style="text-align:right"');
         // Compute tot
         $tot_purchase=bcadd($tot_purchase,$row->a_amount);
         $tot_amorti=bcadd($tot_amorti,$amortized);
         $tot_remain=bcadd($tot_remain,$remain);
  echo '</tr>';
endfor;
?>
<tfoot>
<tr class="highlight">
<td></td>
<td></td>
<td></td>
<td></td>
<td style="text-align:right">
    <?php echo nbm($tot_purchase)?>
</td>
<td style="text-align:right">
    <?php echo nbm($tot_amorti)?>
</td>
<td style="text-align:right">
    <?php echo nbm($tot_remain)?>
</td>
</tr>
</tfoot>
</table>


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
<h2 class="info">Liste  pour <?php echo $year?></h2>
<style>
@PAGE landscape {size: landscape;}
TABLE {PAGE: landscape;}
</style>
<table class="result">
<tr>
  <th>Code</th>
  <th>Description</th>
  <th>Date d'acquisition</th>
  <th>Année d'achat</th>
  <th style="text-align:right"><?php echo _("Montant à l'achat")?></th>
  <th style="text-align:right"><?php echo _("Nombre amortissement")?></th>
  <th style="text-align:right"><?php echo _("Montant à amortir")?></th>
  <th style="text-align:right"><?php echo _("Amortissement")?></th>
  <th style="text-align:right"><?php echo _("Pourcentage")?></th>
  <th style="text-align:right"><?php echo _("Reste à amortir")?></th>
</tr>
<?php 
$tot_amort=0;$tot_net=0;bcscale(2);
$tot_purchase=0;$tot_to_amortize=0;$tot_remain=0;
for ($i=0;$i < count($array) ; $i++):
        $class=($i % 2 == 0 )?' even ':' odd ';
	echo '<tr class="'.$class.'">';
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
        $tot_purchase=bcadd($tot_purchase,$array[$i]['a_amount']);
        $tot_to_amortize=bcadd($tot_to_amortize,$amortize);
        $tot_remain=bcadd($tot_remain,$toamortize);
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
<tr class="highlight">
<td></td>
<td></td>
<td></td>
<td></td>
<td class="num">
    <?php echo nbm($tot_purchase)?>
</td>
<td></td>
<td></td>
<td class="num">
    <?php echo nbm($tot_to_amortize)?>
</td>
<td></td>
<td class="num">
    <?php echo nbm($tot_remain)?>
</td>

</tfoot>
</table>
<hr>
<table class="result" style="width:50%;margin-left:25%">
<tr>
<?php 
echo td("Acquisition de l'année");
   $tot=$cn->get_value(" select coalesce(sum(a_amount),0) from amortissement.amortissement where a_start=$1",
			array($year));
echo td(nbm($tot),"align=\"right\"");
?>
</tr>
<tr>
<?php 
echo td("Amortissement ");
echo td(nbm($tot_amort),"align=\"right\"");
?>
</tr>
<tr>
<?php 
echo td("Valeur net ");
echo td(nbm($tot_net),"align=\"right\"");

?>
</tr>
</table>
<ul class="aligned-block">
    <li>
<?php echo date('d.m.Y')?>
    </li>
    <li>
<form method="GET" action="extension.raw.php">
<?php echo dossier::hidden()?>
<?php echo HtmlInput::hidden('csv_list_year',$year);?>
<?php echo HtmlInput::hidden('ac',$_REQUEST['ac']);?>
<?php echo HtmlInput::extension()?>
<?php echo HtmlInput::submit('csv','Export CSV');?>
</form>
    </li>
    <li>
<form method="GET" action="extension.raw.php">
<?php echo dossier::hidden()?>
<?php echo HtmlInput::hidden('pdf_list_year',$year);?>
<?php echo HtmlInput::hidden('ac',$_REQUEST['ac']);?>
<?php echo HtmlInput::extension()?>
<?php echo HtmlInput::submit('csv','Export PDF');?>
</form>
</li>
</ul>
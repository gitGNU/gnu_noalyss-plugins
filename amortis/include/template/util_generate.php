<?php
/*
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
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/*!\file
 * \brief select the mat. to amortize, the ledger and the year
 */
 ?>
<form id="gen_amort" method="post">
<?php echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);?>
<?php echo HtmlInput::hidden('sa',$_REQUEST['sa']);?>
<?php echo HtmlInput::hidden('sb',$_REQUEST['sb']);?>
<?php echo dossier::hidden()?>

<table>
<tr>
<td>Année</td>
<td><?php echo $year->input();?></td>
</tr>
<tr>
<td>Journal dans lequel l'écriture sera passée</td>
<td><?php echo $sel_ledger->input()?></td>
</tr>
<tr>
<td>Date de l'opération</td>
<td><?php echo $p_date->input()?></td>
</tr>
<?php if ( isset ($f_periode)) : ?>
<tr>
	<td>
		<?php echo $f_periode?>
	</td>
	<td>
		 <?php echo $l_form_per?>
	</td>
</tr>
<?php endif; ?>
<tr>
<td>Pièce</td>
<td><?php echo $pj->input()?></td>
</tr>
</table>

Cochez ce qu'il faut amortir
<table class="result">
<tr>
	<th>Selection</th>
	<th>Quick Code</th>
	<th>Nom</th>
	<th>Description</th>
</tr>
<?php 
/*
 * get all the material
 */
$am=new Amortissement_Sql($cn);
$ret=$am->seek ("where a_visible='Y' ");
for ( $i =0;$i<Database::num_row($ret);$i++):
        $array=$am->next($ret,$i);
	echo HtmlInput::hidden('a_id[]',$array->a_id);
	$ck=new ICheckBox('p_ck'.$i);
	$fiche=new Fiche($cn,$array->f_id);

?>
<tr>
<?php 
if ( isset($_POST['p_ck'.$i])) $ck->selected=true;else $ck->selected=false;
 $view=detail_material($array->f_id,$fiche->get_quick_code());
?>
	<td><?php echo $ck->input()?></td>
	<td><?php echo $view?></td>
	<td><?php echo $fiche->strAttribut(ATTR_DEF_NAME)?></td>
	<td>	<?php echo $fiche->strAttribut(9)?></td>
	<td></td>
</tr>
<?php 
endfor;
?>
</table>
<?php echo HtmlInput::submit('generate',"Générer l'écriture");?>
</form>
<?php echo HtmlInput::button('check_all','Sélectionner tout',' onclick="select_checkbox(\'gen_amort\')"');?>
<?php echo HtmlInput::button('check_none','Tout Désélectionner ',' onclick="unselect_checkbox(\'gen_amort\')"');?>

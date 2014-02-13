<?php
/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

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
   <td><?php echo _("Année")?></td>
<td><?php echo $year->input();?></td>
</tr>
<tr>
<td><?php echo _("Journal dans lequel l'écriture sera passée")?></td>
<td><?php echo $sel_ledger->input()?></td>
</tr>
<tr>
<td><?php echo _("Date de l'opération")?></td>
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
<td><?php echo _("Pièce")?></td>
<td><?php echo $pj->input()?></td>
</tr>
</table>

 <?php echo _("Cochez ce qu'il faut amortir")?>
<table class="result">
<tr>
   <th><?php echo _("Selection")?></th>
   <th><?php echo _("Quick Code")?></th>
   <th><?php echo _("Nom")?></th>
   <th><?php echo _("Description")?></th>
</tr>
<?php 
/*
 * get all the material
 */
$am=new Amortissement_Sql($cn);
$array=$am->seek ("where a_visible='Y' ");
for ( $i =0;$i<count($array);$i++):
	echo HtmlInput::hidden('a_id[]',$array[$i]->a_id);
	$ck=new ICheckBox('p_ck'.$i);
	$fiche=new Fiche($cn,$array[$i]->f_id);

?>
<tr>
<?php 
if ( isset($_POST['p_ck'.$i])) $ck->selected=true;else $ck->selected=false;
$view_history= sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:view_history_card(\'%s\',\'%s\')" >%s</A>',
                        $fiche->id, dossier::id(), $fiche->get_quick_code());
?>
	<td><?php echo $ck->input()?></td>
	<td><?php echo $view_history?></td>
	<td><?php echo $fiche->strAttribut(ATTR_DEF_NAME)?></td>
	<td>	<?php echo $fiche->strAttribut(9)?></td>
	<td></td>
</tr>
<?php 
endfor;
?>
</table>
<?php echo HtmlInput::submit('generate',_("Générer l'écriture"));?>
</form>
<?php echo HtmlInput::button('check_all',_('Sélectionner tout'),' onclick="select_checkbox(\'gen_amort\')"');?>
<?php echo HtmlInput::button('check_none',_('Tout Désélectionner'),' onclick="unselect_checkbox(\'gen_amort\')"');?>

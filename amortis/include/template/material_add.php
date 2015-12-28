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
<h2 class="title">Ajout de matériel</h2>
<form method="POST" id="amrt_add_material" onsubmit="confirm_new_material('amrt_add_material');return false;">
<span style="text-align:center;display:block;font-size:2em" id="p_card_label"  >Nouveau matériel</span>
<table>
<tr>
	<td>Fiche</td>
	<td><?php echo $p_card->input()?><?php echo $p_card->search()?></td>
</tr>
<tr>
	<td>Date d'acquisition</td>
	<td><?php $p_date->id="amrt_date" ;echo $p_date->input()?></td>
</tr>

<tr>
	<td>Montant à amortir</td>
	<td><?php echo $p_amount->input()?></td>
</tr>

<tr>
	<td>Année comptable d'achat</td>
	<td> <?php echo $p_year->input();?></td>
</tr>
<tr>
    <td>
        Poste comptable ou fiche
    </td>
    <td>
    <?php echo $select_type->input(); ?>
    </td>
</tr>
<tr id="deb_use_account_tr_id">
	<td>Poste de charge dotations amortissement (débit)</td>
	<td><?php echo $p_deb->input()?></td>
	<td><?php echo $deb_span->input()?></td>
</tr>
<tr id="cred_use_account_tr_id">
	<td>Poste amortissement en contrepartie</td>
	<td><?php echo $p_cred->input();?></td>
	<td><?php echo $cred_span->input();?></td>
</tr>
<tr id="deb_use_card_tr_id">
	<td>Fiche de charge pour amortissement (déb) </td>
	<td><?php echo $p_card_deb->input()?><?php echo $p_card_deb->search()?></td>
</tr>
<tr id="cred_use_card_tr_id">
	<td>Fiche amortissement en contrepartie</td>
	<td><?php echo $p_card_cred->input();?><?php echo $p_card_cred->search()?></td>
</tr>
<tr>
	<td>Nombre d'années amortissement</td>
	<td><?php echo $p_number->input()?></td>
</tr>
<tr>
	<td></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
</tr>
</table>
<p style="text-align: center">
<?php 
	echo HtmlInput::button('close',_('Annuler'),"onclick=\"removeDiv('$t')\"");
	echo HtmlInput::submit('sauver',_('Sauver'));
	echo HtmlInput::extension();
	echo dossier::hidden();
        echo HtmlInput::hidden('p_new',1);

?>
</p>
</form>
    

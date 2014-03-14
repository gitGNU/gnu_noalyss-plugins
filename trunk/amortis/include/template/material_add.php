<h2 class="title">Ajout de matériel</h2>
<form method="POST" onsubmit="save_new_material(this);return false;">
<span style="text-align:center;display:block;font-size:2em" id="p_card_label"  >Nouveau matériel</span>
<table>
<tr>
	<td>Fiche</td>
	<td><?php echo $p_card->input()?><?php echo $p_card->search()?></td>
</tr>
<tr>
	<td>Date d'acquisition</td>
	<td><?php echo $p_date->input()?></td>
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
	<td>Poste de charge dotations amortissement (débit)</td>
	<td><?php echo $p_deb->input()?></td>
	<td><?php echo $deb_span->input()?></td>
</tr>
<tr>
	<td>Poste amortissement en contrepartie</td>
	<td><?php echo $p_cred->input();?></td>
	<td><?php echo $cred_span->input();?></td>
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
<?php 
	echo HtmlInput::button('close','Annuler',"onclick=\"removeDiv('$t')\"");
	echo HtmlInput::submit('sauver','Sauver',"onclick=\"return confirm('Vous confirmez ?')\" ");
	echo HtmlInput::extension();
	echo dossier::hidden();

?>
</form>

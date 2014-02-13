<h2 class="info">Ajout de matériel</h2>
<form method="POST" onsubmit="save_new_material(this);return false;">
   <span style="text-align:center;display:block;font-size:2em" id="p_card_label"  ><?php echo_("Nouveau matériel")?></span>
<table>
<tr>
   <td><?php echo_("Fiche")?></td>
	<td><?php echo $p_card->input()?><?php echo $p_card->search()?></td>
</tr>
<tr>
   <td><?php echo_("Date d'acquisition")?></td>
	<td><?php echo $p_date->input()?></td>
</tr>

<tr>
   <td><?php echo_("Montant à amortir")?></td>
	<td><?php echo $p_amount->input()?></td>
</tr>

<tr>
   <td><?php echo_("Année comptable d'achat")?></td>
	<td> <?php echo $p_year->input();?></td>
</tr>
<tr>
	<td><?php echo_("Poste de charge dotations amortissement (débit)")?></td>
	<td><?php echo $p_deb->input()?></td>
	<td><?php echo $deb_span->input()?></td>
</tr>
<tr>
	<td><?php echo_("Poste amortissement en contrepartie")?></td>
	<td><?php echo $p_cred->input();?></td>
	<td><?php echo $cred_span->input();?></td>
</tr>
<tr>
	<td><?php echo_("Nombre d'années amortissement")?></td>
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
	echo HtmlInput::button('close',_('Annuler'),"onclick=\"removeDiv('$t')\"");
echo HtmlInput::submit('sauver',_('Sauver'),"onclick=\"return confirm('Vous confirmez ?')\" ");
	echo HtmlInput::extension();
	echo dossier::hidden();

?>
</form>

<h2 class="info">Ajout de matériel</h2>
<form method="get" onsubmit="save_new_material(this)">
<table>
<tr>
	<td>Fiche</td>
	<td><?=$p_card->input()?></td>
</tr>
<tr>
	<td>Montant à amortir</td>
	<td><?=$p_amount->input()?></td>
</tr>

<tr>
	<td>Année comptable d'achat</td>
	<td> <?=$p_year->input();?></td>
</tr>
<tr>
	<td>Poste de charge dotations amortissement (débit)</td>
	<td><?=$p_deb->input()?></td>
	<td><?=$deb_span->input()?></td>
</tr>
<tr>
	<td>Poste amortissement en contrepartie</td>
	<td><?=$p_cred->input();?></td>
	<td><?=$cred_span->input();?></td>
</tr>
<tr>
	<td>Nombre d'années amortissement</td>
	<td><?=$p_number->input()?></td>
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


</form>

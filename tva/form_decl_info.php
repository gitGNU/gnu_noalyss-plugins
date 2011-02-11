<h1>Cadre I : renseignements généraux</h1>

<table>
<tr>
<td>Année</td><td> <?=$ianne?></td>
</tr>
<?if (isset ($str_date)) :?>
<tr>
<td>Date </td><td><?=$str_date?></td>
</tr>
<tr>
<td>Période début </td><td><?=$str_start?></td>
</tr>
<tr>
<td>Période fin </td><td><?=$str_end?></td>
</tr>
<?
endif;
?>
<td>N° de tva </td>
<td><?=$str_tva?></td>
</tr><tr>
<td>Nom</td><td><?=$str_name?></td>
</tr><tr>
<td>Adresse</td><td><?=$str_adress?></td>
</tr><tr>
<td>Pays code postal et localite BE</td><td><?=$str_country?></td>
</tr>
</tr>

</table>

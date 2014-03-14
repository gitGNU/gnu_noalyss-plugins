<h1>Cadre I : renseignements généraux</h1>

<table>
<tr>
<td>Année</td><td> <?php echo $ianne?></td>
</tr>
<?php if (isset ($str_date)) :?>
<tr>
<td>Date </td><td><?php echo $str_date?></td>
</tr>
<tr>
<td>Période début </td><td><?php echo $str_start?></td>
</tr>
<tr>
<td>Période fin </td><td><?php echo $str_end?></td>
</tr>
<?php 
endif;
?>
<td>N° de tva </td>
<td><?php echo $str_tva?></td>
</tr><tr>
<td>Nom</td><td><?php echo $str_name?></td>
</tr><tr>
<td>Adresse</td><td><?php echo $str_adress?></td>
</tr><tr>
<td>Pays code postal et localite BE</td><td><?php echo $str_country?></td>
</tr>
</tr>

</table>

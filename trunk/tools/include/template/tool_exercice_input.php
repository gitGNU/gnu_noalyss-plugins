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

/**
 * @file
 * @brief Input data for new Exercice
 *
 */
?>
<form method="POST" onsubmit="return validate()">
	<table>
		<tr>
			<td>
				Exercice
			</td>
			<td>
				<?php echo $exercice->input()?>
			</td>
		</tr>
		<tr>
			<td>
				Année
			</td>
			<td>
				<?php echo $year->input()?>
			</td>
		</tr>
		<tr>
			<td>
				Nombre de mois
			</td>
			<td>
				<?php echo $nb_month->input()?>
			</td>
		</tr>
		<tr>
			<td>
				A partir du mois de
			</td>
			<td>
				<?php echo $from->input()?>
			</td>
		</tr>
	</table>
<?php 
echo HtmlInput::submit('save','Valider');
echo HtmlInput::request_to_hidden(array('sa','ac','plugin_code','gDossier'));
?>
</form>
<script charset="UTF8" lang="javascript">
	function validate()
	{
		if (trim($('<?php echo $exercice->id?>').value)=='') {
						$('<?php echo $exercice->id?>').style.borderColor='red';
						alert('Exercice invalide');
						return false;
					}
		if (trim($('<?php echo $nb_month->id?>').value)=='') {
						$('<?php echo $nb_month->id?>').style.borderColor='red';
						alert('Nombre de mois invalide');
						return false;
					}
		if (trim($('<?php echo $year->id?>').value)=='') {
						$('<?php echo $year->id?>').style.borderColor='red';
						alert('Année invalide');
						return false;
					}
	}
</script>
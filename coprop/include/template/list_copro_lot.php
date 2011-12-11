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
 * @brief show list between lot et copropriétaire
 *
 */
$gDossier=Dossier::id();
?>
<table class="result">
	<tr>
		<th> Nom copropriétaire </th>
		<th>
			Lot(s)
		</th>
	</tr>
<?
for ($i=0;$i<count($a_copro);$i++):
	$col_copro=HtmlInput::anchor($a_copro[$i]['copro_name'],"","mod_coprop($gDossier,'".$a_copro[$i]['jcl_copro']."','".$_REQUEST['plugin_code']."'");
?>
	<tr>
		<td><?=col_copro?></td>
	</tr>
	<td>
		<?
			$rlot=$cn->execute('lot',$a_copro[$i]['jcl_id']);
			$max=Database::num_row($rlot);
			$sp="";
			for ($e=0;$e<$max;$e++):
				$row=Database::fetch_array($rlot,$e);
				$js_lot=HtmlInput::card_detail($row['lot_qcode'],$row['lot_name']);
				echo $js_lot.$sp;
				$sp=" , ";
			endfor;
		?>

	</td>
</tr>
<?
endfor;
?>

</table>
<?
echo HtmlInput::button("add_link","Ajout Copropriétaire / lot ","onclick=\"add_coprop('$gDossier')\"");
?>
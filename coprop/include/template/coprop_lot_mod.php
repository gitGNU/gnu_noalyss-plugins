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
 * @brief included from ajax_mod_copro_lot.php
 *
 */

?>
<h1>
	Modification<?=HtmlInput::card_detail($copro->strAttribut(ATTR_DEF_QUICKCODE),$copro->strAttribut(ATTR_DEF_NAME),' class="line"')?>
</h1>
<h2>Lot affecté à ce copropriétaire</h2>
<form method="post" onsubmit="return confirm('Vous confirmez ?')">
		<?=HtmlInput::hidden("copro_id",$copro->id)?>
<table class="result">
	<tr>
		<th>
			Lot
		</th>
		<th>
			Description
		</th>
	</tr>
<? for ($i=0;$i<count($acurrent);$i++): ?>
	<tr id="row<?=$acurrent[$i]['l_id']?>">
		<td>
			<?=HtmlInput::card_detail($acurrent[$i]['fiche_qcode'],$acurrent[$i]['fiche_name'],' class="line"')?>
		</td>
		<td>
                    <?=$acurrent[$i]['fiche_desc']?>
		</td>
		<td id="col<?=$acurrent[$i]['l_id']?>">
			<?
			$js="onclick=remove_lot('".$_REQUEST['plugin_code']."','".$_REQUEST['ac']."','".$_REQUEST['gDossier']."','".$acurrent[$i]['l_id']."')";
			echo HtmlInput::anchor("enlever","",$js);
			?>
		</td>
	</tr>
<? endfor; ?>
</table>
<?=HtmlInput::submit('updexist',"Mise à jour")?>
</form>
<hr>
<h2>Autre lots</h2>
<form method="post" onsubmit="return confirm('Vous confirmez ?')">
	<?=HtmlInput::hidden("copro_id",$copro->id)?>
<table class="result">
	<tr>
		<th>
			Lot
		</th>
		<th>
			Description
		</th>
	</tr>
<? for ($i=0;$i<count($not_assigned);$i++): ?>
	<tr >
		<td>
                    		<?=HtmlInput::card_detail($not_assigned[$i]['fiche_qcode'],$not_assigned[$i]['fiche_name'],' class="line"')?>
		</td>
		<td>
                    <?=$not_assigned[$i]['fiche_desc']?>
		</td>
		<td>
			<?
				$ck=new ICheckBox("lot[]");
				$ck->value=$not_assigned[$i]['f_id'];
				echo $ck->input();
			?>
		</td>
	</tr>
<? endfor; ?>
</table>
<?=HtmlInput::submit('addlot',"Ajout des lots sélectionnés")?>
</form>

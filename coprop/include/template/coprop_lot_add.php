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
 * @brief ajout lien entre copro et lot
 *
 */
  $copro=new ICard();
  $copro->label="Copropriétaire : ".HtmlInput::infobulle(0) ;
  $copro->name="w_copro";
  $copro->tabindex=1;
  $copro->value="";
  $copro->table=0;

 // name of the field to update with the name of the card
  $copro->set_attribute('label','w_copro_label');
  // Type of card : deb, cred,
  $copro->set_attribute('typecard',$g_copro_parameter->categorie_coprop);

  $copro->extra=$g_copro_parameter->categorie_coprop;

// Add the callback function to filter the card on the jrn
  $copro->set_callback('filter_card');
  $copro->set_attribute('ipopup','ipopcard');
// when value selected in the autcomplete
  $copro->set_function('fill_data');

// when the data change
  $copro->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
            $copro->name);
  $copro->set_dblclick("fill_ipopcard(this);");

  $copro_label=new ISpan();
  $copro_label->table=0;
  $f_copro=$copro_label->input("w_copro_label","");

// Search button for card
  $f_copro_bt=$copro->search();


echo '<form method="post" onsubmit="return confirm(\'Vous validez ?\')">';
echo h2("Affectation de lot à un copropriétaire",'class="info"');
echo "Copropriétaire ".$copro->input();
echo $f_copro_bt;
echo $f_copro;
echo HtmlInput::hidden('p_jrn',$g_copro_parameter->journal_appel);
echo HtmlInput::hidden('ledger_type','ODS');
?>
<table>
	<tr>
		<th>Lot</th>
		<th>Pourcentage</th>
	</tr>
<?
for ($i=0;$i<20;$i++):
	$lot=new ICard();
  $lot->label="Lot : ".HtmlInput::infobulle(0) ;
  $lot->name="w_lot".$i;
  $lot->tabindex=1;
  $lot->value="";
  $lot->table=0;

 // name of the field to update with the name of the card
  $lot->set_attribute('label','w_lot_label'.$i);
  // Type of card : deb, cred,
  $lot->set_attribute('typecard',$g_copro_parameter->categorie_lot);

  $lot->extra=$g_copro_parameter->categorie_lot;

// Add the callback function to filter the card on the jrn
  $lot->set_callback('filter_card');
  $lot->set_attribute('ipopup','ipopcard');
// when value selected in the autcomplete
  $lot->set_function('fill_data');

// when the data change
  $lot->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
            $lot->name);
  $lot->set_dblclick("fill_ipopcard(this);");

  $lot_label=new ISpan();
  $lot_label->table=0;
  $f_lot=$lot_label->input("w_lot_label".$i,"");

// Search button for card
  $f_lot_bt=$lot->search();
  $num=new INum("lot_per".$i);
?>
<tr>
	<td>
	<?=$f_lot_bt?>
	<?=$lot->input()?>
	</td>
	<td>
		<?=$num->input()?>
	</td>
	<td>

		<?=$f_lot?>
	</td>
</tr>
<? endfor;?>
</table>
<? echo HtmlInput::submit('copro_new','Sauver');?>
<? echo HtmlInput::button("add_link","Retour à la liste sans sauver ","onclick=\"copro_show_list()\"");?>
</form>

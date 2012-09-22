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
 * @brief show the definition
 *
 */
$type_row=$cn->make_array("select p_type,p_description from rapport_advanced.type_row order by p_description");
$type_periode=$cn->make_array("select t_id,t_description from rapport_advanced.periode_type order by t_description");
?>
<table id="table_formulaire_definition_id" class="result" style="table-layout: auto">
	<thead>
	<tr>
	<th style="width:92px">
		Code <?=HtmlInput::infobulle(200)?>
	</th>
	<th>
		Libellé
	</th>
	<th style="width:192px">
		Type de ligne
	</th>
	<th>
		Période
	</th>
	<th style="width:92px">
		Ordre d'apparition
	</th>
	<th>
		Message d'aide
	</th>
	</tr>
	</thead>
	<tbody id="table_body_id">
	<? for ($i=0;$i<$max;$i++):?>
	<tr id="row_<?=$i?>">

		<td>
			<?=HtmlInput::hidden('p_id[]',$this->definition[$i]->p_id) ?>
			<?
				$p_code=new IText('p_code[]',$this->definition[$i]->p_code);
				$p_code->size="10";
				echo $p_code->input();
			?>
		</td>
		<td>
			<?
				$p_libelle=new IText('p_libelle[]',$this->definition[$i]->p_libelle);
				$p_libelle->css_size="100%";
				echo $p_libelle->input();
			?>
		</td>
		<td>
			<?
				$p_type=new ISelect('p_type[]');
				$p_type->value=$type_row;
				$p_type->selected=$this->definition[$i]->p_type;
				echo $p_type->input();
			?>
		</td>
		<td>
			<?
				$p_type_periode=new ISelect('t_id[]');
				$p_type_periode->value=$type_periode;
				$p_type_periode->selected=$this->definition[$i]->t_id;
				echo $p_type_periode->input();
			?>
		</td>
		<td>
			<?
				$p_order=new INum('p_order[]',$this->definition[$i]->p_order);
				$p_order->prec=0;
				$p_order->size=4;
				echo $p_order->input();
			?>
		</td>
		<td>
			<?
				$p_info=new IText('p_info[]',$this->definition[$i]->p_info);
				$p_info->css_size="100%";
				echo $p_info->input();
			?>
		</td>
	</tr>
	<?endfor;?>
	</tbody>
</table>
<?=HtmlInput::button_anchor("Ajout d'une ligne","javascript:void(0)","add_def",
		sprintf('onclick="add_row_definition(\'%s\',\'%s\',\'%s\')"',$_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier']))?>

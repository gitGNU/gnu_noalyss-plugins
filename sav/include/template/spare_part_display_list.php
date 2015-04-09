<?php
/*
 * * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * 
 */
global $g_sav_parameter;

/**
 * @brief show a table containing the spare part from a Repair_Card
 * @param $p_repair_card Service_After_Sale
 */
$gDossier=Dossier::id();
$ac=HtmlInput::default_value_request("ac", -1);
$plugin_code=HtmlInput::default_value_request("plugin_code", -1);
?>
<table id="spare_part_table_list_id">
    <tr>
        <th>
            <?php echo _('Code')?>
        </th>
        <th>
            <?php echo _('Description') ?>
        </th>
        <th>
            <?php echo _('Quantité') ?>
        </th>
    </tr>
    <?php for ($i=0;$i < $count_spare;$i++) : ?>
    <?php
        $spare=new Sav_Spare_Part($a_spare[$i]['id']);
       echo $spare->print_row();
    ?>
    <?php endfor; ?>
    <tr id="add_spare_tr_id">
     <td>
            <?php 
            $input_fiche_materiel=new ICard('spare_part_id');
            $input_fiche_materiel->set_dblclick('fill_ipopcard(this)');
            $input_fiche_materiel->set_function('fill_data');
            $sql=' select fd_id from fiche_def where fd_id in ('.$g_sav_parameter->get_spare_part().')';
            $filter=$cn->make_list($sql);
            $input_fiche_materiel->set_attribute('typecard', $filter);
            $input_fiche_materiel->extra=$filter;
            echo $input_fiche_materiel->input()." ".$input_fiche_materiel->search();
            ?>        
        </td>
        <td>
            <?php
            $quant=new INum('spare_part_quant');
            echo $quant->input();
            ?>
        </td>
      
        <td>
            <?php
               echo HtmlInput::button_action(_('Ajout'), 
                       sprintf('spare_part_add(\'%s\',\'%s\',\'%s\',\'%s\')',$gDossier,$ac,$plugin_code,$p_repair_card));
            ?>
            
        </td>
    </tr>
</table>    

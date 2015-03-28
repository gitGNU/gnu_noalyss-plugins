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

global $cn,$ac,$plugin_code,$gDossier;

/**
 * @brief 
 */
?>
<table id="workhour_table_id">
    <tr>
        <th>
            <?php echo _('Description')?>
        </th>
        <th>
            <?php echo _('Quantité')?>
        </th>
        <th>
        </th>
    </tr>
    <?php
        for ($i=0;$i<$count_workhour;$i++):
            $workour=new Sav_WorkHour($a_workhour[$i]['id']);
        ?>
            <?php echo $workour->print_row();?>
    <?php        
        endfor;
    ?>
    <tr id="add_workhour_tr_id">
     <td>
            <?php 
                $desc=new IText('workhour_description');
                echo $desc->input();
            ?>        
        </td>
        <td>
            <?php
            $quant=new INum('workhour_quant');
            echo $quant->input();
            ?>
        </td>
      
        <td>
            <?php
               echo HtmlInput::button_action(_('Ajout'), 
                       sprintf('workhour_add(\'%s\',\'%s\',\'%s\',\'%s\')',$gDossier,$ac,$plugin_code,$p_repair_id));
            ?>
            
        </td>
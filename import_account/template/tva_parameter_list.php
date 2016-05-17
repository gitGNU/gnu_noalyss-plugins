<?php

/*
 * * Copyright (C) 2016 Dany De Bontridder <dany@alchimerys.be>
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
$nb=count($ret);
/**
 * @file
 * @brief show the tva parameter 
 * @see Impacc_TVA::display_list
 */
?>
<table class="result">
    <tr>
        <th>
            <?php echo _("Label TVA Noalyss") ?>
            
        </th>
        <th>
            <?php echo _("Code TVA du CSV") ?>
        </th>
        <th>
            <?php echo _("Description TVA Noalyss") ?>
            
        </th>
        <th>
            <?php echo _("Taux TVA") ?>
            
        </th>
    </tr>
    <?php for ($i=0;$i<$nb;$i++):?>
    <tr id="row<?php echo $ret[$i]['pt_id']?>">
        
        <td>
            <?php echo HtmlInput::anchor(h($ret[$i]['tva_label']), "", sprintf("onclick=\"tva_parameter_modify('%s')\"",$ret[$i]["pt_id"]))?>
        </td>
        <td>
            <?php echo $ret[$i]['tva_code']?>
        </td>
        </td>
        <td>
            <?php echo h($ret[$i]['tva_comment'])?>
        </td>
        <td>
            <?php echo h($ret[$i]['tva_rate'])?>
        </td>
        <td>
            <?php
            echo HtmlInput::anchor(_("Efface"), "", sprintf("onclick=\"tva_parameter_delete('%s')\"",$ret[$i]['pt_id']));
            ?>
        </td>
    </tr>
    <?php endfor;?>
</table>
<?php
    echo HtmlInput::button_action(_("Ajout"), "tva_parameter_add()");
?>
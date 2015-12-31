<?php

/* 
 * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
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
 */
?>
<table class="result">
    <tr>
        <th>id</th>
        <th>
            <?php echo _('Envoyé par');?>
        </th>
        <th>
            <?php echo _('Sujet');?>
        </th>
        <th></th>
    </tr>
    <?php 
        $nb=count($array);
        $dossier=Dossier::id();
        $plugin_code=HtmlInput::default_value_request("plugin_code", "");
        $ac=HtmlInput::default_value_request("ac", "");
        for ($i= 0 ; $i < $nb ; $i++):
            $class=($i%2==0)?' even ':' odd ';
            // javascript select message
            $js=sprintf("inv_select_message('%s','%s','%s','%s')",
                    $dossier,$ac,$plugin_code,$array[$i]->getp("id"));
            // javascript display detail message
            $js_detail=sprintf("inv_display_message('%s','%s','%s','%s')",
                    $dossier,$ac,$plugin_code,$array[$i]->getp("id"));
                    
    ?>
    <tr id="tr_message<?php echo $array[$i]->getp("id")?>" <?php echo 'class="'.$class.'"';?> >
        <td>
            <?php echo $array[$i]->getp("id")?>
        </td>
        <td>
            <?php echo HtmlInput::anchor($array[$i]->getp('sender'),""
                    ,' onclick="'.$js.'"');?>
        </td>
        <td>
            <?php echo HtmlInput::anchor($array[$i]->getp('subject'),""
                    ,' onclick="'.$js.'"');?>

        </td>
        <td>
            <?php echo HtmlInput::anchor(_('Détail'),"",
                    ' onclick="'.$js_detail.'"');
            ?>
        </td>
    </tr>
    <?php
            endfor;
    
    ?>
</table>
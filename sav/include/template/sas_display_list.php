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

/**
 * @file
 * @brief this template is called from Service_After_Sale::display_list and 
 * aim to list the content
 */
$count_listing=count($listing);
$plugin_code=HtmlInput::default_value_request("plugin_code", "");
$ac=HtmlInput::default_value_request("ac", "");
$gDossier=HtmlInput::default_value_request("gDossier", "0");
?>
<div class="content" id="sav_list_div">
    <table class="result" id="sav_list">
        <?php
        for ($i=0; $i<$count_listing; $i++) :
            $row=$listing[$i];
            $class=($i%2==0)?' even':' odd ';
            ?>
            <tr class="<?php echo $class;?>">

                <td>
                    <?php echo $row['str_date_reception']; ?>
                </td>
                <td>
                    <?php echo $row['repair_number']; ?>
                </td>
                <td>
                    <?php echo $row['name']; ?>
                </td>
                <td>
                    <?php echo $row['customer_qcode']; ?>
                </td>
                <td>
                    <?php echo $row['short_description']; ?>
                </td>
                <td>
                    <?php echo $row['garantie']; ?>
                </td>
                <td>
                    FACTURE
                </td>
                <td>
                    <?php
                    $url="do.php?".http_build_query(array('ac'=>$ac,'plugin_code'=>$plugin_code,"gDossier"=>$gDossier,'sb'=>'detail','sa'=>'enc','repair_card_id'=>$row['id']));
                    echo HtmlInput::anchor(_('Détail'), $url);
                    ?>
                </td>
            </tr>

        <?php endfor; ?>
    </table>
</div>
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

$gDossier=Dossier::id();
$ac=HtmlInput::default_value_request("ac", "#");
$plugin_code=HtmlInput::default_value_request("plugin_code", "#");
/**
 * @file
 * @brief  Show list of imported file
 * @param $array
 */
?>
<table class="result sortable">
    <tr>
        <th>
            <?php echo _("Fichier")?>
        </th>
        <th>
            <?php echo _("Type")?>
        </th>
        <th>
            <?php echo _("Date import")?>
        </th>
        <th>
            <?php echo _("Date Transfert")?>
        </th>
    </tr>
    <?php
        $nb=count($array);
        for ($i=0;$i<$nb;$i++):
    ?>
    <tr id="row<?php echo $array[$i]["id"]?>">
        <td>
            <?php 
            $url="?".http_build_query(
                    array("gDossier"=>$gDossier,
                        "sa"=>"hist",
                        "ac"=>$ac,
                        "id"=>$array[$i]["id"]
                        )
                    );
            echo HtmlInput::anchor($array[$i]['i_filename'],$url);
            
            ?>
        </td>
        <td>
            <?php echo h($array[$i]['i_type'])?>
        </td>
      <!--  <td>
            <?php echo h($array[$i]['simport'])?>
        </td>
      -->
        <td sorttable_customkey="<?=$array[$i]['sorder_transfer']?>" >
            <?php echo h($array[$i]['stransfer'])?>
        </td>
        <td>
              <?php
            echo HtmlInput::anchor(_("Efface"), "", sprintf("onclick=\"history_delete('%s')\"",$array[$i]['id']));
            ?>
        </td>
    </tr>
    <?php endfor; ?>
</table>

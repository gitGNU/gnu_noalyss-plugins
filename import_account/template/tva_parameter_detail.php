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
require_once NOALYSS_INCLUDE."/lib/class_itva_popup.php";
$gDossier=Dossier::id();
/**
 * @file
 * @brief Display detail of the TVA
 * @param  $tva_id=""; 
 * @param  $comment="";
 * @param  $tva_code="";
 * @param  $id=-1;
 * @param  $label;
 * @see Impacc_TVA::display_modify
 * @see Impacc_TVA::display_add
 */
echo HtmlInput::title_box(_("TVA"), "tva_detail_id");
?>
<script>

</script>    
<form method="post" onsubmit="return check_param_tva();return false;">
    <ol style="list-style-type: none">
        <ul>
            <?php  echo _("TVA Noalyss") , " " ;
            $tva_popup=new ITva_Popup("tva_id",$tva_id);
            $tva_popup->set_attribute("gDossier", $gDossier);
            echo $tva_popup->input();
            echo $label," ";
            echo $comment;
            ?>
        </ul>
        <ul>
            <?php  echo _("Code TVA")      ,' ';
            $tva_code=new IText("tva_code",$tva_code);
            echo $tva_code->input();
            ?>
        </ul>
    </ol>
    <?php
    
    echo HtmlInput::request_to_hidden(array("gDossier","ac","plugin_code","sa"));
    echo HtmlInput::hidden("pt_id", $id);
    ?>
    <ul class="aligned-block">
        <li>
            <?php echo HtmlInput::submit("save", _("Valider"));?>
        </li>
        <li>
            <?php echo HtmlInput::button_close("tva_detail_id");?>
        </li>
    </ul>
</form>

    
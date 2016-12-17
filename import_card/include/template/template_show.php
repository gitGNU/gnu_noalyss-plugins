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
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
?>

<div id="template_format" style="display:none">
    <?php
        echo _("Choix modèle"),$select_template->input();
    ?>
    <ul class="aligned-block">
        <li><?php echo HtmlInput::button("close", _("Fermer"),'onclick="$(\'template_format\').hide();$(\'show_template\').show()"');?></li>
        <li><?php echo HtmlInput::button("template_use", _("Utiliser"),'onclick="$(\'template_format\').hide();Format.apply();$(\'show_template\').show()"');?></li>
        
    </ul>
</div>
<p>
    <?php 
        $button = new IButton ("show_template",_("Utiliser un modèle d'import"));
        $button->javascript="$('template_format').show();$('show_template').hide()";
        echo $button->input()
    ?>
        
        
</p>
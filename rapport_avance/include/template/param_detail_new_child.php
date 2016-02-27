<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief add parameter for declaration
 *
 */
?>
<ul class="tabs" >

    <li class="tabs" id="new_account_id_bt">
            Poste comptable et sous-postes
    </li>

</ul>        
<div style="width:100%;margin:1px">
    <span class="error" id="param_detail_info_div"></span>

    <div style="padding: 10px">

    </div>
    <div id="new_account_id" style="display: block">
        <form id="new_paded" method="POST" onsubmit="save_param_detail('new_paded');return false">
            <input type="hidden" value="1" name="child" id="child">
            <p>
            <?php echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code',
    'p_id')) ?>

            <?php echo HtmlInput::hidden('tab', 'new_account_id') ?>
            <?php echo RAPAV_Account::new_row($p_id) ?>
            Une ligne par poste comptable qui dépend du poste donné en (1)
            </p>
            <p>
            <?php echo HtmlInput::submit('save', 'Sauve') ?>
                </p>
        </form>
    </div>
</div>

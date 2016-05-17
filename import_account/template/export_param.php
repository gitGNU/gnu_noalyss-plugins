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
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

/**
 * @file
 * @brief Form to enter date and ledger for export
 * @see Impacc_export::input_param
 */
?>
<div class="content">
<form method="GET" action="extension.raw.php">
    <?php echo HtmlInput::array_to_hidden(array('gDossier','ac','plugin_code','sa'), $_REQUEST);?>
    <ul class="aligned-block">
        <li>
            <?php echo _('Journal'); ?>
            <?php echo $select_ledger->input()?>
        </li>
        <li>
            <?php echo _("Depuis")?>
            <?php echo $date_start->input();?>
        </li>
        <li>
            <?php echo _("jusque")?>
            <?php echo $date_end->input();?>
        </li>
    </ul>
    <?php echo HtmlInput::submit("export_operation", _("Export"));?>
</form>
</div>
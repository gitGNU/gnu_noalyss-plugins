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

if (!defined('ALLOWED'))     die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * First screen for import , ask for the file and the format (CSV , FEC or XML)
 */
?>
<script>
function ctl_display() {
            if ($('format_sel').value==1) 
            {$('csv_div_id').show();} else {$('csv_div_id').hide();};
}    
</script>
<form method="POST" enctype="multipart/form-data" >
    <?php echo HtmlInput::array_to_hidden(array('gDossier','ac','plugin_code','sa'), $_REQUEST)?>
    <?php
        echo _('Fichier'),
                $file->input();
    ?>
    <?php 
    echo _("Format"),$format->input();
    ?>
    <div id="csv_div_id" style="display: none">
    <?php 
    $csv=new Impacc_CSV();
    $csv->input_format();
    ?>
    </div>
<?php
    echo HtmlInput::submit("upload", _("Sauve"))
?>
</form>


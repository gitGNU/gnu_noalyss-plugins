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

// Copyright Author Dany De Bontridder danydb@aevalys.eu
?>
<form method="GET" action="extension.raw.php" onsubmit="return check_form()">
    <?php
    echo HtmlInput::get_to_hidden(array('gDossier','sa','ac','plugin_code'));
    echo HtmlInput::hidden("act","export_operation");
    $date_from=new IDate('p_from');
    $date_from->id='p_start_date_id';
    $date_to=new IDate('p_to');
    $date_to->id="p_end_date_id";
    ?>
    <?php echo _("Date début")?>  <?php echo $date_from->input()?>
    <?php echo _("Date fin")?>  <?php echo $date_to->input()?>
    <p>
    <?php
    echo HtmlInput::submit("act_sb",_("Export CSV"));
    ?>
    </p>
</form>
<script>
   
    function check_form()
    {
       if ($('p_start_date_id').value == "" || ! check_date ($('p_start_date_id').value)) {
            alert('Date incorrect');
            $('p_start_date_id').style.borderColor = "red";
            return false;
        }
        if ($('p_end_date_id').value == "" || ! check_date($('p_end_date_id').value)) {
            alert('Date incorrect');
            $('p_end_date_id').style.borderColor = "red";
            return false;
        }
       
        return true;
    }
</script>    
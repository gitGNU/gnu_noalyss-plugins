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
// Copyright 2016 Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief Show the condition of a listing
 * @see from RAPAV_Listing::display_condition
 * @param $a_condition , $nb_condition
 */

?>
<ul style="padding-left: 2px">
    <?php for ($i=0;$i < $nb_condition ; $i++):?>
    <li>
        
       <?php $msg= $a_condition[$i]['lp_code']." ".RAPAV_Condition::$a_operator[$a_condition[$i]['c_operator']]['label']." ".$a_condition[$i]['c_value'] ;
       echo HtmlInput::anchor($msg, "",sprintf("onclick=\"listing_condition_input('%s','%s','%s','%s','%s')\"",
                $_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$p_listing_id,$a_condition[$i]['id']));
       
       ?>
       <?php
       $delete_action= sprintf("listing_condition_remove('%s','%s','%s','%s')",
                $_REQUEST['plugin_code'],
                $_REQUEST['ac'],
                $_REQUEST['gDossier'],
                $a_condition[$i]['id']);
       
        echo HtmlInput::button_action("X", $delete_action, "","tinybutton");
       ?>
    </li>
    <?php endfor;?>
</ul>
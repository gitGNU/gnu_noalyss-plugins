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

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require_once 'include/class_rapav_condition.php';

/**
 * @file
 * @brief 
 * Input a new condition for current listing
 */
$select_code=new ISelect('lp_id');
$select_code->rowsize = 5;
$select_code->value=$cn->get_array('select lp_id as value,lp_code as label 
            from rapport_advanced.listing_param 
            where l_id=$1 
           
            order by lp_code
            ',
        array($l_id));

$condition=new RAPAV_Condition($lc_id);
$select_code->selected=$condition->get_listing_param();

$value=new IText('c_value',$condition->get_value());
echo HtmlInput::title_box("Ajout condition", "listing_condition_add");
?>

<form id ="listing_condition_input_frm" method="get" onsubmit="listing_condition_save();return false;">
    <?php
    echo HtmlInput::get_to_hidden(array('gDossier','plugin_code','ac','l_id','lc_id'));
    ?>
    <table>
        <tr>
            <td>
                Code
            </td>
            <td>
                <?php echo $select_code->input();?>
            </td>
        </tr>
        <tr>
        <td>
            Opérateur
        </td>
        <td>
            <?php echo $condition->html_select();?>
        </td>
        </tr>
        <tr>
            <td>
                Valeur
            </td>
            <td>
                <?php echo $value->input();?>
            </td>
        </tr>
    </table>
<?php echo HtmlInput::submit("listing_condition_save_sbm","Sauver");        
?>    
</form>
<?php
/*
 * Copyright 2010 De Bontridder Dany <dany@alchimerys.be>
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
?>
    <?php
/**
 * Liste tous les budgets inclus
 * @see Budget::to_list
 */
?>

<table class="result">
    <tr>
        <th> Nom </th>
        <th> Exercice </th>
        <th> Type de budget </th>
        <th style="text-align: right"> Montant </th>
    </tr>
<?php 
for ($i=0;$i<count($array);$i++):
    $class=($i%2==0)?' class="evend" ':' class="odd" ';
    $str_js=sprintf(" onclick=\"budget_detail('%s','%s','%s','%s');  \"",
            $_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$array[$i]['b_id']);
    $js=HtmlInput::anchor("Détail","",$str_js);
	$str_js_del=sprintf(" onclick=\"budget_remove('%s','%s','%s','%s');\" ",
            $_REQUEST['plugin_code'],$_REQUEST['ac'],$_REQUEST['gDossier'],$array[$i]['b_id']);
    $js_del=HtmlInput::anchor("Effacer","",$str_js_del);
?>
    <tr id="row<?php echo $array[$i]['b_id']?>" <?php echo $class?> >
        <td>
            <?php echo $array[$i]['b_name']?>
        </td>
         <td>
            <?php echo $array[$i]['b_exercice']?>
        </td>
        <td>
            <?php echo $array[$i]['str_type']?>
        </td>
        <td CLASS="num">
            <?php echo nbm($array[$i]['b_amount'])?>
        </td>
        <td  id="col1<?php echo $array[$i]['b_id']?>">
            <?php echo $js?>
        </td>
        <td  id="col2<?php echo $array[$i]['b_id']?>">
            <?php echo $js_del?>
        </td>
    </tr>
<?php 
endfor;
?>

</table>

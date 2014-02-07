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
/**
 * @brief Display result of insert, 
 * parameters are $ret (return of seek) and $a_listing  (Intervat_Client_SQL )
 @see intervat_listing_assujetti_step_2.inc.php
 */
global $g_succeed,$g_failed;
$nb=Database::num_row($ret);
?>
<table class="result">
    <tr>
        <th>
            <?php echo _('Nom'); ?>
        </th>
        <th>
            <?php echo _('Numéro TVA'); ?>
        </th>
        <th>
            <?php echo _('Montant'); ?>
        </th>
        <th>
            <?php echo _('TVA'); ?>
        </th>
        <th>
            <?php echo _('Commentaire'); ?>
        </th>
        <th>
            
        </th>
    </tr>
<?php
    for ($i=0;$i<$nb;$i++):
        $data=$a_listing->next($ret,$i);
        $js=sprintf('modify_intervat_assujetti(\'%s\',\'%s\',\'%s\',\'%s\')',
                $_REQUEST['gDossier'],$_REQUEST['ac'],$_REQUEST['plugin_code'],$data->c_id);
        $class=($i%2==0)?'odd':'even';
?>
    <tr id="tr_<?php echo $data->c_id?>" class="<?php echo $class; ?>">
        <td>
            <?php 
            echo h($data->c_name);
            ?>
        </td>
        <td>
            <?php 
            echo h($data->c_vatnumber);
            ?>
        </td>
        <td>
            <?php 
            echo h($data->c_amount_novat);
            ?>
        </td>
        <td>
            <?php 
            echo h($data->c_amount_vat);
            ?>
        </td>
        <td>
            <?php 
            $code= ($data->c_comment=="")?$g_succeed:$g_failed;
            echo $code.h($data->c_comment);
            ?>
        </td>
        <td>
            <a class="line" href="javascript:void(0)" onclick="<?php echo $js; ?>"><?php echo _('Modifier')?></a>
        </td>
    </tr>
    <?php 
    endfor;
    ?>
</table>
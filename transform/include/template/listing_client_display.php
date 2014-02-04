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
$nb=Database::num_row($ret);
?>
<table>
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
            Action
        </th>
    </tr>
<?php    
    for ($i=0;$i<$nb;$i++):
        $data=$a_listing->next($ret,$i);
?>
    <tr>
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
            Modifier / enlever
        </td>
    </tr>
    <?php 
    endfor;
    ?>
</table>
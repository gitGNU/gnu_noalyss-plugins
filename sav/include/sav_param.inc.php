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
// Copyright (2015) Author Dany De Bontridder <dany@alchimerys.be>

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * @param Manage the table sav_parameter, display and set the parameter for the
 * table sav_parameter
 */
require_once 'class_service_after_sale_parameter.php';

global $g_sav_parameter;
$save=HtmlInput::default_value_post('save_param', '');
if ($save != '')
{
    /**
     * Save the parameters
     */
    $spare=HtmlInput::default_value_post('spare_part_select',-1);
    $ledger=HtmlInput::default_value_post('ledger',-1);
    $workhour=HtmlInput::default_value_post('workhour_select',-1);
    $good=HtmlInput::default_value_post('good_select',-1);
    if ( $ledger == -1 )        throw new Exception(_('Paramètre invalide'));
    if ( $spare == -1 )        throw new Exception(_('Paramètre invalide'));
    if ( $workhour == -1 )        throw new Exception(_('Paramètre invalide'));
    if ( $good == -1 )        throw new Exception(_('Paramètre invalide'));
    $g_sav_parameter->set_material($good);
    $g_sav_parameter->save('good',$good);
    $g_sav_parameter->set_spare_part($spare);
    $g_sav_parameter->save('spare',$spare);
    $g_sav_parameter->set_ledger($ledger);
    $g_sav_parameter->save('ledger',$ledger);
    $g_sav_parameter->set_workhour($workhour);
    $g_sav_parameter->save('workhour',$workhour);
    var_dump($_POST);
}

?>
<form method="post">
    <?php
        echo HtmlInput::array_to_hidden(array('ac','gDossier','plugin_code'),$_REQUEST);
    ?>
<table>
    <tr>
        <td>
        <?php echo _('Journal')?>
        </td>
        <td >
            <?php echo $g_sav_parameter->input_ledger()?>
        </td>
    </tr>
    <tr>
        <td>
        <?php echo _('Fiche pour les heure')?>
        </td>
        <td>
            <?php echo $g_sav_parameter->input_workhour()?>
        </td>
    </tr>
    <tr>
        <td>
        <?php echo _('Catégorie de fiche pour les pièces')?>
        </td>
        <td>
            <?php echo $g_sav_parameter->input_spare_part()?>
        </td>
    </tr>
    <tr>
        <td>
        <?php echo _('Catégorie des biens à dépanner')?>
        </td>
        <td>
            <?php echo $g_sav_parameter->input_good()?>
        </td>
    </tr>
</table>
    <?php echo HtmlInput::submit('save_param', _('Sauver les paramètres'));?>
</form>
    
    
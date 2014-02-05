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

require_once 'include/class_transform_client.php';

echo HtmlInput::title_box(_('Client'),'modify_intervat_assujetti_div');
$id=HtmlInput::default_value_get('c_id',null);
if ($id==null || isNumber($id)==0) {
    throw new Exception(_('Aucun client'));
}
$client=new Transform_Client($id);
$name=new IText('c_name',$client->c_name);
$vatnumber=new IText('c_vatnumber',$client->c_vatnumber);
$c_amount_novat=new INum('c_amount_novat',$client->c_amount_novat);
$c_amount_vat=new INum('c_amount_vat',$client->c_amount_vat);
?>        
<form method="get" id="save_intervat_assujetti_frm" onsubmit="return save_intervat_assujetti();return false;">
    <table>
        <tr>
            <td>
                <?php echo _('Nom')?>
            </td>
            <td>
                <?php echo $name->input();?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo _('TVA')?>
            </td>
            <td>
                <?php echo $vatnumber->input();?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo _('Montant')?>
            </td>
            <td>
                <?php echo $c_amount_novat->input();?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo _('TVA')?>
            </td>
            <td>
                <?php echo $c_amount_vat->input();?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo _('Commentaire'); ?>
            </td>
            <td id="modify_intervat_assujetti_div_comment">
                <?php echo $client->c_comment; ?>
            </td>
        </tr>
    </table>
    <input type="checkbox" id="remove_intervat_assujetti" value="5" name="remove_intervat_assujetti"><?php echo _('Cochez pour effacer cette fiche')?>
    <?php
    echo HtmlInput::request_to_hidden(array('c_id','ac','act','gDossier','plugin_code'));
    echo HtmlInput::submit('save_intervat_assujetti_sb',_('Valider'));
    ?>
</form>
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
// require_once '.php';
require_once 'include/class_transform_client.php';
$id = HtmlInput::default_value_request('c_id', null);
$act = HtmlInput::default_value_request('remove_intervat_assujetti', 0);
ob_start();
if ($id == null)
    echo _('Client incorrect');
else
{
    $client = new Transform_Client($id);
    if ($act == 0)
    {
        $action = "UPD";
        $client->c_amount_novat = HtmlInput::default_value_get('c_amount_novat', $client->c_amount_novat);
        $client->c_name = HtmlInput::default_value_get('c_name', $client->c_name);
        $client->c_vatnumber = HtmlInput::default_value_get('c_vatnumber', $client->c_vatnumber);
        $client->c_amount_vat = HtmlInput::default_value_get('c_amount_vat', $client->c_amount_vat);
        $client->set_comment();
        $client->save();
        $js = sprintf('modify_intervat_assujetti(\'%s\',\'%s\',\'%s\',\'%s\')', $_REQUEST['gDossier'], $_REQUEST['ac'], $_REQUEST['plugin_code'], $client->c_id);
        $code = ($client->c_comment == "") ? "ok" : "nok";
        if ($code == "ok"):
            ?>
            <td>
            <?php echo h($client->c_name) ?>
            </td>
            <td>
            <?php echo h($client->c_vatnumber) ?>
            </td>
            <td>
            <?php echo h($client->c_amount_novat) ?>
            </td>
            <td>
            <?php echo h($client->c_amount_vat) ?>
            </td>
            <td>
            <?php echo h($client->c_comment) ?>
            </td>
            <td>
                <a class="line" href="javascript:void(0)" onclick="<?php echo $js; ?>"><?php echo _('Modifier') ?></a>
            </td>
            <?php
            else:
                echo $client->c_comment;
        endif;
    } else
    {
        $action = "EFF";
        $client->delete();
        $code="ok";
        echo _('Effacer');
    }
}
$response = ob_get_clean();
ob_end_clean();
$html = escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>$code</code>
<action>$action</action>
<rowid>$id</rowid>
<html>$html</html>
</data>
EOF;
?>        
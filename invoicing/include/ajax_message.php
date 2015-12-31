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

// Copyright 2015 Author Dany De Bontridder danydb@aevalys.eu
// require_once '.php';
require_once 'class_invoicing_recorded_message.php';
$mr_id=HtmlInput::default_value_get("mr_id", -1);
switch ($act)
{
    case "inv_get_message":
        if (!defined('ALLOWED'))
            die('Appel direct ne sont pas permis');
        echo HtmlInput::title_box(_('Choix Message'), "display_message_box");
        $box=new Invoicing_Recorded_Message($cn);
        $box->display_list();
        break;
    case "inv_select_message":
        $mr=new Invoicing_Recorded_Message($cn);
        $mr->load_message($mr_id);
        header('Content-type: text/xml; charset=UTF-8');
        
        $dom=new DOMDocument('1.0', 'UTF-8');
        $id=$dom->createElement('id', $mr->get_parameter('id'));
        $subject=$dom->createElement("subject",$mr->get_parameter("subject"));
        $sender=$dom->createElement("sender",$mr->get_parameter("sender"));
        $message=$dom->createElement("message",$mr->get_parameter("message"));
        $root=$dom->createElement("root");
        
        $root->appendChild($id);
        $root->appendChild($sender);
        $root->appendChild($subject);
        $root->appendChild($message);
        $dom->appendChild($root);

        echo $dom->saveXML();
        break;
    case "inv_display_message":

        $mr=new Invoicing_Recorded_Message($cn);
        $mr->load_message($mr_id);
        echo HtmlInput::title_box(_('Message').":".$mr->get_parameter("id"), "detail_message_box");
        $mr->display_message();
        echo '<p style="align-text:center">';
        echo HtmlInput::button_action(_('Effacer'),
                sprintf("inv_delete_message('%s','%s','%s','%s')",
                Dossier::id(),$_REQUEST['ac'],$_REQUEST['plugin_code'],$mr_id));
        echo '</p>';
        break;
    case "inv_delete_message":
         $mr=new Invoicing_Recorded_Message_sql($cn,$mr_id);
         $mr->delete();
    default:
        break;
}
?>        
<?php

/*
 * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of class_invoicing_recorded_message
 *
 * @author Dany De Bontridder <dany@alchimerys.be>
 */
require_once 'class_invoicing_recorded_message_sql.php';
class Invoicing_Recorded_Message
{
    function __construct(Database $p_cn,$p_mr_id=0)
    {
        $this->cn=$p_cn;
        $this->message=new Invoicing_Recorded_Message_sql($p_cn,$p_mr_id);
    }
    /**
     * Get an array of recorded message
     */
    function get_all_message() 
    {
        $message=new Invoicing_Recorded_Message_sql($this->cn);
        $a_message=$message->collect_objects();
        return $a_message;
        
    }
    /**
     * Display list of recorded message
     */
    function display_list()
    {
        $array=$this->get_all_message();
        if (count($array)==0) {
            echo _('Aucun message sauvé');
            return;
        }
        $plugin_code=HtmlInput::default_value_request("plugin_code", "");
        $ac=HtmlInput::default_value_request("ac", "");
        $dossier=Dossier::id();
        require_once 'template/recorded_message_list.php';
    }
    /**
     * Add a record into the table invoicing_recorded_message
     * @param type $p_from sender
     * @param type $p_subject subject
     * @param type $p_message message
     */
    function add_message($p_from,$p_subject,$p_message) {
        $msg=new Invoicing_Recorded_Message_sql($this->cn);
        $msg->setp("sender",h($p_from));
        $msg->setp("subject",h($p_subject));
        $msg->setp("message",h($p_message));
        $msg->insert();
    }
    function get_parameter($p_string) {
        return $this->message->getp($p_string);
    }
    function load_message($p_id) {
        $this->message->setp("id",$p_id);
        $this->message->load();
    }
    
    function display_message() {
        require 'template/recorded_message_display.php';
    }
}

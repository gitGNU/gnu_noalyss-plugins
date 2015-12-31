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
 * Description of class_invoicing_recorded_message_sql
 *
 * @author Dany De Bontridder <dany@alchimerys.be>
 */
require_once NOALYSS_INCLUDE.'/database/class_noalyss_sql.php';
require_once NOALYSS_INCLUDE.'/lib/class_database.php';

class Invoicing_Recorded_Message_sql extends Noalyss_SQL
{

    function __construct(Database $p_cn, $p_id=-1)
    {
        $this->table="invoicing.message_recorded";
        $this->primary_key="id";

        $this->name=array(
            "id"=>"id",
            "sender"=>"mr_send_by",
            "message"=>"mr_message",
            "subject"=>"mr_subject"
        );

        $this->type=array(
            "id"=>"numeric",
            "mr_send_by"=>"text",
            "mr_message"=>"text",
            "mr_subject"=>"text"
        );

        $this->default=array(
            "id"=>"numeric",
        );
        $this->date_format="DD.MM.YYYY";
        parent::__construct($p_cn, $p_id);
    }

}

<?php

/**
 * Autogenerated file 
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
require_once NOALYSS_INCLUDE.'/database/class_noalyss_sql.php';
require_once NOALYSS_INCLUDE.'/lib/class_database.php';

/**
 * class_format_sql.php
 *
 * @file
 * @brief abstract of the table importcard.format */
class Importcard_Format_SQL extends Noalyss_SQL
{

    function __construct(Database $p_cn, $p_id=-1)
    {
        $this->table="importcard.format";
        $this->primary_key="id";
        /*
         * List of columns
         */
        $this->name=array(
            "id"=>"id"
            , "f_name"=>"f_name"
            , "f_card_category"=>"f_card_category"
            , "f_skiprow"=>"f_skiprow"
            , "f_delimiter"=>"f_delimiter"
            , "f_surround"=>"f_surround"
            , "f_unicode_encoding"=>"f_unicode_encoding"
            , "f_position"=>"f_position"
            , "f_saved"=>"f_saved"
        );
        /*
         * Type of columns
         */
        $this->type=array(
            "id"=>"numeric"
            , "f_name"=>"text"
            , "f_card_category"=>"numeric"
            , "f_skiprow"=>"numeric"
            , "f_delimiter"=>"text"
            , "f_surround"=>"text"
            , "f_unicode_encoding"=>"text"
            , "f_position"=>"text"
            , "f_saved"=>"numeric"
        );


        $this->default=array(
            "id"=>"auto"
        );

        $this->date_format="DD.MM.YYYY";
        parent::__construct($p_cn, $p_id);
    }

}

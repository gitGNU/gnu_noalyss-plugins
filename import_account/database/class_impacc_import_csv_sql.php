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
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE.'/database/class_noalyss_sql.php';
require_once NOALYSS_INCLUDE.'/lib/class_database.php';

/**
 * class_import_csv_sql.php
 *
 * @file
 * @brief abstract of the table impacc.import_csv */
class Impacc_Import_csv_SQL extends Noalyss_SQL
{

    function __construct(Database $p_cn, $p_id=-1)
    {
        $this->table="impacc.import_csv";
        $this->primary_key="id";
        /*
         * List of columns
         */
        $this->name=array(
            "id"=>"id"
            , "s_decimal"=>"s_decimal"
            , "s_thousand"=>"s_thousand"
            , "s_encoding"=>"s_encoding"
            , "jrn_def_id"=>"jrn_def_id"
            , "s_surround"=>"s_surround"
            , "s_delimiter"=>"s_delimiter"
            ,"import_id"=>"import_id"
            ,"s_date_format"=>"s_date_format"
        );
        /*
         * Type of columns
         */
        $this->type=array(
            "id"=>"numeric"
            , "s_decimal"=>"text"
            , "s_thousand"=>"text"
            , "s_encoding"=>"text"
            , "jrn_def_id"=>"numeric"
            , "s_surround"=>"text"
            , "s_delimiter"=>"text"
            ,"import_id"=>"numeric"
            ,"s_date_format"=>"numeric"
        );


        $this->default=array(
            "id"=>"auto"
        );

        $this->date_format="DD.MM.YYYY";
        parent::__construct($p_cn, $p_id);
    }
}
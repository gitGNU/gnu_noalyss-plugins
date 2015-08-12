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
 * 
 * Copyright 2010 De Bontridder Dany <dany@alchimerys.be>

 */
/**
 * @file
 * @brief Manage the table amortissement.amortissement
 *
 *
  Example
  @code

  @endcode
 */
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_noalyss_sql.php';

/**
 * @brief Manage the table amortissement.amortissement
 */
class Amortissement_Sql extends Noalyss_SQL
{
    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

    function __construct(&$p_cn,$p_id=-1)
    {
        $this->table="amortissement.amortissement";
        $this->primary_key="a_id";
        $this->name=array("a_id"=>"a_id",
            "f_id"=>"f_id"
            , "account_deb"=>"account_deb"
            , "account_cred"=>"account_cred"
            , "a_start"=>"a_start"
            , "a_amount"=>"a_amount"
            , "a_nb_year"=>"a_nb_year"
            , "a_visible"=>"a_visible"
            , "a_date"=>"a_date"
            , 'charge'=>'card_deb'
            , 'amorti'=>'card_cred'
        );
        $this->type=array("a_id"=>"numeric",
            "f_id"=>"numeric"
            , "account_deb"=>"numeric"
            , "account_cred"=>"numeric"
            , "a_start"=>"numeric"
            , "a_amount"=>"numeric"
            , "a_nb_year"=>"numeric"
            , "a_visible"=>"text"
            , "a_date"=>"date"
            , 'card_deb'=>'numeric'
            , 'card_cred'=>'numeric'
        );
        
        $this->date_format='DD.MM.YYYY';

        $this->default=array(
            "a_id"=>"auto"
        );
        global $cn;
        parent::__construct($p_cn, $p_id);
    }

}

// Amortissement_Sql::test_me();
?>

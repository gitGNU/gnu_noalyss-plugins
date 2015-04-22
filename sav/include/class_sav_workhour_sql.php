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
/* $Revision$ */

// Copyright (c) 2015 Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief Manage the table service_after_sale.sav_workhour
 *
 *
  Example
  @code

  @endcode
 */
require_once('class_noalyss_sql.php');

/**
 * @brief Manage the table service_after_sale.sav_workhour
 */
class Sav_Workhour_SQL extends Noalyss_SQL
{

    //------ Attributes-----
    var $id;
    var $total_workhour;
    var $repair_card_id;
    var $work_description;

    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

    function __construct($p_id=-1)
    {


        $this->table="service_after_sale.sav_workhour";
        $this->primary_key="id";

        $this->name=array(
            "id"=>"id", "id"=>"id"
            , "total_workhour"=>"total_workhour"
            , "repair_card_id"=>"repair_card_id"
            , "work_description"=>"work_description"
        );

        $this->type=array(
            "id"=>"numeric"
            , "total_workhour"=>"numeric"
            , "repair_card_id"=>"numeric"
            , "work_description"=>"text"
        );

        $this->default=array(
            "id"=>"auto"
        );
        global $cn;
        $this->date_format="DD.MM.YYYY";
        parent::__construct($cn, $p_id);
    }

    /**
     * @brief Add here your own code: verify is always call BEFORE insert or update
     */
    public function verify()
    {
        parent::verify();
    }

}

?>

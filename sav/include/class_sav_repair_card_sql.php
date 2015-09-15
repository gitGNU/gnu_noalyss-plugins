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
 * @brief Manage the table service_after_sale.sav_repair_card
 *
 *
  Example
  @code

  @endcode
 */
require_once NOALYSS_INCLUDE.'/database/class_noalyss_sql.php';

/**
 * @brief Manage the table service_after_sale.sav_repair_card
 */
class Sav_Repair_Card_SQL extends Noalyss_SQL
{

    //------ Attributes-----
    var $id;
    var $f_id_customer;
    var $f_id_personnel_received;
    var $f_id_personnel_done;
    var $f_id_good;
    var $date_reception;
    var $date_start;
    var $date_end;
    var $garantie;
    var $description_failure;
    var $jr_id;
    var $tech_creation_date;
    var $card_status;
    var $repair_number;
    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */
    static $card_status_value;
    static $card_status_value_simple;

    function __construct($p_id=-1)
    {
        
        
        Sav_Repair_Card_SQL::$card_status_value=array(
            array("value"=>'D','label'=>_('Brouillon')),
            array("value"=>'C','label'=>_('Fermé')),
            array("value"=>'E','label'=>_('En-cours'))
            );
        Sav_Repair_Card_SQL::$card_status_value_simple=array(
            'D'=>_('Brouillon'),
            'C'=>_('Fermé'),
            'E'=>_('En-cours')
            );

        $this->table="service_after_sale.sav_repair_card";
        $this->primary_key="id";

        $this->name=array(
            "id"=>"id"
            , "f_id_customer"=>"f_id_customer"
            , "f_id_personnel_received"=>"f_id_personnel_received"
            , "f_id_personnel_done"=>"f_id_personnel_done"
            , "date_reception"=>"date_reception"
            , "date_start"=>"date_start"
            , "date_end"=>"date_end"
            , "garantie"=>"garantie"
            , "description_failure"=>"description_failure"
            , "jr_id"=>"jr_id"
            , "tech_creation_date"=>"tech_creation_date"
            ,'card_status'=>'card_status'
            , 'repair_number'=>'repair_number'
            ,"f_id_good"=>"f_id_good"
        );

        $this->type=array(
            "id"=>"numeric"
            , "f_id_customer"=>"numeric"
            , "f_id_personnel_received"=>"numeric"
            , "f_id_personnel_done"=>"numeric"
            , "date_reception"=>"date"
            , "date_start"=>"date"
            , "date_end"=>"date"
            , "garantie"=>"text"
            , "description_failure"=>"text"
            , "jr_id"=>"numeric"
            , "tech_creation_date"=>"date"
            ,'card_status'=>'text'
            ,'repair_number'=>'text'
            ,'f_id_good'=>"numeric"
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
        if (!in_array($this->card_status,array('C','D','E')) )
                throw new Exception('Status invalid');
        parent::verify();
    }

}

?>

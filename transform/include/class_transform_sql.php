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

/**
 * @file
 * @brief Manage the tables of  transform schema
 *
 */
require_once('class_phpcompta_sql.php');

/**
 * @brief Manage the table transform.request
 */
class Transform_Request_SQL extends Phpcompta_SQL
{

    //------ Attributes-----
    var $r_id;
    var $r_date;

    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

    function __construct($p_id = -1)
    {


        $this->table = "transform.request";
        $this->primary_key = "r_id";

        $this->name = array(
            "r_id" => "r_id",
            "r_date" => "r_date"
        );

        $this->type = array(
            "r_id" => "numeric",
            "r_date" => "date"
        );

        $this->default = array(
            "r_id" => "auto"
            ,"r_date"=>"auto"
        );
        global $cn;
        $this->date_format = "DD.MM.YYYY";
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

/**
 * @brief Manage the table transform.intervat_representative
 */
class Intervat_Representative_SQL extends Phpcompta_SQL
{

    //------ Attributes-----
    var $rp_id;
    var $r_id;
    var $rp_listing_id;
    var $rp_issued;
    var $rp_type;
    var $rp_name;
    var $rp_street;
    var $rp_postcode;
    var $rp_city;
    var $rp_email;
    var $rp_phone;
    var $rp_countrycode;

    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

    function __construct($p_id = -1)
    {


        $this->table = "transform.intervat_representative";
        $this->primary_key = "rp_id";

        $this->name = array(
            "rp_id" => "rp_id"
            , "r_id" => "r_id"
            , "rp_listing_id" => "rp_listing_id"
            , "rp_issued" => "rp_issued"
            , "rp_type" => "rp_type"
            , "rp_name" => "rp_name"
            , "rp_street" => "rp_street"
            , "rp_postcode" => "rp_postcode"
            , "rp_city" => "rp_city"
            , "rp_email" => "rp_email"
            , "rp_phone" => "rp_phone"
            , "rp_countrycode" => "rp_countrycode"
        );

        $this->type = array(
            "rp_id" => "numeric"
            , "r_id" => "numeric"
            , "rp_listing_id" => "text"
            , "rp_issued" => "text"
            , "rp_type" => "text"
            , "rp_name" => "text"
            , "rp_street" => "text"
            , "rp_postcode" => "text"
            , "rp_city" => "text"
            , "rp_email" => "text"
            , "rp_phone" => "text"
            , "rp_countrycode" => "text"
        );

        $this->default = array(
            "rp_id" => "auto"
        );
        global $cn;
        $this->date_format = "DD.MM.YYYY";
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

/**
 * @brief Manage the table transform.intervat_declarant
 */
class Intervat_Declarant_SQL extends Phpcompta_SQL
{

    //------ Attributes-----
    var $d_id;
    var $r_id;
    var $d_name;
    var $d_street;
    var $d_postcode;
    var $d_city;
    var $d_email;
    var $d_phone;
    var $d_vat_number;
    var $d_countrycode;
    var $d_periode;

    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

    function __construct($p_id = -1)
    {


        $this->table = "transform.intervat_declarant";
        $this->primary_key = "d_id";

        $this->name = array(
            "d_id" => "d_id"
            , "r_id" => "r_id"
            , "d_name" => "d_name"
            , "d_street" => "d_street"
            , "d_postcode" => "d_postcode"
            , "d_city" => "d_city"
            , "d_email" => "d_email"
            , "d_phone" => "d_phone"
            , "d_vat_number" => "d_vat_number"
            , "d_countrycode" => "d_countrycode"
            , "d_periode" => "d_periode"
        );

        $this->type = array(
            "d_id" => "numeric"
            , "r_id" => "numeric"
            , "d_name" => "text"
            , "d_street" => "text"
            , "d_postcode" => "text"
            , "d_city" => "text"
            , "d_email" => "text"
            , "d_phone" => "text"
            , "d_vat_number" => "text"
            , "d_countrycode" => "text"
            , "d_periode" => "text"
        );

        $this->default = array(
            "d_id" => "auto"
        );
        global $cn;
        $this->date_format = "DD.MM.YYYY";
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
/**
 * @brief Manage the table transform.intervat_client
 */
class Intervat_Client_SQL extends Phpcompta_SQL
{

    //------ Attributes-----
    var $c_id;
    var $d_id;
    var $c_name;
    var $c_vatnumber;
    var $c_amount_vat;
    var $c_amount_novat;
    var $c_issuedby;

    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

    function __construct($p_id = -1)
    {


        $this->table = "transform.intervat_client";
        $this->primary_key = "c_id";

        $this->name = array(
            "c_id" => "c_id"
            , "d_id" => "d_id"
            , "c_name" => "c_name"
            , "c_vatnumber" => "c_vatnumber"
            , "c_amount_vat" => "c_amount_vat"
            , "c_amount_novat" => "c_amount_novat"
            , "c_issuedby" => "c_issuedby"
        );

        $this->type = array(
            "c_id" => "numeric"
            , "d_id" => "numeric"
            , "c_name" => "text"
            , "c_vatnumber" => "text"
            , "c_amount_vat" => "text"
            , "c_amount_novat" => "text"
            , "c_issuedby" => "text"
        );

        $this->default = array(
            "c_id" => "auto"
        );
        global $cn;
        $this->date_format = "DD.MM.YYYY";
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
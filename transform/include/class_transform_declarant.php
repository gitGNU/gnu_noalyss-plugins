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

/**
 * @brief contains declarant
  @code
  $vat_number = "0000000097";
  $name = "Nom Declarant";
  $street = "Rue du declarant";
  $postcode = "9999";
  $city = "TESTCITY";
  $countrycode = "BE";
  $email = "dany@alch.be";
  $phone = "000000000";
  @endcode
 */
// Copyright Author Dany De Bontridder danydb@aevalys.eu
require_once 'class_transform_sql.php';
class Transform_Declarant
{

    /**
     * name
     */
    var $name;

    /**
     * street
     */
    var $street;

    /**
     * Postcode
     */
    var $postcode;

    /**
     * city
     */
    var $city;

    /**
     * country code (BE)
     */
    var $countrycode;

    /**
     * email
     */
    var $email;

    /**
     * phone
     */
    var $phone;

    /**
     * vatnumber
     */
    var $vatnumber;
    /**
     * readOnly
     */
    var $readOnly;
    function __construct()
    {
        $this->readOnly=false;
        $this->data=new Intervat_Declarant_SQL;
    }
    function fromPost()
    {
        global $g_parameter;
        $this->name = HtmlInput::default_value_post("p_dec_name", $g_parameter->MY_NAME);
        $this->street = HtmlInput::default_value_post("p_dec_street", $g_parameter->MY_STREET);
        $this->postcode = HtmlInput::default_value_post("p_dec_postcode", $g_parameter->MY_CP);
        $this->city = HtmlInput::default_value_post("p_dec_city", $g_parameter->MY_COMMUNE);
        $this->countrycode = HtmlInput::default_value_post("p_dec_countrycode", "BE");
        $this->email = HtmlInput::default_value_post("p_dec_email", null);
        $this->phone = HtmlInput::default_value_post("p_dec_phone", $g_parameter->MY_TEL);
        $this->vatnumber = HtmlInput::default_value_post("p_dec_vatnumber", $g_parameter->MY_TVA);
        $this->year=HtmlInput::default_value_post('p_year',null);
    }

    function input()
    {
        $h_name = new IText('p_dec_name', $this->name);
        $h_name->readOnly=$this->readOnly;
        $h_vatnumber = new IText('p_dec_vatnumber', $this->vatnumber);
        $h_vatnumber->readOnly=$this->readOnly;
        $h_street = new IText('p_dec_street', $this->street);
        $h_street->readOnly=$this->readOnly;
        $h_postcode = new IText('p_dec_postcode', $this->postcode);
        $h_postcode->readOnly=$this->readOnly;
        $h_city = new IText('p_dec_city', $this->city);
        $h_city->readOnly=$this->readOnly;
        $h_countrycode = new IText('p_dec_countrycode', $this->countrycode);
        $h_countrycode->readOnly=$this->readOnly;
        $h_email = new IText('p_dec_email', $this->email);
        $h_email->readOnly=$this->readOnly;
        $h_phone = new IText('p_dec_phone', $this->phone);
        $h_phone->readOnly=$this->readOnly;
        require_once 'template/listing_assujetti_declarant.php';
    }

    function insert()
    {
        $this->data->d_name=$this->name;
        $this->data->d_street=$this->street;
        $this->data->d_postcode=$this->postcode;
        $this->data->d_city=$this->city;
        $this->data->d_countrycode=$this->countrycode;
        $this->data->d_email=$this->email;
        $this->data->d_phone=$this->phone;
        $this->data->d_vat_number=$this->vatnumber;
        $this->data->d_countrycode=$this->countrycode;
        $this->data->d_periode=$this->year;
        $this->data->insert();
    }
    function from_db($request_id)
    {
        global $cn;
        $id = $cn->get_value("select d_id from transform.intervat_declarant where r_id=$1", array($request_id));
        $this->data = new Intervat_Declarant_SQL($id);
        $this->name = $this->data->d_name;
        $this->street = $this->data->d_street;
        $this->postcode = $this->data->d_postcode;
        $this->city = $this->data->d_city;
        $this->countrycode = $this->data->d_countrycode;
        $this->email = $this->data->d_email;
        $this->phone = $this->data->d_phone;
        $this->vatnumber = $this->data->d_vat_number;
        $this->countrycode = $this->data->d_countrycode;
        $this->year = $this->data->d_periode;
    }

    function display()
    {
        $this->readOnly=true;
        $this->input();
    }

}

?>

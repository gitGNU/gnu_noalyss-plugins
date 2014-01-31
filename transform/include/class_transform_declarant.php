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
   function fromPost()
   {
       $this->name=HtmlInput::default_value_post("p_dec_name",null);
       $this->street=HtmlInput::default_value_post("p_dec_street",null);
       $this->postcode=HtmlInput::default_value_post("p_dec_postcode",null);
       $this->city=HtmlInput::default_value_post("p_dec_city",null);
       $this->countrycode=HtmlInput::default_value_post("p_dec_countrycode",null);
       $this->email=HtmlInput::default_value_post("p_dec_email",null);
       $this->phone=HtmlInput::default_value_post("p_dec_phone",null);
       $this->vatnumber=HtmlInput::default_value_post("p_dec_vatnumber",null);
   }
   function input()
   {
       $h_name=new IText('p_dec_name',$this->name);
       $h_vatnumber=new IText('p_dec_vatnumber',$this->vatnumber);
       $h_street=new IText('p_dec_street',$this->street);
       $h_postcode=new IText('p_dec_postcode',$this->postcode);
       $h_city=new IText('p_dec_city',$this->city);
       $h_countrycode=new IText('p_dec_countrycode',$this->countrycode);
       $h_email=new IText('p_dec_email',$this->email);
       $h_phone=new IText('p_dec_phone',$this->phone);
       require_once 'template/listing_assujetti_declarant.php';

   }
}
?>

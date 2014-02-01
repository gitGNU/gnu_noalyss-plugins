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
 * @brief contains the representative
 @code
  $ID = "0000000097";
$issued = "BE";
$type = 'TIN';
$name = "Nom Mandataire";
$street = "Nom de rue";
$postcode = "9999";
$city = "TESTCITY";
$countrycode = "BE";
$email = "dany@alch.be";
$phone = "000000000";
 @endcode
 */

class Transform_Representative
{
    /**
     * id is the id of the listing
     */
    var $id;
    /**
     * issued by
     */
   var $issued;
   /**
    * Type (TIN)
    */
   var $type;
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
    * Transform_Representative_SQL
    */
   var $data;
   function fromPost()
   {
       $this->id=HtmlInput::default_value_post("p_id",1);
       $this->type=HtmlInput::default_value_post("p_type",null);
       $this->name=HtmlInput::default_value_post("p_name",null);
       $this->street=HtmlInput::default_value_post("p_street",null);
       $this->postcode=HtmlInput::default_value_post("p_postcode",null);
       $this->city=HtmlInput::default_value_post("p_city",null);
       $this->countrycode=HtmlInput::default_value_post("p_countrycode",null);
       $this->email=HtmlInput::default_value_post("p_email",null);
       $this->phone=HtmlInput::default_value_post("p_phone",null);
       $this->issued=HtmlInput::default_value_post("p_issued",null);
   }
   function input()
   {
       $h_type=new ISelect('p_type');
       $h_type->value=array(
            array("label"=>'TIN',"value"=>"TIN"),
            array("label"=>'NVAT',"value"=>"NVAT"),
            array("label"=>'other',"value"=>"other")
           );
       $h_type->selected=$this->type;
       $h_name=new IText('p_name',$this->name);
       $h_street=new IText('p_street',$this->street);
       $h_postcode=new IText('p_postcode',$this->postcode);
       $h_city=new IText('p_city',$this->city);
       $h_countrycode=new IText('p_countrycode',$this->countrycode);
       $h_email=new IText('p_email',$this->email);
       $h_phone=new IText('p_phone',$this->phone);
       $h_id=new INum('p_id',$this->id);
       $h_issued=new IText("p_issued",$this->issued);
       require_once 'template/listing_assujetti_representative.php';

   }
   function insert()
   {
       var_dump($this->id);
       $this->data->rp_listing_id=$this->id;
       $this->data->rp_issued=$this->issued;
       $this->data->rp_type=$this->type;
       $this->data->rp_name=$this->name;
       $this->data->rp_street=$this->street;
       $this->data->rp_postcode=$this->postcode;
       $this->data->rp_countrycode=$this->countrycode;
       $this->data->rp_email=$this->email;
       $this->data->rp_phone=$this->phone;
       
       $this->data->insert();
   }
}


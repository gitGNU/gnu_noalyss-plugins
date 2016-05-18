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
 * class_parameter_tva_sql.php
 *
 *@file
 *@brief abstract of the table impacc.parameter_tva */
class Impacc_Parameter_Tva_SQL extends Noalyss_SQL
{

function __construct(Database $p_cn,$p_id=-1)
  {
  $this->table = "impacc.parameter_tva";
  $this->primary_key = "pt_id";
/*
 * List of columns
 */
  $this->name=array(
  	"pt_id"=>"pt_id"
	,"tva_id"=>"tva_id"
	,"tva_code"=>"tva_code"
        );
/*
 * Type of columns
 */
  $this->type = array(
   	"pt_id"=>"numeric"
	,"tva_id"=>"numeric"
	,"tva_code"=>"text"
          );
 

  $this->default = array(
  "pt_id" => "auto"
  );

  $this->date_format = "DD.MM.YYYY";
  parent::__construct($p_cn,$p_id);
  }
  

}
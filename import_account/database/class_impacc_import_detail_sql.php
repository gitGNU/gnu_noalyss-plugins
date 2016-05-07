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

/**
 * class_import_detail_sql.php
 *
 *@file
 *@brief abstract of the table impacc.import_detail */
class Impacc_Import_detail_SQL extends Noalyss_SQL
{

function __construct(Database $p_cn,$p_id=-1)
  {
  $this->table = "impacc.import_detail";
  $this->primary_key = "id";
/*
 * List of columns
 */
  $this->name=array(
  	"id"=>"id"
	,"import_id"=>"import_id"
	,"id_date"=>"id_date"
	,"id_code_group"=>"id_code_group"
	,"id_nb_row"=>"id_nb_row"
	,"id_pj"=>"id_pj"
	,"id_acc"=>"id_acc"
	,"id_acc_second"=>"id_acc_second"
	,"id_quant"=>"id_quant"
	,"id_amount_novat"=>"id_amount_novat"
	,"id_amount_vat"=>"id_amount_vat"
	,"tva_code"=>"tva_code"
        ,"id_label"=>"id_label"
	,"jr_id"=>"jr_id"
        ,'id_status'=>'id_status'
        ,'id_message'=>'id_message'
        );
/*
 * Type of columns
 */
  $this->type = array(
   	"id"=>"numeric"
	,"import_id"=>"numeric"
	,"id_date"=>"text"
	,"id_code_group"=>"text"
	,"id_nb_row"=>"numeric"
	,"id_pj"=>"text"
	,"id_acc"=>"text"
	,"id_acc_second"=>"text"
	,"id_quant"=>"text"
	,"id_amount_novat"=>"text"
	,"id_amount_vat"=>"text"
	,"tva_code"=>"text"
        ,"id_label"=>"text"
	,"jr_id"=>"numeric"
        ,'id_status'=>'numeric'
        ,'id_message'=>'text'
          );
 

  $this->default = array(
  "id" => "auto"
  );

  $this->date_format = "DD.MM.YYYY";
  parent::__construct($p_cn,$p_id);
  }
  

}
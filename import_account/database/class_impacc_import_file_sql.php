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
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
require_once NOALYSS_INCLUDE.'/database/class_noalyss_sql.php';
require_once NOALYSS_INCLUDE.'/lib/class_database.php';


/**
 * class_import_file_sql.php
 *
 *@file
 *@brief abstract of the table impacc.import_file */
class Impacc_Import_file_SQL extends Noalyss_SQL
{

function __construct(Database $p_cn,$p_id=-1)
  {
  $this->table = "impacc.import_file";
  $this->primary_key = "id";
/*
 * List of columns
 */
  $this->name=array(
  	"id"=>"id"
	,"i_filename"=>"i_filename"
	,"i_tmpname"=>"i_tmpname"
	,"i_type"=>"i_type"
	,"i_date_transfer"=>"i_date_transfer"
	,"i_date_import"=>"i_date_import"
        );
/*
 * Type of columns
 */
  $this->type = array(
   	"id"=>"numeric"
	,"i_filename"=>"text"
	,"i_tmpname"=>"text"
	,"i_type"=>"text"
	,"i_date_transfer"=>"timestamp without time zone"
	,"i_date_import"=>"timestamp without time zone"
          );
 

  $this->default = array(
  "id" => "auto"
  );

  $this->date_format = "DD.MM.YYYY HH:MI:SS";
  parent::__construct($p_cn,$p_id);
  }
  

}
?>

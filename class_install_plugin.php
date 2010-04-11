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
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief this class manages the installation and the patch of the plugin
 */

class Install_Plugin 
{

  function __construct($p_cn) {
    $this->cn=$p_cn;
  }

  /**
   *@brief install the plugin, create all the needed schema, tables, proc
   * in the database
   *@param $p_dossier is the dossier id
   */
  function install() {
    $this->cn->start();
    // create the schema
    $this->create_schema();
    // create table + put default values
    $this->create_table_parameter();
    $this->cn->commit();
  }
  function create_schema() {
    $this->cn->exec_sql('create schema tva_belge');
  }
  function create_table_parameter() {
$sql=<<<EOF
CREATE TABLE tva_belge.parameter
(
  pcode text NOT NULL,
  pvalue text,
  paccount text,
  CONSTRAINT parameter_pkey PRIMARY KEY (pcode)
 );
EOF;
$this->cn->exec_sql($sql);
// load default value
$array=array(
	     'GRIL00'=>array('4514','70%,71%'),
	     'GRIL01'=>array('4513','70%,71%,'),
	     'GRIL02'=>array('4512','70%,71%'),
	     'GRIL03'=>array('4511','70%,71%'),
	     'GRIL44'=>array('45145','70%,71%'),
	     'GRIL45'=>array('45144','70%,71%'),
	     'GRIL46'=>array('45145','70%,71%'),
	     'GRIL47'=>array('4514','701%'),
	     'GRIL48'=>array('4514','7091'),
	     'GRIL49'=>array('4511,4512,4513','7092'),
	     'GRIL81'=>array('4111,4112,4113','60%'),
	     'GRIL82'=>array('4111,4112,4113','61%'),
	     'GRIL83'=>array('4111','21,22,23,24,25'),
	     'GRIL84'=>array('','6096'),
	     'GRIL85'=>array('','6097'),
	     'GRIL86'=>array('45142','61%'),
	     'GRIL87'=>array('451',''),
	     'GRIL88'=>array('45145',''),
	     'ATVA'=>array('4117',''));

foreach ($array as $code=>$value) {
  var_dump($value);
  $this->cn->exec_sql('insert into tva_belge.parameter(pcode,pvalue,paccount) values ($1,$2,$3)',
		      array($code,$value[0],$value[1]));
  }
}
}
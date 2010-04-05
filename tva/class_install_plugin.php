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
  CONSTRAINT parameter_pkey PRIMARY KEY (pcode)
 );
EOF;
$this->cn->exec_sql($sql);
// load default value
$array=array(
	     'GRIL00'=>'4114',
	     'GRIL01'=>'4113',
	     'GRIL02'=>'4112',
	     'GRIL03'=>'4111',
	     'GRIL44'=>'41142',
	     'GRIL45'=>'41144',
	     'GRIL46'=>'41145',
	     'GRIL47'=>'41141',
	     'GRIL48'=>'7091',
	     'GRIL49'=>'7092',
	     'GRIL81'=>'60',
	     'GRIL82'=>'61',
	     'GRIL83'=>'21,22,23,24',
	     'GRIL84'=>'6096',
	     'GRIL85'=>'6097',
	     'GRIL86'=>'45142',
	     'GRIL87'=>'45144',
	     'GRIL88'=>'45145',
	     'ATVA'=>'4117');
foreach ($array as $code=>$value) {
  $this->cn->exec_sql('insert into tva_belge.parameter(pcode,pvalue) values ($1,$2)',
		      array($code,$value));
  }
}
}
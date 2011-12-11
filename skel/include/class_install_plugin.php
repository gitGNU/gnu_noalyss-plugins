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

/* !\file
 * \brief this class manages the installation and the patch of the plugin
 replace SKEL by the plugin schema
 */

class Install_Plugin
{

	function __construct($p_cn)
	{
		$this->cn = $p_cn;
	}

	/**
	 * @brief install the plugin, create all the needed schema, tables, proc
	 * in the database
	 * @param $p_dossier is the dossier id
	 */
	function install()
	{
		$this->cn->start();
		// create the schema
		$this->create_schema();
		// create table + put default values
		$this->create_table_parameter();
		$this->cn->commit();
	}

	function create_schema()
	{
		$this->cn->exec_sql('create schema SKEL');
	}

	function create_table_parameter()
	{
		$sql = <<<EOF
CREATE TABLE SKEL.parameter
(
  pr_id text NOT NULL,
  pr_value text,
  CONSTRAINT SKEL_parameter_pkey PRIMARY KEY (pr_id)
 );
EOF;
		$this->cn->exec_sql($sql);
// load default value
		$array = array(
			'GRIL00' => array('6'),
			'GRIL01' => array('3'),
			'GRIL02' => array('2', ''),

		);

		foreach ($array as $code => $value)
		{
			$this->cn->exec_sql('insert into SKEL.parameter(pr_id,pr_value,pr_other) values ($1,$2)', array($code, $value[0]));
		}
	}

}


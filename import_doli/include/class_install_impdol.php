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

/**
 * @file
 * @brief
 *
 */
class install_impdol
{

	function install($p_cn)
	{

		$cn->exec_sql("create schema impdol");
		$cn->exec_sql("
			create table impdol.version (
				v_id bigint primary key,
				v_date now(),
				v_text text
			)
	");
		$cn->exec_sql("insert into impdol.version(v_id,v_text", array(1, "Installation"));
		$cn->exec_sql('
			CREATE TABLE impdol.parameter_tva
				(
					pt_id serial NOT NULL,
					tva_id bigint,
					pt_rate numeric(20,4) DEFAULT 0,
					CONSTRAINT parameter_tva_pkey PRIMARY KEY (pt_id )
					)
			');
	}

}

?>

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
		try
		{
			$p_cn->start();
			$p_cn->exec_sql("create schema impdol");
			$p_cn->exec_sql("
			create table impdol.version (
				v_id bigint primary key,
				v_date date default now(),
				v_text text
			)
				");
			$p_cn->exec_sql("insert into impdol.version(v_id,v_text) values ($1,$2)", array(1, "Installation"));

			$p_cn->exec_sql("
				CREATE TABLE impdol.import
					(
					i_id serial NOT NULL,
					temp_file text,
					send_file text,
					i_date timestamp with time zone DEFAULT now(),
					i_row bigint,
					CONSTRAINT import_pkey PRIMARY KEY (i_id )
					)");

			$p_cn->exec_sql('
			CREATE TABLE impdol.parameter_tva
				(
					pt_id serial NOT NULL,
					tva_id bigint,
					pt_rate numeric(20,4) DEFAULT 0,
					CONSTRAINT parameter_tva_pkey PRIMARY KEY (pt_id )
					)
			');
			$p_cn->exec_sql("
			CREATE TABLE impdol.operation_tmp
					(
					o_id bigserial NOT NULL,
					o_doli text,
					o_date text,
					o_qcode text,
					f_id text,
					o_label text,
					o_pj text,
					amount_unit text,
					amount_vat text,
					number_unit text,
					vat_rate text,
					amount_total text,
					jrn_def_id text,
					o_message text,
					i_id bigint,
					o_result char,
					tva_id bigint,
					o_type char,
					o_poste text,
					CONSTRAINT operation_tmp_pkey PRIMARY KEY (o_id ),
					CONSTRAINT operation_tmp_i_id_fkey FOREIGN KEY (i_id)
						REFERENCES impdol.import (i_id) MATCH SIMPLE
						ON UPDATE CASCADE ON DELETE CASCADE
					)
			");

			$p_cn->exec_sql("
			CREATE TABLE impdol.operation_transfer
				(
				ot_id serial NOT NULL,
				j_id bigint,
				o_id bigint,
				CONSTRAINT operation_transfer_pkey PRIMARY KEY (ot_id ),
				CONSTRAINT operation_transfer_j_id_fkey FOREIGN KEY (j_id)
					REFERENCES jrnx (j_id) MATCH SIMPLE
					ON UPDATE CASCADE ON DELETE CASCADE,
				CONSTRAINT operation_transfer_o_id_fkey FOREIGN KEY (o_id)
					REFERENCES impdol.operation_tmp (o_id) MATCH SIMPLE
					ON UPDATE CASCADE ON DELETE CASCADE
				)");



			$p_cn->exec_sql("
			COMMENT ON COLUMN impdol.operation_tmp.o_result IS 'result is T can be transferrable, N cannot be transferrable';
			");

			$p_cn->exec_sql("
			COMMENT ON COLUMN impdol.operation_tmp.o_type IS 'S = marchandise, serviceT = tiers (fournisseurs, client)'
			");
			$p_cn->commit();
		}
		catch (Exception $e)
		{
			$p_cn->rollback();
			print_r($e->getTraceAsString());
		}
	}

}

?>

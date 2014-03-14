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
  function __construct($p_cn) 
  {
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
    $this->create_table_format_bank();
    $this->create_table_import();
    $this->create_table_temp_bank();
    $this->create_table_version();

    $this->cn->commit();
  }
  function create_schema() {
    $this->cn->exec_sql('create schema importbank');
  }
  function create_table_format_bank() {
    $sql="
	  CREATE TABLE importbank.format_bank
	      (
		id serial NOT NULL,
		format_name text NOT NULL,
		jrn_def_id integer,
		pos_lib integer,
		pos_amount integer,
		pos_date integer,
		pos_operation_nb integer,
		sep_decimal character(1),
		sep_thousand character(1),
		sep_field character(1),
		format_date text,
		nb_col integer,
		skip integer,
		pos_third integer,
		pos_extra integer,
		CONSTRAINT format_bank_pkey PRIMARY KEY (id),
		CONSTRAINT fk_jrn FOREIGN KEY (jrn_def_id)
		    REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE
		    ON UPDATE CASCADE ON DELETE SET NULL
	      )";

    $this->cn->exec_sql($sql);
  }
  function create_table_import()
  {
    $sql="CREATE TABLE importbank.import
    (
      id serial NOT NULL,
      i_date timestamp with time zone DEFAULT now(),
      format_bank_id bigint,
          CONSTRAINT import_pkey PRIMARY KEY (id),
          CONSTRAINT fk_format_bank FOREIGN KEY (format_bank_id)
          REFERENCES importbank.format_bank (id) MATCH SIMPLE
          ON UPDATE CASCADE ON DELETE CASCADE  )";
    $this->cn->exec_sql($sql);
  }	
  function create_table_temp_bank()
  {
    $sql="CREATE TABLE importbank.temp_bank
	       (
		 id serial NOT NULL,
		 tp_date date NOT NULL,
		 jrn_def_id integer,
		 libelle text,
		 amount numeric(20,4),
		 ref_operation text,
		 status character(1) DEFAULT 'N'::bpchar,
		 import_id bigint,
		 tp_third text,
		 tp_extra text,
		 f_id integer,
		 tp_rec text,
		 tp_error_msg text,
		 CONSTRAINT temp_bank_pkey PRIMARY KEY (id),
		 CONSTRAINT fk_import_id FOREIGN KEY (import_id)
		     REFERENCES importbank.import (id) MATCH SIMPLE
		     ON UPDATE CASCADE ON DELETE CASCADE,
		 CONSTRAINT fk_jrn_temp_bank FOREIGN KEY (jrn_def_id)
		     REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE
		     ON UPDATE CASCADE ON DELETE SET NULL,
		 CONSTRAINT temp_bank_f_id_fkey FOREIGN KEY (f_id)
		     REFERENCES fiche (f_id) MATCH SIMPLE
		     ON UPDATE CASCADE ON DELETE CASCADE
	       )";
    $this->cn->exec_sql($sql);
  }
  function create_table_version()
  {
    $sql="CREATE TABLE importbank.version
	 (
	   version integer NOT NULL,
	   CONSTRAINT version_pkey PRIMARY KEY (version)
	 )";
    $this->cn->exec_sql($sql);
  }
}

?>

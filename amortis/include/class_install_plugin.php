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
    $this->create_table_declaration_amount();
    $this->create_table_intra();
    $this->create_table_intra_child();
    $this->create_table_assujetti();
    $this->create_table_assujetti_child();

    $this->cn->commit();
  }
  function create_schema() {
    $this->cn->exec_sql('create schema tva_belge');
  }
  function create_table_assujetti() {
    $sql="
CREATE TABLE tva_belge.assujetti
(
  a_id serial NOT NULL,
  start_date date NOT NULL,
  end_date date NOT NULL,
  xml_oid oid,
  periodicity character(1) NOT NULL,
  tva_name text,
  num_tva text,
  adress text,
  country text,
  date_decl date DEFAULT now(),
  periode_dec integer,
  CONSTRAINT assujetti_pk PRIMARY KEY (a_id)
)
";
    $this->cn->exec_sql($sql);
  }
  function create_table_assujetti_child() {
   $sql="
CREATE TABLE tva_belge.assujetti_chld
(
  ac_id serial NOT NULL,
  a_id bigint,
  ac_tvanum text NOT NULL,
  ac_amount numeric(20,4) NOT NULL,
  ac_vat numeric(20,4) NOT NULL,
  ac_periode character varying(6) NOT NULL,
  ac_qcode text NOT NULL,
  ac_name text NOT NULL,
  CONSTRAINT assujetti_chld_pk PRIMARY KEY (ac_id),
  CONSTRAINT assujetti_fk FOREIGN KEY (a_id)
      REFERENCES tva_belge.assujetti (a_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
)
";
    $this->cn->exec_sql($sql);
  }
  function create_table_intra() {
    $sql=<<<EOF

CREATE TABLE tva_belge.intracomm
(
  i_id serial NOT NULL,
  start_date date not null,
  end_date date not null,
  xml_oid oid,
  periodicity char(1) not null,
  tva_name text,
  num_tva text,
  adress text,
  country text,
  date_decl date default now(),
  periode_dec integer,
  CONSTRAINT intracom_pk PRIMARY KEY (i_id)
)
EOF;
    $this->cn->exec_sql($sql);
  }
  function create_table_intra_child() {
$sql=<<<EOF

CREATE TABLE tva_belge.intracomm_chld
(
  ic_id serial,
  i_id bigint, 
  ic_tvanum text NOT NULL,
  ic_amount numeric(20,4) NOT NULL,
  ic_code character varying(1) NOT NULL,
  ic_periode character varying(6) NOT NULL,
  ic_qcode text not null,
  ic_name text not null,
  CONSTRAINT intracom_chld_pk PRIMARY KEY (ic_id),
 CONSTRAINT intracom_fk FOREIGN KEY (i_id)
      REFERENCES tva_belge.intracomm (i_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
)
EOF;
$this->cn->exec_sql($sql);
  }
  /**
   *@brief create the table tva_belge.declaration_amount
   */
  function create_table_declaration_amount() {
    $sql=<<<EOF
      create table tva_belge.declaration_amount
(
 da_id serial,
 d00 numeric(20,4) default 0.0 not null,
 d01 numeric(20,4) default 0.0 not null,
 d02 numeric(20,4) default 0.0 not null,
 d03 numeric(20,4) default 0.0 not null,
 d44 numeric(20,4) default 0.0 not null,
 d45 numeric(20,4) default 0.0 not null,
 d46 numeric(20,4) default 0.0 not null,
 d47 numeric(20,4) default 0.0 not null,
 d48 numeric(20,4) default 0.0 not null,
 d49 numeric(20,4) default 0.0 not null,
 d81 numeric(20,4) default 0.0 not null,
 d82 numeric(20,4) default 0.0 not null,
 d83 numeric(20,4) default 0.0 not null,
 d84 numeric(20,4) default 0.0 not null,
 d85 numeric(20,4) default 0.0 not null,
 d86 numeric(20,4) default 0.0 not null,
 d87 numeric(20,4) default 0.0 not null,
 d88 numeric(20,4) default 0.0 not null,
 d54 numeric(20,4) default 0.0 not null,
 d55 numeric(20,4) default 0.0 not null,
 d56 numeric(20,4) default 0.0 not null,
 d57 numeric(20,4) default 0.0 not null,
 d61 numeric(20,4) default 0.0 not null,
 d63 numeric(20,4) default 0.0 not null,
 dxx numeric(20,4) default 0.0 not null,
 d59 numeric(20,4) default 0.0 not null,
 d62 numeric(20,4) default 0.0 not null,
 d64 numeric(20,4) default 0.0 not null,
 dyy numeric(20,4) default 0.0 not null,
 d71 numeric(20,4) default 0.0 not null,
 d72 numeric(20,4) default 0.0 not null,
 d91 numeric(20,4) default 0.0 not null,
 start_date date not null,
 end_date date not null,
 xml_oid oid,
 periodicity char(1) not null,
 tva_name text,
 num_tva text,
 adress text,
 country text,
 date_decl date default now(),
 periode_dec integer,
  CONSTRAINT declaration_amount_pkey PRIMARY KEY (da_id)
 );
EOF;
$this->cn->exec_sql($sql);

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
	     'GRIL00'=>array('6',''),
	     'GRIL01'=>array('3',''),
	     'GRIL02'=>array('2',''),
	     'GRIL03'=>array('1',''),
	     'GRIL44'=>array('',''),
	     'GRIL45'=>array('',''),
	     'GRIL46'=>array('5','70%,71%'),
	     'GRIL47'=>array('',''),
	     'GRIL48'=>array('','7091'),
	     'GRIL49'=>array('','7092'),
	     'GRIL81'=>array('1,2,3','60%'),
	     'GRIL82'=>array('1,2,3','61%'),
	     'GRIL83'=>array('1,2,3','22%,23%,24%,25%'),
	     'GRIL84'=>array('1,2,3,4','6091'),
	     'GRIL85'=>array('',''),
	     'GRIL86'=>array('5','61%,22%,23%,24%,25%,60%'),
	     'GRIL87'=>array('',''),
	     'GRIL88'=>array('',''),
	     'GRIL54'=>array('1,2,3','7%'),
	     'GRIL55'=>array('',''),
	     'GRIL56'=>array('',''),
	     'GRIL57'=>array('',''),
	     'GRIL61'=>array('',''),
	     'GRIL63'=>array('',''),
	     'GRIL59'=>array('1,2,3','6%,22%,23%,24%,25%'),
	     'GRIL62'=>array('',''),
	     'GRIL64'=>array('',''),
	     'ATVA'=>array('4117','')
	     );

foreach ($array as $code=>$value) {
  $this->cn->exec_sql('insert into tva_belge.parameter(pcode,pvalue,paccount) values ($1,$2,$3)',
		      array($code,$value[0],$value[1]));
  }
}
}

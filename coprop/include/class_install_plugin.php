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

/**\file
 * \brief this class manages the installation and the patch of the plugin
 */
define ('ATTR_IMMEUBLE',5000);
define ('ATTR_COPROP',5001);

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
		$this->create_card();

		$this->create_table_parameter();

		$this->create_table();

		$this->cn->commit();
	}

	function create_schema()
	{
		$this->cn->exec_sql('create schema coprop');
        $this->cn->exec_sql("create sequence coprop.appel_fond_id");
	}

	function create_card()
	{
		// create categorie immeuble, lot et coprop.
		$fiche_def = new Fiche_Def($this->cn);

		// Create categorie pour lot immeuble et coprop + attribut
		$fiche_def->add(array(
			'FICHE_REF' => 9,
			'nom_mod' => 'Copropriétaires - plugin',
                        'fd_description'=>'Liste des copropriétaires, catégorie créée par le plugin copropriété',
			'class_base' => null)
		);
		$copro = $fiche_def->id;
		$lot_def = new Fiche_Def($this->cn);

		$lot_def->add(array(
			'FICHE_REF' => 15,
			'nom_mod' => 'Lots - plugin',
                        'fd_description'=>'Liste des lots, catégorie créée par le plugin copropriété',
			'class_base' => null)
		);
		$lot = $lot_def->id;

		$imm_def = new Fiche_Def($this->cn);
		$imm_def->add(array(
			'FICHE_REF' => 15,
			'nom_mod' => 'immeuble - plugin',
                        'fd_description'=>'Liste des immeubles, catégorie créée par le plugin copropriété',
			'class_base' => null)
		);
		$immeuble = $imm_def->id;

		// creation attribut
		$this->cn->exec_sql("insert into attr_def (ad_id,ad_text,ad_type,ad_size,ad_extra)
		values  (".ATTR_COPROP.",'Copropriétaire','select','22','select f_id,vw_name from vw_fiche_attr where fd_id = $copro ')");
		$this->cn->exec_sql("insert into attr_def (ad_id,ad_text,ad_type,ad_size,ad_extra) values
		(".ATTR_IMMEUBLE.",'Immeuble','select','22','select f_id,vw_name from vw_fiche_attr where fd_id = $immeuble ');");

		$lot_def->InsertAttribut(ATTR_COPROP); // lien vers coprop
		$lot_def->InsertAttribut(ATTR_IMMEUBLE);// lien vers immeuble

		$imm_def->InsertAttribut(14); // adresse
		$imm_def->InsertAttribut(15); // code postale
		$imm_def->InsertAttribut(24); //ville
		$imm_def->InsertAttribut(16); // pays

		$fiche_def->InsertAttribut(27); // gsm
		$fiche_def->InsertAttribut(32); // prénom
		$fiche_def->InsertAttribut(10); // date début
		$fiche_def->InsertAttribut(33); // date fin
		//
		// creation vue (create_view_summary)
		$this->cn->exec_sql("CREATE OR REPLACE VIEW coprop.summary AS
				SELECT a.f_id AS lot_id, m.ad_value AS building_id, c.ad_value AS coprop_id
				FROM fiche_detail a
				JOIN fiche f1 ON f1.f_id = a.f_id
				JOIN ( SELECT fd1.f_id, fd1.ad_value
					FROM fiche_detail fd1
					WHERE fd1.ad_id = ".ATTR_IMMEUBLE.") m ON m.f_id = a.f_id
				JOIN ( SELECT fd1.f_id, fd1.ad_value
				FROM fiche_detail fd1
				WHERE fd1.ad_id = ".ATTR_COPROP.") c ON c.f_id = a.f_id
				WHERE f1.fd_id = ".$lot." AND a.ad_id = 1");
		$this->lot_id=$lot;
		$this->immeuble_id=$immeuble;
		$this->coprop_id=$copro;

	}

	function create_table_parameter()
	{
		$sql = <<<EOF
CREATE TABLE coprop.parameter
(
  pr_id text NOT NULL,
  pr_value text,
  CONSTRAINT copro_parameter_pkey PRIMARY KEY (pr_id)
 );
EOF;
		$this->cn->exec_sql($sql);
// load default value
		$array = array(
			'categorie_lot' => $this->lot_id,
			'categorie_coprop' => $this->coprop_id,
			'categorie_immeuble' => $this->immeuble_id,
			'categorie_appel' => 0,
			'poste_appel' => 0,
			'categorie_charge'=>0,
			'journal_appel' => 0
		);

		foreach ($array as $code => $value)
		{
			$this->cn->exec_sql('insert into coprop.parameter(pr_id,pr_value) values ($1,$2)', array($code, $value));
		}
	}
	function create_table()
	{

	       $file=dirname(__FILE__)."/../sql/create_table.sql";
		$this->cn->execute_script($file);

	}
	function upgrade()
	{
		global $cn;
		$version=$cn->get_value('select max(v_id) from coprop.version');
		for ($i=$version+1;$i<=COPROP_VERSION;$i++)
		{
			$file=dirname(__FILE__)."/../sql/upgrade$i.sql";
			$this->cn->execute_script($file);
		}
	}

}

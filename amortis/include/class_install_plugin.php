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

  function __construct(& $p_cn) {
    $this->cn=$p_cn;
  }

  /**
   *@brief install the plugin, create all the needed schema, tables, proc
   * in the database
   *@param $p_dossier is the dossier id
   */
  function install() 
  {
    $this->cn->start();
    // create the schema
    $this->create_schema();
    $this->create_tables();
 
    $this->cn->commit();
  }
  function create_schema() 
  {
    $this->cn->exec_sql('create schema amortissement');
  }
  function create_tables()
  {
    $this->cn->start();
    $table="CREATE TABLE amortissement.amortissement
(
  a_id serial NOT NULL,
  f_id bigint NOT NULL,
  account_deb account_type,
  account_cred account_type,
  a_amount numeric(20,2) NOT NULL DEFAULT 0,
  a_nb_year numeric(4,2) NOT NULL DEFAULT 0,
  a_start integer,
  a_visible character(1) DEFAULT 'Y'::bpchar,
  CONSTRAINT amortissement_pkey PRIMARY KEY (a_id),
  CONSTRAINT amortissement_account_cred_fkey FOREIGN KEY (account_cred)
      REFERENCES tmp_pcmn (pcm_val) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT amortissement_account_deb_fkey FOREIGN KEY (account_deb)
      REFERENCES tmp_pcmn (pcm_val) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT amortissement_f_id_fkey FOREIGN KEY (f_id)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT amortissement_f_id_key UNIQUE (f_id)
)";

    $this->cn->exec_sql($table);

    $table="CREATE TABLE amortissement.version
(
   val integer primary key
)";

    $this->cn->exec_sql($table);

    $table="CREATE TABLE amortissement.amortissement_detail
(
  ad_id serial NOT NULL,
  ad_amount numeric(20,2) NOT NULL DEFAULT 0,
  a_id bigint,
  ad_year integer,
  ad_percentage numeric(5,2),
  CONSTRAINT amortissement_detail_pkey PRIMARY KEY (ad_id),
  CONSTRAINT amortissement_detail_a_id_fkey FOREIGN KEY (a_id)
      REFERENCES amortissement.amortissement (a_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)";

    $idx="CREATE INDEX fki_amortissement
  ON amortissement.amortissement_detail
  USING btree
  (a_id);";
    
    $this->cn->exec_sql($table);

    $table="
CREATE TABLE amortissement.amortissement_histo
(
  ha_id serial NOT NULL,
  a_id bigint,
  h_amount numeric(20,4) NOT NULL,
  jr_internal text,
  h_year integer NOT NULL,
  h_pj text,
  CONSTRAINT amortissement_histo_pkey PRIMARY KEY (ha_id),
  CONSTRAINT amortissement_histo_a_id_fkey FOREIGN KEY (a_id)
      REFERENCES amortissement.amortissement (a_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);";

    $idx="CREATE UNIQUE INDEX amortissement_histo_uq
  ON amortissement.amortissement_histo
  USING btree
  (h_year, a_id);

";
    $this->cn->exec_sql($table);

    $fct="CREATE OR REPLACE FUNCTION amortissement.amortissement_ins()
  RETURNS trigger AS
\$BODY\$

declare 
i int;
nyear int;
n_ad_amount numeric(20,2);
total numeric(20,2);
last_ad_id bigint;
begin
	i :=0;
	total := 0;
	loop
	   
	   if i = NEW.a_nb_year then
		exit ;
	   end if;
           nyear :=  NEW.a_start +i;
           n_ad_amount := NEW.a_amount/NEW.a_nb_year;

           total := total + n_ad_amount;

           if total > NEW.a_amount then
		n_ad_amount := NEW.a_amount -  total - n_ad_amount;
	   end if;

           insert into amortissement.amortissement_detail(ad_year,ad_amount,a_id) values (nyear,n_ad_amount,NEW.a_id) returning ad_id into last_ad_id;
           insert into amortissement.amortissement_histo(a_id,h_amount,h_year) values (NEW.a_id,0,nyear);
	   i := i+1;
	end loop;
	if total < NEW.a_amount then
		n_ad_amount := n_ad_amount+NEW.a_amount-total;
		update amortissement.amortissement_detail set ad_amount=n_ad_amount where ad_id=last_ad_id;
	end if;
	return NEW;
end;
\$BODY\$
  LANGUAGE 'plpgsql'";

    $this->cn->exec_sql($fct);

    $comment_fct="COMMENT ON FUNCTION amortissement.amortissement_ins() IS 'Fill the table amortissement_detail after an insert'";

    $this->cn->exec_sql($comment_fct);

    $fct="CREATE OR REPLACE FUNCTION amortissement.amortissement_up()
  RETURNS trigger AS
\$BODY\$

declare 
i int;
nyear int;
n_ad_amount numeric(20,2);
total numeric(20,2);
last_ad_id bigint;
n_pct numeric(5,2);
begin
	i :=0;
	if NEW.a_nb_year != OLD.a_nb_year or NEW.a_start != OLD.a_start then
	   delete from amortissement.amortissement_detail where a_id=NEW.a_id;

           n_ad_amount := round(NEW.a_amount/NEW.a_nb_year,2);
	   n_pct := round(NEW.a_amount / n_ad_amount,2);
	 loop
	   
	   if i = NEW.a_nb_year then
		exit ;
	   end if;
           nyear :=  NEW.a_start +i;


           total := round(total + n_ad_amount,2);

           if total > NEW.a_amount then
		n_ad_amount := NEW.a_amount -  total - n_ad_amount;
	   end if;
raise notice 'ad_amount % total %s n_pct %',n_ad_amount,total,n_pct;	
           insert into amortissement.amortissement_detail(ad_year,ad_amount,ad_percentage,a_id) values (nyear,n_ad_amount,n_pct,NEW.a_id) returning ad_id into last_ad_id;
	   i := i+1;
	end loop;
raise notice 'Total %',total;
	if total < NEW.a_amount then
		n_ad_amount := n_ad_amount+NEW.a_amount-total;
		update amortissement.amortissement_detail set ad_amount=n_ad_amount where ad_id=last_ad_id;
	end if;
   end if;
   return NEW;
end;
\$BODY\$
  LANGUAGE 'plpgsql' ";

    $this->cn->exec_sql($fct);


  $trigger="CREATE TRIGGER amortissement_after_ins
  AFTER INSERT
  ON amortissement.amortissement
  FOR EACH ROW
  EXECUTE PROCEDURE amortissement.amortissement_ins();";

  $this->cn->exec_sql($trigger);

    $trigger="CREATE TRIGGER amortissement_after_up
  AFTER UPDATE
  ON amortissement.amortissement
  FOR EACH ROW
  EXECUTE PROCEDURE amortissement.amortissement_up();";

  $this->cn->exec_sql($trigger);
  $this->cn->exec_sql("insert into amortissement.version values (1)");
  $this->cn->commit();
  }
}
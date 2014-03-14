begin;

create table rapport_advanced.listing
(
    l_id    serial primary key,
    l_name text check (length(trim(l_name)) > 0 and l_name is not null),
    l_description   text,
    l_lob oid,
    l_filename text,
    l_mimetype text,
    l_size bigint default 0,
    fd_id bigint references fiche_def (fd_id) on update cascade on delete set null
);
CREATE TABLE rapport_advanced.listing_param
(
  lp_id serial NOT NULL,
  l_id bigint,
  lp_code text NOT NULL,
  lp_comment text,
  l_order integer,
  ad_id bigint,
  lp_card_saldo character(1),
  lp_with_card character(1),
  tmp_val account_type,
  tva_id integer,
  fp_formula text,
  fp_signed integer,
  jrn_def_type character(3),
  tt_id integer,
  with_tmp_val account_type,
  type_sum_account bigint,
  operation_pcm_val account_type,
  jrn_def_id bigint,
  date_paid integer DEFAULT 0,
  type_detail text,
  lp_paid text, -- used by Listing_Compute_Historique
  lp_histo integer DEFAULT 0, -- 0 : no history...
  CONSTRAINT listing_param_pkey PRIMARY KEY (lp_id ),
  CONSTRAINT fk_listing_param_detail_attr_def FOREIGN KEY (ad_id)
      REFERENCES attr_def (ad_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_listing_param_listing FOREIGN KEY (l_id)
      REFERENCES rapport_advanced.listing (l_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT listing_param_jrn_def_id_fkey FOREIGN KEY (jrn_def_id)
      REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED,
  CONSTRAINT listing_param_tt_id_fkey FOREIGN KEY (tt_id)
      REFERENCES rapport_advanced.total_type (tt_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT listing_param_tva_id_fkey FOREIGN KEY (tva_id)
      REFERENCES tva_rate (tva_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT listing_param_type_sum_account_fkey FOREIGN KEY (type_sum_account)
      REFERENCES rapport_advanced.total_type_account (tt_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL,
  CONSTRAINT c_lp_code CHECK (length(btrim(lp_code)) > 0),
  CONSTRAINT ck_type_detail CHECK (type_detail = ANY (ARRAY['ATTR'::text, 'COMP'::text, 'FORM'::text, 'SALDO'::text, 'ACCOUNT'::text])),
  CONSTRAINT listing_param_lp_card_saldo_check CHECK (lp_card_saldo = ANY (ARRAY['C'::bpchar, 'D'::bpchar, 'S'::bpchar, NULL::bpchar])),
  CONSTRAINT listing_param_lp_with_card_check CHECK (lp_with_card = ANY (ARRAY['Y'::bpchar, 'N'::bpchar]))
);
COMMENT ON COLUMN rapport_advanced.listing_param.lp_paid IS 'used by Listing_Compute_Historique';
COMMENT ON COLUMN rapport_advanced.listing_param.lp_histo IS '0 : no history
1 : with history';

CREATE OR REPLACE FUNCTION rapport_advanced.formulaire_param_detail_jrn_def_id_ins_upd()
  RETURNS trigger AS
$BODY$
declare
	jrn_def_id integer;
begin
	if NEW.jrn_def_id = -1 then
		jrn_def_id=NULL;
		NEW.jrn_def_id := jrn_def_id;
	end if;
	return NEW;
end;
$BODY$
  LANGUAGE plpgsql ;


CREATE OR REPLACE FUNCTION rapport_advanced.listing_param_code_transform()
 RETURNS trigger
    AS $function$
    declare
        sResult text;
    begin
        sResult := lower(NEW.lp_code);

        sResult := translate(sResult,E'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');
        sResult := translate(sResult,E' $€µ£%.+-/\\!(){}(),;_&|"#''^<>*','');

        NEW.lp_code=upper(sResult);

return NEW;

end;
$function$
 LANGUAGE plpgsql;

-- Trigger: listing_param_code_transform_trg on rapport_advanced.listing_param


CREATE TRIGGER listing_param_code_transform_trg
  BEFORE INSERT OR UPDATE OF lp_code
  ON rapport_advanced.listing_param
  FOR EACH ROW
  EXECUTE PROCEDURE rapport_advanced.listing_param_code_transform();

-- Trigger: listing_param_trg on rapport_advanced.listing_param


CREATE TRIGGER listing_param_trg
  BEFORE INSERT OR UPDATE OF jrn_def_id
  ON rapport_advanced.listing_param
  FOR EACH ROW
  EXECUTE PROCEDURE rapport_advanced.formulaire_param_detail_jrn_def_id_ins_upd();


CREATE TABLE rapport_advanced.listing_compute
(
  lc_id bigserial NOT NULL, -- PK
  l_name text, -- Description or note
  l_description text, -- Description or note
  l_id bigint, -- FK to listing
  l_start date, -- start data
  l_end date, -- end_date
  l_keep character(1) NOT NULL DEFAULT 'N'::bpchar, -- If yes, it is keeped with N it will deleted
  l_timestamp timestamp without time zone DEFAULT now(),
  CONSTRAINT listing_compute_pkey PRIMARY KEY (lc_id ),
  CONSTRAINT listing_fk FOREIGN KEY (l_id)
      REFERENCES rapport_advanced.listing (l_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

COMMENT ON TABLE rapport_advanced.listing_compute  IS 'Parent of listing_compute_detail';
COMMENT ON COLUMN rapport_advanced.listing_compute.lc_id IS 'PK';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_id IS 'FK to listing';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_name IS 'Title';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_description IS 'Description or note';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_start IS 'start data';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_end IS 'end_date';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_keep IS 'If yes, it is keeped with N it will deleted';


CREATE OR REPLACE FUNCTION rapport_advanced.listing_compute_trg() 
 returns trigger 
 as 
$_BODY_$
declare 
begin
 NEW.l_timestamp=now() ;
return NEW;
end;
$_BODY_$ LANGUAGE plpgsql;
CREATE TRIGGER listing_compute_trg
 BEFORE 
 INSERT OR UPDATE 
 on rapport_advanced.listing_compute
 FOR EACH ROW EXECUTE PROCEDURE rapport_advanced.listing_compute_trg();


begin;
-- Table: rapport_advanced.listing_compute_detail
CREATE TABLE rapport_advanced.listing_compute_detail
(
  ld_id bigserial NOT NULL, -- PK
  lc_id bigint, -- fk to listing_compute
  ld_value_date date, -- Used when computed value is a date
  ld_value_numeric numeric(20,4), -- Used when computed value is numeric
  ld_value_text text, -- Used when computed value is a text
  lp_id bigint, -- fk to listing_param, used for the condition
  lf_id bigint, -- FK to listing_compute_fiche
  lc_code text,
  lc_comment text,
  lc_order bigint,
  lc_histo integer DEFAULT 0,
  CONSTRAINT listing_compute_detail_pkey PRIMARY KEY (ld_id ),
  CONSTRAINT listing_compute_detail_lc_id_fkey FOREIGN KEY (lc_id)
      REFERENCES rapport_advanced.listing_compute (lc_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT listing_compute_detail_lp_id_fkey FOREIGN KEY (lp_id) REFERENCES rapport_advanced.listing_param (lp_id) MATCH SIMPLE ON UPDATE SET NULL ON DELETE SET NULL);
COMMENT ON TABLE rapport_advanced.listing_compute_detail  IS 'Detail of computing listing_param';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.ld_id IS 'PK';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.lc_id IS 'fk to listing_compute';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.ld_value_date IS 'Used when computed value is a date';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.ld_value_numeric IS 'Used when computed value is numeric';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.ld_value_text IS 'Used when computed value is a text';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.lp_id IS 'fk to listing_param, used for the condition';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.lf_id IS 'FK to listing_compute_fiche';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.lc_code IS 'code from listing_param';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.lc_comment IS 'comment from listing_param';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.lc_order IS 'order from listing_param';
COMMENT ON COLUMN rapport_advanced.listing_compute_detail.lc_histo IS '0 : no history 1 : with history';

CREATE INDEX fki_listing_compute_fiche_lf_id2_fk
  ON rapport_advanced.listing_compute_detail
  USING btree
  (lf_id );

-- Index: rapport_advanced.fki_listing_compute_fiche_lf_id_fk

CREATE INDEX fki_listing_compute_fiche_lf_id_fk
  ON rapport_advanced.listing_compute_detail
  USING btree
  (lf_id );

CREATE TABLE rapport_advanced.listing_compute_fiche
(
  lf_id serial NOT NULL, -- PK
  f_id bigint NOT NULL, -- FK to fiche
  lf_lob oid, -- Generated file if any
  lf_pdf oid, -- Generated file if any
  lf_filename text, -- Name of the generated file. It should be based on the name of the template + unique id
  lf_pdf_filename text, -- Generated file if any
  lf_mimetype text, -- Same mimetype as in table listing
  lc_id bigint,
  lf_action_included text,
  lf_email_send_date timestamp,
  lf_email_send_result text,
  CONSTRAINT listing_compute_fiche_pkey PRIMARY KEY (lf_id ),
  CONSTRAINT fk_listing_compute_lc_id FOREIGN KEY (lc_id)
      REFERENCES rapport_advanced.listing_compute (lc_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT listing_compute_fiche_f_id_fkey FOREIGN KEY (f_id)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
COMMENT ON TABLE rapport_advanced.listing_compute_fiche  IS 'Content :card document to generate';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_id IS 'PK';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.f_id IS 'FK to fiche';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_lob IS 'Generated file if any';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_pdf IS 'PDF File';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_filename IS 'Name of the generated file. It should be based on the name of the template + unique id';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_pdf_filename IS 'Name of the PDF file.base of lf_filename';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_mimetype IS 'Same mimetype as in table listing';

COMMENT ON COLUMN rapport_advanced.listing_param.lp_paid IS 'used by Listing_Compute_Historique';

CREATE TABLE rapport_advanced.listing_compute_historique
(
  lh_id bigserial NOT NULL, -- PK
  ld_id bigint, -- FK to listing_compute_detail
  jr_id bigint, -- FK to jrn
  CONSTRAINT listing_compute_historique_pkey PRIMARY KEY (lh_id ),
  CONSTRAINT listing_compute_historique_jr_id_fkey FOREIGN KEY (jr_id)
      REFERENCES jrn (jr_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT listing_compute_historique_ld_id_fkey FOREIGN KEY (ld_id)
      REFERENCES rapport_advanced.listing_compute_detail (ld_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
COMMENT ON TABLE rapport_advanced.listing_compute_historique  IS 'Content : history of operation for a detail';
COMMENT ON COLUMN rapport_advanced.listing_compute_historique.lh_id IS 'PK';
COMMENT ON COLUMN rapport_advanced.listing_compute_historique.ld_id IS 'FK to listing_compute_detail';
COMMENT ON COLUMN rapport_advanced.listing_compute_historique.jr_id IS 'FK to jrn';

create table rapport_advanced.rapav_parameter
(
    param_code text primary key,
    param_value text 
);
insert into rapport_advanced.rapav_parameter values ('FROM','phpcompta@localhost');

insert into rapport_advanced.version(version_id,version_note) values (5,'Ajout des listings');

commit;

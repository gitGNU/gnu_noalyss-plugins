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
create table rapport_advanced.listing_param
(
 lp_id serial primary key,
 l_id bigint constraint fk_listing_param_listing references rapport_advanced.listing(l_id) on update cascade on delete cascade,
 lp_code text not null constraint c_lp_code check (length (trim(lp_code))> 0),
 lp_comment text,
 l_card int default 0,
 l_order int,
 ad_id bigint constraint fk_listing_param_detail_attr_def references attr_def (ad_id),
 lp_card_saldo char(1) check (lp_card_saldo in ('C','D','S',NULL)),
 lp_with_card char (1) check (lp_with_card in ('Y','N')),
 tmp_val account_type,
 tva_id integer,
 fp_formula text,
 fp_signed integer,
 jrn_def_type character(3),
 tt_id integer,
 type_detail integer,
 with_tmp_val account_type,
 type_sum_account bigint,
 operation_pcm_val account_type,
 jrn_def_id bigint, -- FK to jrn_def, if null then all the ledgers are concerned
 date_paid integer DEFAULT 0
);


ALTER TABLE rapport_advanced.listing_param
  ADD CONSTRAINT fk_listing_param_type_row_detail FOREIGN KEY (type_detail)
      REFERENCES rapport_advanced.type_row_detail (tr_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL;
ALTER TABLE rapport_advanced.listing_param
  ADD CONSTRAINT listing_param_jrn_def_id_fkey FOREIGN KEY (jrn_def_id)
      REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED;
ALTER TABLE rapport_advanced.listing_param
  ADD CONSTRAINT listing_param_tt_id_fkey FOREIGN KEY (tt_id)
      REFERENCES rapport_advanced.total_type (tt_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE rapport_advanced.listing_param
  ADD CONSTRAINT listing_param_tva_id_fkey FOREIGN KEY (tva_id)
      REFERENCES tva_rate (tva_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE rapport_advanced.listing_param
  ADD CONSTRAINT listing_param_type_sum_account_fkey FOREIGN KEY (type_sum_account)
      REFERENCES rapport_advanced.total_type_account (tt_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL;
CREATE TRIGGER listing_param_trg
  BEFORE INSERT OR UPDATE OF jrn_def_id
  ON rapport_advanced.listing_param
  FOR EACH ROW
  EXECUTE PROCEDURE rapport_advanced.formulaire_param_detail_jrn_def_id_ins_upd();
commit;
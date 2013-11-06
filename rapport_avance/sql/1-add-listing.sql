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
 l_order int
);
create table rapport_advanced.listing_param_detail
(
    ad_id bigint constraint fk_listing_param_detail_attr_def references attr_def (ad_id),
    lp_card_saldo char(1) check (lp_card_saldo in ('C','D','S',NULL)),
    lp_with_card char (1) check (lp_with_card in ('Y','N')),
    lp_id bigint constraint fk_listing_param_detail_listing_param references rapport_advanced.listing_param (lp_id)
)  inherits (rapport_Advanced.formulaire_param_detail);

ALTER TABLE rapport_advanced.listing_param_detail
  ADD CONSTRAINT fk_listing_param_detail_type_row_detail FOREIGN KEY (type_detail)
      REFERENCES rapport_advanced.type_row_detail (tr_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL;
ALTER TABLE rapport_advanced.listing_param_detail
  ADD CONSTRAINT listing_param_detail_jrn_def_id_fkey FOREIGN KEY (jrn_def_id)
      REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED;
ALTER TABLE rapport_advanced.listing_param_detail
  ADD CONSTRAINT listing_param_detail_tt_id_fkey FOREIGN KEY (tt_id)
      REFERENCES rapport_advanced.total_type (tt_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE rapport_advanced.listing_param_detail
  ADD CONSTRAINT listing_param_detail_tva_id_fkey FOREIGN KEY (tva_id)
      REFERENCES tva_rate (tva_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE rapport_advanced.listing_param_detail
  ADD CONSTRAINT listing_param_detail_type_sum_account_fkey FOREIGN KEY (type_sum_account)
      REFERENCES rapport_advanced.total_type_account (tt_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL;
CREATE TRIGGER listing_param_detail_trg
  BEFORE INSERT OR UPDATE OF jrn_def_id
  ON rapport_advanced.listing_param_detail
  FOR EACH ROW
  EXECUTE PROCEDURE rapport_advanced.formulaire_param_detail_jrn_def_id_ins_upd();

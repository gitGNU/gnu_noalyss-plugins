create table rapport_advanced.listing
(
    l_id    serial primary key,
    l_description   text,
    l_lob oid,
    l_mimetype text,
    l_size bigint,
    fd_id bigint references fiche_def (fd_id) on update cascade on delete null
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

--ALTER TABLE ONLY rapport_advanced.listing_param_detail
 --   ADD CONSTRAINT listing_param_detail_p_type_fkey FOREIGN KEY (p_type) REFERENCES rapport_advanced.type_row(p_type);


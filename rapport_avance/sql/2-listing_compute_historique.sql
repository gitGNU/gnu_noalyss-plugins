
ALTER TABLE rapport_advanced.listing_param ADD COLUMN lp_paid text;
COMMENT ON COLUMN rapport_advanced.listing_param.lp_paid IS 'used by Listing_Compute_Historique';

CREATE TABLE rapport_advanced.listing_compute_historique
(
  lh_id bigserial NOT NULL, -- PK
  lc_id bigint, -- FK to listing_compute_detail
  jr_id bigint, -- FK to jrn
  CONSTRAINT listing_compute_historique_pkey PRIMARY KEY (lh_id ),
  CONSTRAINT listing_compute_historique_jr_id_fkey FOREIGN KEY (jr_id)
      REFERENCES jrn (jr_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT listing_compute_historique_ld_id_fkey FOREIGN KEY (lc_id)
      REFERENCES rapport_advanced.listing_compute_detail (ld_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE rapport_advanced.listing_compute_historique
  OWNER TO phpcompta;
COMMENT ON TABLE rapport_advanced.listing_compute_historique
  IS 'Content : history of operation for a detail';
COMMENT ON COLUMN rapport_advanced.listing_compute_historique.lh_id IS 'PK';
COMMENT ON COLUMN rapport_advanced.listing_compute_historique.lc_id IS 'FK to listing_compute_detail';
COMMENT ON COLUMN rapport_advanced.listing_compute_historique.jr_id IS 'FK to jrn';


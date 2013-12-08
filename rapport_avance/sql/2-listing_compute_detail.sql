-- Table: rapport_advanced.listing_compute_detail

-- DROP TABLE rapport_advanced.listing_compute_detail;

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
  CONSTRAINT listing_compute_detail_pkey PRIMARY KEY (ld_id ),
  CONSTRAINT listing_compute_detail_lc_id_fkey FOREIGN KEY (lc_id)
      REFERENCES rapport_advanced.listing_compute (lc_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT listing_compute_detail_lp_id_fkey FOREIGN KEY (lp_id)
      REFERENCES rapport_advanced.listing_param (lp_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL
)
WITH (
  OIDS=FALSE
);
ALTER TABLE rapport_advanced.listing_compute_detail
  OWNER TO phpcompta;
COMMENT ON TABLE rapport_advanced.listing_compute_detail
  IS 'Detail of computing listing_param';
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


-- Index: rapport_advanced.fki_listing_compute_fiche_lf_id2_fk

-- DROP INDEX rapport_advanced.fki_listing_compute_fiche_lf_id2_fk;

CREATE INDEX fki_listing_compute_fiche_lf_id2_fk
  ON rapport_advanced.listing_compute_detail
  USING btree
  (lf_id );

-- Index: rapport_advanced.fki_listing_compute_fiche_lf_id_fk

-- DROP INDEX rapport_advanced.fki_listing_compute_fiche_lf_id_fk;

CREATE INDEX fki_listing_compute_fiche_lf_id_fk
  ON rapport_advanced.listing_compute_detail
  USING btree
  (lf_id );

--
--

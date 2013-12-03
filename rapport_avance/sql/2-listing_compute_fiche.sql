-- Table: rapport_advanced.listing_compute_fiche

-- DROP TABLE rapport_advanced.listing_compute_fiche;

CREATE TABLE rapport_advanced.listing_compute_fiche
(
  lf_id serial NOT NULL, -- PK
  f_id bigint NOT NULL, -- FK to fiche
  lf_lob oid, -- Generated file if any
  lf_filename text, -- Name of the generated file. It should be based on the name of the template + unique id
  lf_mimetype text, -- Same mimetype as in table listing
  CONSTRAINT listing_compute_fiche_pkey PRIMARY KEY (lf_id ),
  CONSTRAINT listing_compute_fiche_f_id_fkey FOREIGN KEY (f_id)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE rapport_advanced.listing_compute_fiche
  OWNER TO phpcompta;
COMMENT ON TABLE rapport_advanced.listing_compute_fiche
  IS 'Content :
card
document to generate';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_id IS 'PK';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.f_id IS 'FK to fiche';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_lob IS 'Generated file if any';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_filename IS 'Name of the generated file. It should be based on the name of the template + unique id';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_mimetype IS 'Same mimetype as in table listing';


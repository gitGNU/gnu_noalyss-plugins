-- Table: rapport_advanced.listing_compute_fiche

-- DROP TABLE rapport_advanced.listing_compute_fiche;

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
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_pdf IS 'PDF File';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_filename IS 'Name of the generated file. It should be based on the name of the template + unique id';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_pdf_filename IS 'Name of the PDF file.base of lf_filename';
COMMENT ON COLUMN rapport_advanced.listing_compute_fiche.lf_mimetype IS 'Same mimetype as in table listing';


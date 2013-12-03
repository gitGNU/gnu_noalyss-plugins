-- Table: rapport_advanced.listing_compute

-- DROP TABLE rapport_advanced.listing_compute;

CREATE TABLE rapport_advanced.listing_compute
(
  lc_id bigserial NOT NULL, -- PK
  l_id bigint, -- FK to listing
  l_description text, -- Description or note
  l_start date, -- start data
  l_end date, -- end_date
  l_keep character(1) NOT NULL DEFAULT 'N'::bpchar, -- If yes, it is keeped with N it will deleted
  l_timestamp timestamp without time zone DEFAULT now(),
  CONSTRAINT listing_compute_pkey PRIMARY KEY (lc_id ),
  CONSTRAINT listing_fk FOREIGN KEY (l_id)
      REFERENCES rapport_advanced.listing (l_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE rapport_advanced.listing_compute
  OWNER TO phpcompta;
COMMENT ON TABLE rapport_advanced.listing_compute
  IS 'Parent of listing_compute_detail
';
COMMENT ON COLUMN rapport_advanced.listing_compute.lc_id IS 'PK';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_id IS 'FK to listing';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_description IS 'Description or note';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_start IS 'start data';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_end IS 'end_date';
COMMENT ON COLUMN rapport_advanced.listing_compute.l_keep IS 'If yes, it is keeped with N it will deleted';


-- Trigger: listing_compute_trg on rapport_advanced.listing_compute

-- DROP TRIGGER listing_compute_trg ON rapport_advanced.listing_compute;

CREATE TRIGGER listing_compute_trg
  BEFORE INSERT OR UPDATE
  ON rapport_advanced.listing_compute
  FOR EACH ROW
  EXECUTE PROCEDURE rapport_advanced.listing_compute_trg();


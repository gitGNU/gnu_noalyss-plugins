-- Table: rapport_advanced.listing_condition

-- DROP TABLE rapport_advanced.listing_condition;

CREATE TABLE rapport_advanced.listing_condition
(
  lc_id bigserial NOT NULL, -- PK
  CONSTRAINT listing_condition_pkey PRIMARY KEY (lc_id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE rapport_advanced.listing_condition
  OWNER TO phpcompta;
COMMENT ON TABLE rapport_advanced.listing_condition
  IS 'Content condition for a generation';
COMMENT ON COLUMN rapport_advanced.listing_condition.lc_id IS 'PK';


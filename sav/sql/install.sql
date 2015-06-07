begin;

CREATE SCHEMA service_after_sale;
COMMENT ON SCHEMA service_after_sale IS 'Contains element for the SAV plugin';
CREATE SEQUENCE service_after_sale.intervention_id_seq;
CREATE SEQUENCE service_after_sale.parameter_id_seq;
CREATE SEQUENCE service_after_sale.repair_card_number_seq;
CREATE SEQUENCE service_after_sale.sav_repair_card_id_seq;
CREATE SEQUENCE service_after_sale.spare_part_id_seq;
CREATE TABLE service_after_sale.sav_repair_card
(
  id serial NOT NULL,
  f_id_customer integer, -- Customer card
  f_id_personnel_received integer, -- Not used : card for crew
  f_id_personnel_done integer, -- Not used card for crew
  date_reception timestamp without time zone, -- Reception of the good
  date_start timestamp without time zone, -- Start of the work
  date_end timestamp without time zone, -- Date end of the repair
  garantie character varying(180), -- Warranty number - code
  description_failure text, -- Description of the issue
  jr_id integer, -- Link to the invoice
  tech_creation_date timestamp without time zone DEFAULT now(), -- Not used
  repair_number text, -- Not used
  card_status character(1) not null, -- Status is En-cours Draft Closed
  f_id_good bigint, -- Card of returned good
  CONSTRAINT repair_card_pkey PRIMARY KEY (id ),
  CONSTRAINT repair_card_f_id_customer_fkey FOREIGN KEY (f_id_customer)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT repair_card_f_id_personnel_done_fkey FOREIGN KEY (f_id_personnel_done)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT repair_card_f_id_personnel_received_fkey FOREIGN KEY (f_id_personnel_received)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT repair_card_jr_id_fkey FOREIGN KEY (jr_id)
      REFERENCES jrn (jr_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);

COMMENT ON TABLE service_after_sale.sav_repair_card  IS 'Main table : contains the repair card';
COMMENT ON COLUMN service_after_sale.sav_repair_card.f_id_customer IS 'Customer card';
COMMENT ON COLUMN service_after_sale.sav_repair_card.f_id_personnel_received IS 'Not used : card for crew';
COMMENT ON COLUMN service_after_sale.sav_repair_card.f_id_personnel_done IS 'Not used card for crew';
COMMENT ON COLUMN service_after_sale.sav_repair_card.date_reception IS 'Reception of the good';
COMMENT ON COLUMN service_after_sale.sav_repair_card.date_start IS 'Start of the work';
COMMENT ON COLUMN service_after_sale.sav_repair_card.date_end IS 'Date end of the repair';
COMMENT ON COLUMN service_after_sale.sav_repair_card.garantie IS 'Warranty number - code';
COMMENT ON COLUMN service_after_sale.sav_repair_card.description_failure IS 'Description of the issue';
COMMENT ON COLUMN service_after_sale.sav_repair_card.jr_id IS 'Link to the invoice';
COMMENT ON COLUMN service_after_sale.sav_repair_card.tech_creation_date IS 'Not used';
COMMENT ON COLUMN service_after_sale.sav_repair_card.repair_number IS 'Not used';
COMMENT ON COLUMN service_after_sale.sav_repair_card.card_status IS 'Status is En-cours Draft Closed';
COMMENT ON COLUMN service_after_sale.sav_repair_card.f_id_good IS 'Card of returned good';

CREATE TABLE service_after_sale.sav_parameter
(
  code text not null,
  value text not null,
  description text,
  id integer NOT NULL DEFAULT nextval('service_after_sale.parameter_id_seq'::regclass),
  CONSTRAINT parameter_pkey PRIMARY KEY (id )
);

COMMENT ON TABLE service_after_sale.sav_parameter  IS 'Parameter of the plugin';
insert into service_after_sale.sav_parameter (code,value, description) values 
('good',-1,'matériel retourné'),
('spare',-1,'Spare part'),
('ledger',2,'Default ledger of sales'),
('workhour',-1,'Workhour card id');

ALTER TABLE service_after_sale.sav_parameter
  ADD CONSTRAINT sav_parameter_code_key UNIQUE(code );

COMMENT ON TABLE service_after_sale.sav_parameter   IS 'Parameter of the plugin';

CREATE TABLE service_after_sale.sav_spare_part
(
  id bigint NOT NULL DEFAULT nextval('service_after_sale.spare_part_id_seq'::regclass),
  f_id_material integer  NOT NULL, -- FK to Fiche
  repair_card_id integer  NOT NULL, -- FK to sav_repair_card
  quantity numeric(6,2) NOT NULL, -- quantity of spare_part
  CONSTRAINT spare_part_pkey PRIMARY KEY (id ),
  CONSTRAINT sav_spare_part_material_fk FOREIGN KEY (f_id_material)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT sav_spare_part_repair FOREIGN KEY (repair_card_id)
      REFERENCES service_after_sale.sav_repair_card (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
COMMENT ON TABLE service_after_sale.sav_spare_part  IS 'Spare_part';
COMMENT ON COLUMN service_after_sale.sav_spare_part.f_id_material IS 'FK to Fiche ';
COMMENT ON COLUMN service_after_sale.sav_spare_part.repair_card_id IS 'FK to sav_repair_card';
COMMENT ON COLUMN service_after_sale.sav_spare_part.quantity IS 'quantity of spare_part';

CREATE TABLE service_after_sale.sav_version
(
  version_id bigint NOT NULL, -- PK : version id
  version_comment text, -- Comment about version
  version_date timestamp with time zone NOT NULL DEFAULT now(), -- Date of update
  CONSTRAINT sav_version_pkey PRIMARY KEY (version_id )
);

COMMENT ON TABLE service_after_sale.sav_version  IS 'Version of the schema';
COMMENT ON COLUMN service_after_sale.sav_version.version_id IS 'PK : version id';
COMMENT ON COLUMN service_after_sale.sav_version.version_comment IS 'Comment about version';
COMMENT ON COLUMN service_after_sale.sav_version.version_date IS 'Date of update';
insert into service_after_sale.sav_version(version_id,version_comment) values (1,'Install');

CREATE TABLE service_after_sale.sav_workhour
(
  id integer NOT NULL DEFAULT nextval('service_after_sale.intervention_id_seq'::regclass),
  total_workhour numeric(20,4)  NOT NULL, -- amount of workhour
  repair_card_id integer  NOT NULL , -- FK to sav_repair_card
  work_description text, -- Description of the work (optionnal)
  f_id_workhour bigint  NOT NULL, -- Card for workhour
  CONSTRAINT intervention_pkey PRIMARY KEY (id ),
  CONSTRAINT sav_workhour_f_id_workhour_fkey FOREIGN KEY (f_id_workhour)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT sav_workhour_repair_card_id_fkey FOREIGN KEY (repair_card_id)
      REFERENCES service_after_sale.sav_repair_card (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
COMMENT ON TABLE service_after_sale.sav_workhour  IS 'Workhours';
COMMENT ON COLUMN service_after_sale.sav_workhour.total_workhour IS 'amount of workhour';
COMMENT ON COLUMN service_after_sale.sav_workhour.repair_card_id IS 'FK to sav_repair_card';
COMMENT ON COLUMN service_after_sale.sav_workhour.work_description IS 'Description of the work (optionnal)';
COMMENT ON COLUMN service_after_sale.sav_workhour.f_id_workhour IS 'Card for workhour';
commit;


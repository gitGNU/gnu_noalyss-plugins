-- Table: repair_card

-- DROP TABLE repair_card;
-- create schema service_after_sale;

CREATE TABLE service_after_sale.repair_card
(
  id serial NOT NULL,
  f_id_customer integer,
  f_id_personnel_received integer,
  f_id_personnel_done integer,
  f_id_spare_part integer,
  date_reception time without time zone,
  date_start time without time zone,
  date_end time without time zone,
  garantie character varying(180),
  description_fail text,
  jr_id integer,
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
)
;

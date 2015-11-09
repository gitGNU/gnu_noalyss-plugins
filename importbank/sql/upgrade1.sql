begin;
ALTER TABLE importbank.temp_bank ADD COLUMN is_checked integer;
ALTER TABLE importbank.temp_bank ALTER COLUMN is_checked SET DEFAULT 0;

CREATE TABLE importbank.suggest_bank
(
  id bigserial NOT NULL, -- pk
  temp_bank_id bigint, -- FK to temp_bank
  jr_id bigint, -- Possible operation from jrn
  f_id bigint NOT NULL,
  CONSTRAINT suggest_bank_pkey PRIMARY KEY (id ),
  CONSTRAINT suggest_bank_temp_bank_id_fkey FOREIGN KEY (temp_bank_id)
      REFERENCES importbank.temp_bank (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
;
COMMENT ON TABLE importbank.suggest_bank
  IS 'Contains the possible reconciliation for tables from temp_bank';
COMMENT ON COLUMN importbank.suggest_bank.id IS 'pk';
COMMENT ON COLUMN importbank.suggest_bank.temp_bank_id IS 'FK to temp_bank';
COMMENT ON COLUMN importbank.suggest_bank.jr_id IS 'Possible operation from jrn';

insert into importbank.version values (1);
commit;

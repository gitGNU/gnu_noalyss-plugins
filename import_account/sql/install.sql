create schema impacc;
create table impacc.version (
				v_id bigint primary key,
				v_date date default now(),
				v_text text
			);
insert into impacc.version(v_id,v_text) values (1,'install');

CREATE TABLE impacc.parameter_tva
(
    pt_id serial NOT NULL,
    tva_id bigint not null references tva_rate(tva_id) on delete cascade on update cascade,
    tva_code varchar(10) not null,
    CONSTRAINT parameter_tva_pkey PRIMARY KEY (pt_id )
);

CREATE TABLE impacc.import_file
(
  id serial NOT NULL,
  i_filename text not null,
  i_tmpname text not null,
  i_type text not null,
  i_date_transfer timestamp without time zone,
  i_date_import timestamp without time zone,
  CONSTRAINT import_file_pkey PRIMARY KEY (id )
)
;
comment on table impacc.import_file is '';
comment on column impacc.import_file.i_filename is '';


CREATE TABLE impacc.import_detail
(
  id serial NOT NULL,
  import_id bigint,
  id_date text,
  id_code_group character varying(10),
  id_nb_row integer,
  id_pj character varying(20),
  id_acc character varying(255),
  id_acc_second character varying(255),
  id_quant character varying(255),
  id_amount_novat character varying(255),
  id_amount_vat character varying(255),
  tva_code character varying(10),
  jr_id bigint,
  id_status integer,
  id_message text,
  id_label text,
  id_date_limit text,
  id_date_payment text,
  id_date_conv text,
  id_date_limit_conv text,
  id_date_payment_conv text,
  id_quant_conv text,
  id_amount_novat_conv text,
  id_amount_vat_conv text,
  CONSTRAINT import_detail_pkey PRIMARY KEY (id ),
  CONSTRAINT import_detail_jr_id_fkey FOREIGN KEY (jr_id)
      REFERENCES jrn (jr_id) MATCH SIMPLE
      ON UPDATE cascade ON DELETE cascade
);
comment on table impacc.import_detail is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.jr_id  is 'jrn.jr_id corresponding operation ';
comment on column impacc.import_detail.id_message is 'Contains the error code , separated by a comma';
comment on column impacc.import_detail.id_status  is '0 means OK,-1 too few row, 1 error see in id_message for detail , 2 transferred successfully';

CREATE TABLE impacc.import_csv
(
  id serial NOT NULL, -- PK
  s_decimal "char",
  s_thousand "char",
  s_encoding text,
  jrn_def_id integer not null,
  s_surround "char",
  s_delimiter "char",
  import_id integer,
  s_date_format integer not null,
  CONSTRAINT import_csv_pkey PRIMARY KEY (id ),
 CONSTRAINT import_csv_import_id_fkey FOREIGN KEY (import_id)
      REFERENCES impacc.import_file (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
COMMENT ON TABLE impacc.import_csv   IS 'Record the setting for CSV';
COMMENT ON COLUMN impacc.import_csv.id IS 'PK';


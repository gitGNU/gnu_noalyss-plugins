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

create table impacc.import (
    id serial primary key,
    jrn_def_id bigint references jrn_def(jrn_def_id) on delete cascade on update cascade,
    i_date_transfer timestamp ,
    i_date_import timestamp
);

comment on table impacc.import is '';
comment on column impacc.import.jrn_def_id is '';
comment on column impacc.import.jrn_def_id is '';
comment on column impacc.import.jrn_def_id is '';
comment on column impacc.import.jrn_def_id is '';

create table impacc.import_detail 
(
    id serial primary key,
    import_id bigint references impacc.import(id),
    id_date text,
    id_code_group varchar(10) not null,
    id_nb_row int not null,
    id_pj varchar(20) ,
    id_acc varchar(255),
    id_acc_second varchar(255),
    id_quant varchar(255),
    id_amount_novat varchar(255),
    id_amount_vat varchar(255),
    tva_code varchar(10),
    jr_id bigint references jrn(jr_id)
    
);
comment on table impacc.import_detail is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
comment on column impacc.import_detail.  is '';
begin;

create schema importbank;

create table importbank.version (version int primary key);
insert into importbank.version values(1);

create table importbank.format_bank(
       id serial primary key,
       format_name text not null,
       jrn_def_id int,
       pos_lib int,
       pos_amount int,
       pos_date int,
       pos_operation_nb int,
       sep_decimal char,
       sep_thousand char,
       sep_field char,
       format_date text,
       nb_col int,
	skip int);

alter table importbank.format_bank 
      add constraint fk_jrn foreign key (jrn_def_id) references public.jrn_def(jrn_def_id)  on delete set null on update cascade;

create table importbank.import
       (
       id serial primary key,
       i_date date default now(),
       format_bank_id bigint
       );
alter table importbank.import 
      add constraint fk_format_bank foreign key (format_bank_id) references importbank.format_bank(id)  on delete cascade on update cascade;


create table importbank.temp_bank
       (
       id serial primary key,
       tp_date date not null,
       jrn_def_id int,
       libelle text,
       amount numeric(20,4),
       ref_operation text,
       status char default 'N',
       import_id bigint,
       f_id bigint default NULL 
       );
alter table importbank.temp_bank 
      add constraint fk_jrn_temp_bank foreign key (jrn_def_id) references public.jrn_def(jrn_def_id)  on delete set null on update cascade;

alter table importbank.temp_bank 
      add constraint fk_import_id foreign key (import_id) references importbank.import(id)  on delete cascade on update cascade;

commit;

create schema rapport_advanced;

create table rapport_advanced.formulaire
(f_id serial primary key,
f_title text,
f_description text);
insert into rapport_advanced.formulaire values (1,'cerfa3310-ca3');

create 	table rapport_advanced.type_row(
p_type integer primary key,
p_description text);

insert into rapport_advanced.type_row values (1,'Titre'), (2,'Sous-titre'),(3,'Formule');

create table rapport_advanced.periode_type
(
t_id integer primary key,
t_description text);

insert into rapport_advanced.periode_type values (1,'D''après date'),(2,'N'),(3,'N-1'),(4,'N-2'),(5,'N-3');

create table rapport_advanced.type_row_detail
(tr_id integer primary key,
 tr_description text);

 insert into rapport_advanced.type_row_detail(tr_id,tr_description) values (1,'Formule'),(2,'Poste comptable et code'),(3,'Calcul sur formulaire');


create table  rapport_advanced.formulaire_param
(
	p_id serial primary key,
	p_code	text,
	p_libelle	text,
	p_type integer references rapport_advanced.type_row(p_type) ,
	p_order integer not null,
	f_id integer references rapport_advanced.formulaire(f_id) on update cascade,
	t_id integer references rapport_advanced.periode_type(t_id)
);
create table rapport_advanced.total_type(
	tt_id serial primary key,
	tt_label text);

insert into rapport_advanced.total_type values (0,'Total code TVA+Poste comptable'),(1,'Total code tva'),(2,'Total poste comptable');

create table rapport_advanced.formulaire_param_detail
(
	fp_id serial primary key,
	p_id integer references rapport_advanced.formulaire_param(p_id) on update cascade on delete cascade,
	tmp_val account_type,
	tva_id integer references public.tva_rate(tva_id),
	fp_formula text,
	fp_signed integer,
	type_detail integer,
	jrn_def_type char(3),
	tt_id integer references rapport_advanced.total_type(tt_id)
);
ALTER TABLE rapport_advanced.formulaire_param ADD UNIQUE (f_id, p_code);

ALTER TABLE rapport_advanced.formulaire_param
   ADD COLUMN p_info text;
COMMENT ON COLUMN rapport_advanced.formulaire_param.p_info IS 'si non vide affiche infobulle';

ALTER TABLE rapport_advanced.formulaire_param_detail
  ADD CONSTRAINT fk_formulaire_param_detail_type_row_detail FOREIGN KEY (type_detail)
      REFERENCES rapport_advanced.type_row_detail (tr_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL;

CREATE SEQUENCE rapport_advanced.declaration_param_seq;

CREATE TABLE rapport_advanced.declaration
(
  d_id bigserial NOT NULL,
  d_title text,
  d_start date,
  d_end date,
  to_keep character(1),
  d_generated timestamp with time zone NOT NULL DEFAULT now(),
  f_id bigint,
  CONSTRAINT declaration_pkey PRIMARY KEY (d_id ),
  CONSTRAINT declaration_f_id_fkey FOREIGN KEY (f_id)
      REFERENCES rapport_advanced.formulaire (f_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL
);

CREATE TABLE rapport_advanced.declaration_row
(
  dr_id bigint NOT NULL DEFAULT 0,
  d_id bigint,
  dr_libelle text,
  dr_order bigint,
  dr_code text,
  dr_amount numeric(25,4) NOT NULL,
  CONSTRAINT declaration_row_pkey PRIMARY KEY (dr_id ),
  CONSTRAINT declaration_row_d_id_fkey FOREIGN KEY (d_id)
      REFERENCES rapport_advanced.declaration (d_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
CREATE TABLE rapport_advanced.declaration_row_detail
(
  ddr_id bigserial NOT NULL,
  ddr_amount numeric(25,4) NOT NULL DEFAULT 0,
  dr_id bigint,
  CONSTRAINT declaration_row_detail_pkey PRIMARY KEY (ddr_id ),
  CONSTRAINT declaration_row_detail_dr_id_fkey FOREIGN KEY (dr_id)
      REFERENCES rapport_advanced.declaration_row (dr_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED
);


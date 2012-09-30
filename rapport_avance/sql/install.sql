begin;


create schema rapport_advanced;

create table rapport_advanced.version(version_id integer primary key,version_date timestamp default now(),version_note text);

insert into rapport_advanced.version(version_id,version_note) values  (1,'Installation du plugin');

create table rapport_advanced.formulaire
(f_id serial primary key,
f_title text,
f_description text);


create 	table rapport_advanced.type_row(
p_type integer primary key,
p_description text);

insert into rapport_advanced.type_row values (1,'Titre'), (2,'Titre 2ième Niveau'),(3,'Formule'),(6,'Titre 3ième Niveau');

create table rapport_advanced.periode_type
(
t_id integer primary key,
t_description text);

insert into rapport_advanced.periode_type values (1,'D''après date'),(2,'N'),(3,'N-1'),(4,'N-2'),(5,'N-3');

create table rapport_advanced.type_row_detail
(tr_id integer primary key,
 tr_description text);

 insert into rapport_advanced.type_row_detail(tr_id,tr_description) values (1,'Formule'),(2,'Poste comptable et code'),(3,'Calcul sur formulaire');


CREATE TABLE rapport_advanced.formulaire_param
(
  p_id serial NOT NULL,
  p_code text,
  p_libelle text,
  p_type integer,
  p_order integer NOT NULL,
  f_id integer,
  p_info text, -- si non vide affiche infobulle
  t_id integer,
  CONSTRAINT formulaire_param_pkey PRIMARY KEY (p_id ),
  CONSTRAINT formulaire_param_f_id_fkey FOREIGN KEY (f_id)
      REFERENCES rapport_advanced.formulaire (f_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE cascade,
  CONSTRAINT formulaire_param_p_type_fkey FOREIGN KEY (p_type)
      REFERENCES rapport_advanced.type_row (p_type) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT formulaire_param_t_id_fkey FOREIGN KEY (t_id)
      REFERENCES rapport_advanced.periode_type (t_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT formulaire_param_f_id_p_code_key UNIQUE (f_id , p_code )
);
create table rapport_advanced.total_type(
	tt_id serial primary key,
	tt_label text);

insert into rapport_advanced.total_type values (0,'Total code TVA+Poste comptable'),(1,'Total code tva'),(2,'Total poste comptable');

CREATE TABLE rapport_advanced.formulaire_param_detail
(
  fp_id serial NOT NULL,
  p_id integer,
  tmp_val account_type,
  tva_id integer,
  fp_formula text,
  fp_signed integer,
  jrn_def_type character(3),
  tt_id integer,
  type_detail integer,
  CONSTRAINT formulaire_param_detail_pkey PRIMARY KEY (fp_id ),
  CONSTRAINT fk_formulaire_param_detail_type_row_detail FOREIGN KEY (type_detail)
      REFERENCES rapport_advanced.type_row_detail (tr_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL,
  CONSTRAINT formulaire_param_detail_p_id_fkey FOREIGN KEY (p_id)
      REFERENCES rapport_advanced.formulaire_param (p_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT formulaire_param_detail_tt_id_fkey FOREIGN KEY (tt_id)
      REFERENCES rapport_advanced.total_type (tt_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT formulaire_param_detail_tva_id_fkey FOREIGN KEY (tva_id)
      REFERENCES tva_rate (tva_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);

COMMENT ON COLUMN rapport_advanced.formulaire_param.p_info IS 'si non vide affiche infobulle';

CREATE INDEX fki_formulaire_param_detail_type_row_detail
  ON rapport_advanced.formulaire_param_detail
  USING btree
  (type_detail);

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
  dr_type integer,
  dr_info text,
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

COMMENT ON TABLE rapport_advanced.declaration
  IS 'contains all the declaration';
COMMENT ON COLUMN rapport_advanced.declaration.d_title IS 'Title from formulaire, set when generated';
COMMENT ON COLUMN rapport_advanced.declaration.d_start IS 'start date';
COMMENT ON COLUMN rapport_advanced.declaration.d_end IS 'end date';
COMMENT ON COLUMN rapport_advanced.declaration.to_keep IS 'Y to keep, N not to keep, will be deleted';
COMMENT ON COLUMN rapport_advanced.declaration.d_generated IS 'moment of the computing';
COMMENT ON COLUMN rapport_advanced.declaration.f_id IS 'pk of the formulaire.f_id';
COMMENT ON COLUMN rapport_advanced.declaration_row.dr_libelle IS 'label copied from formulaire_param.p_libelle';
COMMENT ON COLUMN rapport_advanced.declaration_row.dr_order IS 'order copied from formulaire_param.p_order';
COMMENT ON COLUMN rapport_advanced.declaration_row.dr_code IS 'code copied from formulaire_param.p_code';
COMMENT ON COLUMN rapport_advanced.declaration_row.dr_amount IS 'amount computed, normally equal to the sum in declaration_row_detail';
COMMENT ON COLUMN rapport_advanced.declaration_row.dr_type IS 'Type of line (titles or formula)';
COMMENT ON COLUMN rapport_advanced.declaration_row.dr_info IS 'notice copied from formulaire_param.p_info';


insert into rapport_advanced.formulaire values (1,'cerfa3310-ca3');
-- insert into formulaire_param

SELECT pg_catalog.setval('rapport_advanced.formulaire_f_id_seq', 10, true);
SELECT pg_catalog.setval('rapport_advanced.formulaire_param_detail_fp_id_seq', 73, true);


--
-- TOC entry 2520 (class 0 OID 0)
-- Dependencies: 2197
-- Name: formulaire_param_p_id_seq; Type: SEQUENCE SET; Schema: rapport_advanced; Owner: dany
--

SELECT pg_catalog.setval('rapport_advanced.formulaire_param_p_id_seq', 65, true);


--
-- TOC entry 2515 (class 0 OID 18841503)
-- Dependencies: 2198
-- Data for Name: formulaire_param; Type: TABLE DATA; Schema: rapport_advanced; Owner: dany
--

INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (33, '0950_base', 'Opérations imposables à un taux particulier ', 3, 300, 1, 'Décompte effectué sur annexe 3310A', 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (35, '0950_taxe', 'Opérations imposables à un taux particulier', 3, 310, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (36, '0600', 'TVA antérieurement déduite à reverser', 3, 320, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (37, 'C1', 'Total de la TVA brute', 3, 330, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (38, '0035', 'Dont TVA sur acquisition intracommunautaire', 3, 340, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (39, '0038', 'Dont TVA sur opérations à destination de Monaco', 3, 350, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (1, '0979', 'Ventes, Prestations de services', 3, 10, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (3, '0981', 'Autres opérations imposables', 3, 20, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (4, '0044', 'Achats de prestations de services intracommunautaires', 3, 30, 1, 'Article 283-2 du code général de l''impôt', 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (5, '0031', 'Acquisition intracommunautaire', 3, 40, 1, 'Dont vente à distance', 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (6, '0030', 'Livraison d''électricité, de gaz naturel, de chaleur ou de froid imposable en France', 3, 50, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (7, '0040', 'Achat de biens ou de prestations de services réalisé auprès d''un assujetti non établi en France', 3, 60, 1, 'Article 283-1 du Code Général des Impôts', 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (8, '0036', 'Régularisation', 3, 70, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (9, '0032', 'Exportation hors CE', 3, 80, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (10, '0033', 'Autres opérations non imposables', 3, 90, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (11, '0034', 'Livraisons intracommunautaires', 3, 100, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (12, '0029', 'Livraison d''électricité, de gaz naturel, de chaleur, de froid, non imposable en France', 3, 110, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (13, '0037', 'Achats en franchise', 3, 120, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (14, '0043', 'Ventes de biens ou de prestations de services réalisés par un assujetti non établi en France ', 3, 130, 1, 'Article 283-3 du Code Général des Impôts', 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (15, '0039', 'Régularisation', 3, 140, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (16, 'T2', 'Décompte de la TVA à payer ', 1, 150, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (17, 'T3', 'Opérations réalisées en France métropolitaine', 6, 160, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (18, '0206_base', 'Taux normal 19,6 % base', 3, 170, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (19, '0206_taxe', 'Taux normal 19,6% tva', 3, 180, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (20, '0105_base', 'Taux réduit 5,5% base', 3, 190, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (21, '0105_taxe', 'Taux réduit 5,5% tva', 3, 200, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (22, '0150_base', 'Taux réduit 7% base', 3, 210, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (23, '0150_taxe', 'Taux réduit 7% tva', 3, 220, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (25, '0201_base', 'Taux normal 8,5% base', 3, 240, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (26, '0201_taxe', 'Taux normal 8,5% tva', 3, 250, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (27, '0100_base', 'Taux réduit 2,1% base', 3, 260, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (28, '0100_taxe', 'Taux réduit 2,1% tva', 3, 270, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (30, '0900_base', 'Anciens taux base', 3, 290, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (31, '0900_taxe', 'Anciens taux tva', 3, 290, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (40, 'T2A', 'TVA Brute', 2, 155, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (24, 'T4', 'Opérations réalisées dans DOM', 6, 230, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (29, 'T5', 'Opérations imposables à un autre taux ', 6, 280, 1, 'France métropolitaine ou DOM', 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (41, 'T7', 'TVA Déductible', 2, 360, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (42, '0703', 'Biens constituants des immobilisations', 3, 370, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (43, '0702', 'Autres Biens & Services ', 3, 380, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (44, '0059', 'Autre TVA à déduire', 3, 390, 1, 'Dont régularisation sur de la tva collectée', 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (45, '8001', 'Report du crédit apparaissant à la ligne 27 de la précédente déclaration', 3, 400, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (46, 'C2', 'Total TVA déductible', 3, 410, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (47, '0709', 'Dont TVA non perçue récupérable par les assujettis disposant d''un établissement stable dans les DOM', 3, 420, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (48, 'T8', 'Crédit ', 1, 430, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (49, '0705', 'Crédit de TVA', 3, 440, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (50, '8002', 'Remboursement demandé sur formulaire 3519', 3, 450, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (51, '8005', 'Crédit de TVA transfèré à la société tête de groupe sur la déclaration récapitulative 3310-CA3G', 3, 460, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (52, '8003', 'Crédit à reporter', 3, 470, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (53, 'T9', 'Tva à  payer', 1, 480, 1, NULL, 1);
INSERT INTO rapport_advanced.formulaire_param (p_id, p_code, p_libelle, p_type, p_order, f_id, p_info, t_id) VALUES (54, 'C3', 'Tva nette', 3, 490, 1, NULL, 1);
commit;
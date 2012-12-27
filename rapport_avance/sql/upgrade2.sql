begin;
insert into rapport_advanced.type_row_detail values (4,'Poste comptable');
insert into rapport_advanced.type_row_detail  (tr_id,tr_description) values (5,'Opération réconcilée');
insert into rapport_advanced.periode_type (t_id,t_description) values (6,'Depuis début exercice courant N');

ALTER TABLE rapport_advanced.formulaire_param_detail ADD COLUMN with_tmp_val account_type;
ALTER TABLE rapport_advanced.formulaire_param_detail ADD COLUMN type_sum_account bigint;
ALTER TABLE rapport_advanced.formulaire_param_detail ADD COLUMN operation_pcm_val account_type;


CREATE TABLE rapport_advanced.total_type_account
(
  tt_id serial NOT NULL,
  tt_label text,
  CONSTRAINT total_type_account_pkey PRIMARY KEY (tt_id )
);
INSERT INTO rapport_advanced.total_type_account (tt_id, tt_label) VALUES (1, 'Solde (débit-crédit)');
INSERT INTO rapport_advanced.total_type_account (tt_id, tt_label) VALUES (2, 'Solde (crédit-débit)');
INSERT INTO rapport_advanced.total_type_account (tt_id, tt_label) VALUES (3, 'Débit');
INSERT INTO rapport_advanced.total_type_account (tt_id, tt_label) VALUES (4, 'Crédit');



CREATE TABLE rapport_advanced.restore_formulaire_param
(
  p_id integer NOT NULL,
  p_code text,
  p_libelle text,
  p_type integer,
  p_order integer NOT NULL,
  f_id integer,
  p_info text, -- si non vide affiche infobulle
  t_id integer,
  CONSTRAINT restore_formulaire_param_pkey PRIMARY KEY (p_id )

);
CREATE TABLE rapport_advanced.restore_formulaire_param_detail
(
  fp_id integer NOT NULL,
  p_id integer,
  tmp_val account_type,
  tva_id integer,
  fp_formula text,
  fp_signed integer,
  jrn_def_type character(3),
  tt_id integer,
  type_detail integer,
  with_tmp_val account_type,
  type_sum_account bigint,
  operation_pcm_val account_type,
  CONSTRAINT restore_formulaire_param_detail_pkey PRIMARY KEY (fp_id ),
  CONSTRAINT formulaire_param_detail_p_id_fkey FOREIGN KEY (p_id)
      REFERENCES rapport_advanced.formulaire_param (p_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
insert into rapport_advanced.version(version_id,version_note) values (2,'Ajout formules, export CSV des déclarations et des formulaires');

ALTER TABLE rapport_advanced.formulaire_param_detail
  ADD CONSTRAINT formulaire_param_detail_type_sum_account_fkey FOREIGN KEY (type_sum_account)
      REFERENCES rapport_advanced.total_type_account (tt_id) MATCH SIMPLE
      ON UPDATE SET NULL ON DELETE SET NULL;
commit;
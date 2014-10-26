 -- Copyright 2010 De Bontridder Dany <dany@alchimerys.be>
 --   This file is part of NOALYSS.
 --
 --   NOALYSS is free software; you can redistribute it and/or modify
 --   it under the terms of the GNU General Public License as published by
 --   the Free Software Foundation; either version 2 of the License, or
 --   (at your option) any later version.
 --
 --   NOALYSS is distributed in the hope that it will be useful,
 --   but WITHOUT ANY WARRANTY; without even the implied warranty of
 --   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 --   GNU General Public License for more details.
 --
 --   You should have received a copy of the GNU General Public License
 --   along with NOALYSS; if not, write to the Free Software
 --   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
begin;
CREATE TABLE coprop.budget
(
  b_id serial NOT NULL,
  b_name text, -- nom budget
  b_amount numeric(20,4) NOT NULL DEFAULT 0, -- Budget total
  b_exercice bigint,
  b_type character varying(8),
  CONSTRAINT budget_pkey PRIMARY KEY (b_id )
);

COMMENT ON TABLE coprop.budget  IS 'Budget parent';
COMMENT ON COLUMN coprop.budget.b_name IS 'nom budget';
COMMENT ON COLUMN coprop.budget.b_amount IS 'Budget total';
COMMENT ON COLUMN coprop.budget.b_exercice IS 'Budget exercice';
COMMENT ON COLUMN coprop.budget.b_type IS 'Budget type : OPER ou PREV';


CREATE TABLE coprop.clef_repartition
(
  cr_id serial NOT NULL,
  cr_name text NOT NULL,
  cr_note text,
  cr_tantieme bigint NOT NULL DEFAULT 0, -- tantieme totaux
  CONSTRAINT clef_repartition_pkey PRIMARY KEY (cr_id )
);

COMMENT ON COLUMN coprop.clef_repartition.cr_tantieme IS 'tantieme totaux';

CREATE TABLE coprop.budget_detail
(
  bt_id serial NOT NULL,
  bt_label character varying(60) NOT NULL,
  f_id bigint NOT NULL, -- fk fiche.f_id
  b_id bigint, -- fk budget.b_id
  bt_amount numeric(20,4),
  cr_id bigint, -- Fk vers clef_repartition
  CONSTRAINT budget_detail_pkey PRIMARY KEY (bt_id ),
  CONSTRAINT budget_detail_budget_fk FOREIGN KEY (b_id)
      REFERENCES coprop.budget (b_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT budget_detail_clef FOREIGN KEY (cr_id)
      REFERENCES coprop.clef_repartition (cr_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT budget_detail_fiche_fk FOREIGN KEY (f_id)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT bt_amount_ck CHECK (bt_amount > 0::numeric)
);
COMMENT ON TABLE coprop.budget_detail  IS 'Detail budget';
COMMENT ON COLUMN coprop.budget_detail.f_id IS 'fk fiche.f_id';
COMMENT ON COLUMN coprop.budget_detail.b_id IS 'fk budget.b_id';
COMMENT ON COLUMN coprop.budget_detail.cr_id IS 'Fk vers clef_repartition';

CREATE TABLE coprop.clef_repartition_detail
(
  crd_id serial NOT NULL,
  lot_fk bigint,
  crd_amount numeric(20,4) DEFAULT 0,
  cr_id bigint,
  CONSTRAINT clef_repartition_detail_pkey PRIMARY KEY (crd_id ),
  CONSTRAINT clef_repartition_detail_cr_id_fkey FOREIGN KEY (cr_id)
      REFERENCES coprop.clef_repartition (cr_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE TABLE coprop.appel_fond
(
  af_id bigint NOT NULL,
  af_date date NOT NULL, 
  af_confirmed character(1) NOT NULL DEFAULT 'N'::bpchar, 
  af_percent numeric(4,2) NOT NULL DEFAULT 0,
  af_amount numeric(20,4) NOT NULL DEFAULT 0, 
  af_card bigint,
  af_ledger bigint,
  tech_per timestamp with time zone NOT NULL DEFAULT now(),
  jr_internal text, 
  b_id bigint,
  cr_id bigint,
  CONSTRAINT appel_fond_pkey PRIMARY KEY (af_id ),
  CONSTRAINT appel_fond_af_card_fkey FOREIGN KEY (af_card)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT appel_fond_af_ledger_fkey FOREIGN KEY (af_ledger)
      REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT appel_fond_b_id_fkey FOREIGN KEY (b_id)
      REFERENCES coprop.budget (b_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT appel_fond_cr_id_fkey FOREIGN KEY (cr_id)
      REFERENCES coprop.clef_repartition (cr_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT appel_fond_jr_internal_fkey FOREIGN KEY (jr_internal)
      REFERENCES jrn (jr_internal) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

COMMENT ON TABLE coprop.appel_fond  IS 'appel de fond';
COMMENT ON COLUMN coprop.appel_fond.af_date IS 'date de l''appel de fond';
COMMENT ON COLUMN coprop.appel_fond.af_confirmed IS 'confirmé ou temp (Y/N)';
COMMENT ON COLUMN coprop.appel_fond.af_percent IS '% sur le budget';
COMMENT ON COLUMN coprop.appel_fond.af_amount IS 'montant donné ou calculé';
COMMENT ON COLUMN coprop.appel_fond.af_card IS 'fiche appel fond';
COMMENT ON COLUMN coprop.appel_fond.af_ledger IS 'journal pour enregistrer af';
COMMENT ON COLUMN coprop.appel_fond.jr_internal IS 'opération correspondante';
CREATE TABLE coprop.appel_fond_detail
(
  afd_id serial NOT NULL,
  af_id bigint NOT NULL, -- appel de fond
  lot_id bigint NOT NULL,
  key_id bigint NOT NULL,
  afd_amount numeric(20,4),
  key_tantieme numeric(20,4), -- tantième de la clef
  lot_tantieme numeric(20,4), -- tantieme du lot
  CONSTRAINT appel_fond_detail_pkey PRIMARY KEY (afd_id )
);

COMMENT ON TABLE coprop.appel_fond_detail  IS 'detail af';
COMMENT ON COLUMN coprop.appel_fond_detail.af_id IS 'appel de fond';
COMMENT ON COLUMN coprop.appel_fond_detail.key_tantieme IS 'tantième de la clef';
COMMENT ON COLUMN coprop.appel_fond_detail.lot_tantieme IS 'tantieme du lot';


CREATE TABLE coprop.version
(
  v_id bigint NOT NULL,
  v_note text,
  v_date date DEFAULT now(),
  CONSTRAINT version_pkey PRIMARY KEY (v_id )
);

insert into coprop.version (v_id,v_note) values (1,'Installation');

commit;
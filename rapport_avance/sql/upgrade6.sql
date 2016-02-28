begin ;
alter table rapport_advanced.formulaire_param_detail drop constraint formulaire_param_detail_jrn_def_id_fkey;
alter table rapport_advanced.formulaire_param_detail drop constraint formulaire_param_detail_tva_id_fkey;
alter table rapport_advanced.declaration_row alter dr_order type numeric(20,8);
alter table rapport_advanced.declaration_row add dr_account text;
insert into rapport_advanced.type_row values(9,'Liste de postes');
insert into rapport_advanced.type_row_detail values(6,'Liste de postes');

create table rapport_advanced.listing_condition (
	id serial primary key,
	lp_id bigint references rapport_advanced.listing_param(lp_id) on delete cascade on update cascade,
	c_operator integer,
	c_value text);

comment on table rapport_advanced.listing_condition is 'Contains conditions';
comment on column rapport_advanced.listing_condition.lp_id is 'foreign key to listing_param';
comment on column rapport_advanced.listing_condition.c_operator is 'operator = : 0 , >= : 1 , <= 2';
comment on column rapport_advanced.listing_condition.c_value is 'value for comparison';

insert into rapport_advanced.version(version_id,version_note) values (6,'Conditions pour les listing et liste de postes pour les formulaires');
commit;
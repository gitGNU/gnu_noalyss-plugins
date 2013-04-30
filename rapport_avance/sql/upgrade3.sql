ALTER TABLE rapport_advanced.formulaire ADD COLUMN f_lob oid;
COMMENT ON COLUMN rapport_advanced.formulaire.f_lob IS 'OID for file';

ALTER TABLE rapport_advanced.formulaire ADD COLUMN f_filename text;
COMMENT ON COLUMN rapport_advanced.formulaire.f_filename IS 'filename';

ALTER TABLE rapport_advanced.formulaire ADD COLUMN f_mimetype text;
COMMENT ON COLUMN rapport_advanced.formulaire.f_mimetype IS 'Mimetype of the file';

ALTER TABLE rapport_advanced.formulaire ADD COLUMN f_size bigint;
COMMENT ON COLUMN rapport_advanced.formulaire.f_size IS 'Size of the file';

insert into rapport_advanced.type_row(p_type,p_description) values (7,'Texte');
insert into rapport_advanced.type_row(p_type,p_description) values (8,'Remarque');

insert into rapport_advanced.formulaire_param(p_code,p_libelle,p_type,p_order,f_id,t_id)
	select p_code||'note',p_info,8,p_order+1,f_id,t_id from rapport_advanced.formulaire_param where coalesce(p_info,'') <> '';

alter table rapport_advanced.formulaire_param drop p_info;
ALTER TABLE rapport_advanced.declaration_row DROP COLUMN dr_info ;

ALTER TABLE rapport_advanced.declaration ADD COLUMN d_lob oid;
COMMENT ON COLUMN rapport_advanced.declaration.d_lob IS 'OID for file';

ALTER TABLE rapport_advanced.declaration ADD COLUMN d_filename text;
COMMENT ON COLUMN rapport_advanced.declaration.d_filename IS 'filename';

ALTER TABLE rapport_advanced.declaration ADD COLUMN d_mimetype text;
COMMENT ON COLUMN rapport_advanced.declaration.d_mimetype IS 'Mimetype of the file';

ALTER TABLE rapport_advanced.declaration ADD COLUMN d_size bigint;
COMMENT ON COLUMN rapport_advanced.declaration.d_size IS 'Size of the file';

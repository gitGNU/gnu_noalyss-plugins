ALTER TABLE rapport_advanced.formulaire ADD COLUMN f_lob oid;
COMMENT ON COLUMN rapport_advanced.formulaire.f_lob IS 'OID for file';

ALTER TABLE rapport_advanced.formulaire ADD COLUMN f_filename text;
COMMENT ON COLUMN rapport_advanced.formulaire.f_filename IS 'filename';

ALTER TABLE rapport_advanced.formulaire ADD COLUMN f_mimetype text;
COMMENT ON COLUMN rapport_advanced.formulaire.f_mimetype IS 'Mimetype of the file';

ALTER TABLE rapport_advanced.formulaire ADD COLUMN f_size bigint;
COMMENT ON COLUMN rapport_advanced.formulaire.f_size IS 'Size of the file';


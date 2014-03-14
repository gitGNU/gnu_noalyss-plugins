begin;

ALTER TABLE rapport_advanced.formulaire_param_detail   ADD COLUMN jrn_def_id bigint;
COMMENT ON COLUMN rapport_advanced.formulaire_param_detail.jrn_def_id IS 'FK to jrn_def, if null then all the ledgers are concerned';

CREATE FUNCTION rapport_advanced.formulaire_param_detail_jrn_def_id_ins_upd() RETURNS trigger
    AS $$
declare
	jrn_def_id integer;
begin
	if NEW.jrn_def_id = -1 then
		jrn_def_id=NULL;
		NEW.jrn_def_id := jrn_def_id;
	end if;
	return NEW;
end;
$$
language plpgsql;

CREATE INDEX fki_jrn_def_id ON rapport_advanced.formulaire_param_detail USING btree (jrn_def_id);

CREATE TRIGGER formulaire_param_detail_trg BEFORE INSERT OR UPDATE OF jrn_def_id ON rapport_advanced.formulaire_param_detail FOR EACH ROW EXECUTE PROCEDURE rapport_advanced.formulaire_param_detail_jrn_def_id_ins_upd();

ALTER TABLE rapport_advanced.formulaire_param_detail  ADD CONSTRAINT formulaire_param_detail_jrn_def_id_fkey FOREIGN KEY (jrn_def_id)      REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE      ON UPDATE CASCADE ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED;



alter table rapport_advanced.formulaire_param_detail add column date_paid integer default 0;
alter table rapport_advanced.restore_formulaire_param_detail add column date_paid integer default 0;

insert into rapport_advanced.version(version_id,version_note) values (4,'Ajout date et journaux dans les formules');

commit;

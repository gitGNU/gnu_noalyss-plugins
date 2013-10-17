ALTER TABLE rapport_advanced.formulaire_param_detail
   ADD COLUMN jrn_def_id bigint;
COMMENT ON COLUMN rapport_advanced.formulaire_param_detail.jrn_def_id IS 'FK to jrn_def, if null then all the ledgers are concerned';
ALTER TABLE rapport_advanced.formulaire_param_detail
  ADD CONSTRAINT formulaire_param_detail_jrn_def_id_fkey FOREIGN KEY (jrn_def_id)
      REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED;
ALTER TABLE rapport_advanced.formulaire_param_detail
  ADD CONSTRAINT formulaire_param_detail_jrn_def_id_fkey FOREIGN KEY (jrn_def_id)
      REFERENCES jrn_def (jrn_def_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED;
CREATE TRIGGER formulaire_param_detail_trg
  BEFORE INSERT OR UPDATE OF jrn_def_id
  ON rapport_advanced.formulaire_param_detail
  FOR EACH ROW
  EXECUTE PROCEDURE rapport_advanced.formulaire_param_detail_jrn_def_id_ins_upd();



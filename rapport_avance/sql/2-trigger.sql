CREATE OR REPLACE FUNCTION rapport_advanced.listing_compute_trg() 
 returns trigger 
 as 
$_BODY_$
declare 
begin
 NEW.l_timestamp=now() ;
return NEW;
end;
$_BODY_$ LANGUAGE plpgsql;
CREATE TRIGGER listing_compute_trg
 BEFORE 
 INSERT OR UPDATE 
 on rapport_advanced.listing_compute
 FOR EACH ROW EXECUTE PROCEDURE rapport_advanced.listing_compute_trg();

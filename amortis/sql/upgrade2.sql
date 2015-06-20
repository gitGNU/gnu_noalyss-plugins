begin;

CREATE OR REPLACE FUNCTION amortissement.amortissement_up()
  RETURNS trigger AS
$BODY$

declare 
i int;
nyear int;
n_ad_amount numeric(20,2);
total numeric(20,2);
last_ad_id bigint;
n_pct numeric(5,2);
lha_id bigint;
begin
	i :=0;
	if NEW.a_nb_year != OLD.a_nb_year or NEW.a_start != OLD.a_start or NEW.a_amount != OLD.a_amount then
	   delete from amortissement.amortissement_detail where a_id=NEW.a_id;
	   delete from amortissement.amortissement_histo where a_id=NEW.a_id and
	   	       					 (h_year < NEW.a_start or h_year > NEW.a_start+NEW.a_nb_year-1);
	   if NEW.a_nb_year != 0 then
		   n_ad_amount := round(NEW.a_amount/NEW.a_nb_year,2);
		   n_pct := round(n_ad_amount / NEW.a_amount ,2);
		 loop
		   
		   if i = NEW.a_nb_year then
			exit ;
		   end if;
		   nyear :=  NEW.a_start +i;

		   select ha_id into lha_id from amortissement.amortissement_histo where a_id=NEW.a_id and h_year = nyear;

		   if NOT FOUND then 
		      insert into amortissement.amortissement_histo(a_id,h_year,h_amount) values (NEW.a_id,nyear,0);
		   end if;

		   total := round(total + n_ad_amount,2);

		   if total > NEW.a_amount then
			n_ad_amount := NEW.a_amount -  total - n_ad_amount;
		   end if;
		   insert into amortissement.amortissement_detail(ad_year,ad_amount,ad_percentage,a_id) values (nyear,n_ad_amount,1/NEW.a_nb_year,NEW.a_id) returning ad_id into last_ad_id;
		   i := i+1;
		end loop;
		if total < NEW.a_amount then
			n_ad_amount := n_ad_amount+NEW.a_amount-total;
			update amortissement.amortissement_detail set ad_amount=n_ad_amount where ad_id=last_ad_id;
		end if;
	   end if;
	end if;
   return NEW;
end;

$BODY$
  LANGUAGE plpgsql
insert into amortissement.version values (3);
commit;
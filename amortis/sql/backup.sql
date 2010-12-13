--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.5
-- Dumped by pg_dump version 9.0.1
-- Started on 2010-12-13 21:25:44 CET

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 9 (class 2615 OID 5578171)
-- Name: amortissement; Type: SCHEMA; Schema: -; Owner: dany
--

CREATE SCHEMA amortissement;


ALTER SCHEMA amortissement OWNER TO dany;

SET search_path = amortissement, pg_catalog;

--
-- TOC entry 87 (class 1255 OID 6039987)
-- Dependencies: 708 9
-- Name: amortissement_ins(); Type: FUNCTION; Schema: amortissement; Owner: dany
--

CREATE FUNCTION amortissement_ins() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

declare 
i int;
nyear int;
n_ad_amount numeric(20,2);
total numeric(20,2);
last_ad_id bigint;
begin
	i :=0;
	loop
	   
	   if i = NEW.a_nb_year then
		exit ;
	   end if;
           nyear :=  NEW.a_start +i;
           n_ad_amount := NEW.a_amount/NEW.a_nb_year;

           total := total + n_ad_amount;

           if total > NEW.a_amount then
		n_ad_amount := NEW.a_amount -  total - n_ad_amount;
	   end if;

           insert into amortissement.amortissement_detail(ad_year,ad_amount,a_id) values (nyear,n_ad_amount,NEW.a_id) returning ad_id into last_ad_id;
	   i := i+1;
	end loop;
	if total < NEW.a_amount then
		n_ad_amount := n_ad_amount+NEW.a_amount-total;
		update amortissement.amortissement_detail set ad_amount=n_ad_amount where ad_id=last_ad_id;
	end if;
	return NEW;
end;
$$;


ALTER FUNCTION amortissement.amortissement_ins() OWNER TO dany;

--
-- TOC entry 2396 (class 0 OID 0)
-- Dependencies: 87
-- Name: FUNCTION amortissement_ins(); Type: COMMENT; Schema: amortissement; Owner: dany
--

COMMENT ON FUNCTION amortissement_ins() IS 'Fill the table amortissement_detail after an insert';


--
-- TOC entry 89 (class 1255 OID 6040043)
-- Dependencies: 9 708
-- Name: amortissement_up(); Type: FUNCTION; Schema: amortissement; Owner: dany
--

CREATE FUNCTION amortissement_up() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

declare 
i int;
nyear int;
n_ad_amount numeric(20,2);
total numeric(20,2);
last_ad_id bigint;
n_pct numeric(5,2);
begin
	i :=0;
	if NEW.a_nb_year != OLD.a_nb_year or NEW.a_start != OLD.a_start then
	   delete from amortissement.amortissement_detail where a_id=NEW.a_id;

           n_ad_amount := round(NEW.a_amount/NEW.a_nb_year,2);
	   n_pct := round(NEW.a_amount / n_ad_amount,2);
	 loop
	   
	   if i = NEW.a_nb_year then
		exit ;
	   end if;
           nyear :=  NEW.a_start +i;


           total := round(total + n_ad_amount,2);

           if total > NEW.a_amount then
		n_ad_amount := NEW.a_amount -  total - n_ad_amount;
	   end if;
raise notice 'ad_amount % total %s n_pct %',n_ad_amount,total,n_pct;	
           insert into amortissement.amortissement_detail(ad_year,ad_amount,ad_percentage,a_id) values (nyear,n_ad_amount,n_pct,NEW.a_id) returning ad_id into last_ad_id;
	   i := i+1;
	end loop;
raise notice 'Total %',total;
	if total < NEW.a_amount then
		n_ad_amount := n_ad_amount+NEW.a_amount-total;
		update amortissement.amortissement_detail set ad_amount=n_ad_amount where ad_id=last_ad_id;
	end if;
   end if;
   return NEW;
end;
$$;


ALTER FUNCTION amortissement.amortissement_up() OWNER TO dany;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 2080 (class 1259 OID 5578174)
-- Dependencies: 2372 2373 2374 9 373 373
-- Name: amortissement; Type: TABLE; Schema: amortissement; Owner: dany; Tablespace: 
--

CREATE TABLE amortissement (
    a_id integer NOT NULL,
    f_id bigint NOT NULL,
    account_deb public.account_type,
    account_cred public.account_type,
    a_amount numeric(20,2) DEFAULT 0 NOT NULL,
    a_nb_year numeric(4,2) DEFAULT 0 NOT NULL,
    a_start integer,
    a_visible character(1) DEFAULT 'Y'::bpchar
);


ALTER TABLE amortissement.amortissement OWNER TO dany;

--
-- TOC entry 2079 (class 1259 OID 5578172)
-- Dependencies: 2080 9
-- Name: amortissement_a_id_seq; Type: SEQUENCE; Schema: amortissement; Owner: dany
--

CREATE SEQUENCE amortissement_a_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE amortissement.amortissement_a_id_seq OWNER TO dany;

--
-- TOC entry 2397 (class 0 OID 0)
-- Dependencies: 2079
-- Name: amortissement_a_id_seq; Type: SEQUENCE OWNED BY; Schema: amortissement; Owner: dany
--

ALTER SEQUENCE amortissement_a_id_seq OWNED BY amortissement.a_id;


--
-- TOC entry 2082 (class 1259 OID 5578200)
-- Dependencies: 2376 9
-- Name: amortissement_detail; Type: TABLE; Schema: amortissement; Owner: dany; Tablespace: 
--

CREATE TABLE amortissement_detail (
    ad_id integer NOT NULL,
    ad_amount numeric(20,2) DEFAULT 0 NOT NULL,
    a_id bigint,
    ad_year integer,
    ad_percentage numeric(5,2)
);


ALTER TABLE amortissement.amortissement_detail OWNER TO dany;

--
-- TOC entry 2081 (class 1259 OID 5578198)
-- Dependencies: 9 2082
-- Name: amortissement_detail_ad_id_seq; Type: SEQUENCE; Schema: amortissement; Owner: dany
--

CREATE SEQUENCE amortissement_detail_ad_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE amortissement.amortissement_detail_ad_id_seq OWNER TO dany;

--
-- TOC entry 2398 (class 0 OID 0)
-- Dependencies: 2081
-- Name: amortissement_detail_ad_id_seq; Type: SEQUENCE OWNED BY; Schema: amortissement; Owner: dany
--

ALTER SEQUENCE amortissement_detail_ad_id_seq OWNED BY amortissement_detail.ad_id;


--
-- TOC entry 2086 (class 1259 OID 6040047)
-- Dependencies: 9
-- Name: amortissement_histo; Type: TABLE; Schema: amortissement; Owner: dany; Tablespace: 
--

CREATE TABLE amortissement_histo (
    ha_id integer NOT NULL,
    a_id bigint,
    h_amount numeric(20,4) NOT NULL,
    jr_internal text NOT NULL,
    h_year integer NOT NULL
);


ALTER TABLE amortissement.amortissement_histo OWNER TO dany;

--
-- TOC entry 2085 (class 1259 OID 6040045)
-- Dependencies: 2086 9
-- Name: amortissement_histo_ha_id_seq; Type: SEQUENCE; Schema: amortissement; Owner: dany
--

CREATE SEQUENCE amortissement_histo_ha_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE amortissement.amortissement_histo_ha_id_seq OWNER TO dany;

--
-- TOC entry 2399 (class 0 OID 0)
-- Dependencies: 2085
-- Name: amortissement_histo_ha_id_seq; Type: SEQUENCE OWNED BY; Schema: amortissement; Owner: dany
--

ALTER SEQUENCE amortissement_histo_ha_id_seq OWNED BY amortissement_histo.ha_id;


--
-- TOC entry 2371 (class 2604 OID 5578177)
-- Dependencies: 2080 2079 2080
-- Name: a_id; Type: DEFAULT; Schema: amortissement; Owner: dany
--

ALTER TABLE amortissement ALTER COLUMN a_id SET DEFAULT nextval('amortissement_a_id_seq'::regclass);


--
-- TOC entry 2375 (class 2604 OID 5578203)
-- Dependencies: 2082 2081 2082
-- Name: ad_id; Type: DEFAULT; Schema: amortissement; Owner: dany
--

ALTER TABLE amortissement_detail ALTER COLUMN ad_id SET DEFAULT nextval('amortissement_detail_ad_id_seq'::regclass);


--
-- TOC entry 2377 (class 2604 OID 6040050)
-- Dependencies: 2085 2086 2086
-- Name: ha_id; Type: DEFAULT; Schema: amortissement; Owner: dany
--

ALTER TABLE amortissement_histo ALTER COLUMN ha_id SET DEFAULT nextval('amortissement_histo_ha_id_seq'::regclass);


--
-- TOC entry 2383 (class 2606 OID 5578206)
-- Dependencies: 2082 2082
-- Name: amortissement_detail_pkey; Type: CONSTRAINT; Schema: amortissement; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY amortissement_detail
    ADD CONSTRAINT amortissement_detail_pkey PRIMARY KEY (ad_id);


--
-- TOC entry 2379 (class 2606 OID 6040008)
-- Dependencies: 2080 2080
-- Name: amortissement_f_id_key; Type: CONSTRAINT; Schema: amortissement; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_f_id_key UNIQUE (f_id);


--
-- TOC entry 2386 (class 2606 OID 6040055)
-- Dependencies: 2086 2086
-- Name: amortissement_histo_pkey; Type: CONSTRAINT; Schema: amortissement; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY amortissement_histo
    ADD CONSTRAINT amortissement_histo_pkey PRIMARY KEY (ha_id);


--
-- TOC entry 2381 (class 2606 OID 5578182)
-- Dependencies: 2080 2080
-- Name: amortissement_pkey; Type: CONSTRAINT; Schema: amortissement; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_pkey PRIMARY KEY (a_id);


--
-- TOC entry 2384 (class 1259 OID 6039977)
-- Dependencies: 2082
-- Name: fki_amortissement; Type: INDEX; Schema: amortissement; Owner: dany; Tablespace: 
--

CREATE INDEX fki_amortissement ON amortissement_detail USING btree (a_id);


--
-- TOC entry 2392 (class 2620 OID 6040044)
-- Dependencies: 89 2080
-- Name: amortissement; Type: TRIGGER; Schema: amortissement; Owner: dany
--

CREATE TRIGGER amortissement
    BEFORE UPDATE ON amortissement
    FOR EACH ROW
    EXECUTE PROCEDURE amortissement_up();


--
-- TOC entry 2393 (class 2620 OID 6040041)
-- Dependencies: 87 2080
-- Name: amortissement_after_ins; Type: TRIGGER; Schema: amortissement; Owner: dany
--

CREATE TRIGGER amortissement_after_ins
    AFTER INSERT ON amortissement
    FOR EACH ROW
    EXECUTE PROCEDURE amortissement_ins();


--
-- TOC entry 2389 (class 2606 OID 5578193)
-- Dependencies: 2051 2080
-- Name: amortissement_account_cred_fkey; Type: FK CONSTRAINT; Schema: amortissement; Owner: dany
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_account_cred_fkey FOREIGN KEY (account_cred) REFERENCES public.tmp_pcmn(pcm_val);


--
-- TOC entry 2388 (class 2606 OID 5578188)
-- Dependencies: 2080 2051
-- Name: amortissement_account_deb_fkey; Type: FK CONSTRAINT; Schema: amortissement; Owner: dany
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_account_deb_fkey FOREIGN KEY (account_deb) REFERENCES public.tmp_pcmn(pcm_val);


--
-- TOC entry 2390 (class 2606 OID 6039972)
-- Dependencies: 2380 2080 2082
-- Name: amortissement_detail_a_id_fkey; Type: FK CONSTRAINT; Schema: amortissement; Owner: dany
--

ALTER TABLE ONLY amortissement_detail
    ADD CONSTRAINT amortissement_detail_a_id_fkey FOREIGN KEY (a_id) REFERENCES amortissement(a_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2387 (class 2606 OID 5578183)
-- Dependencies: 1922 2080
-- Name: amortissement_f_id_fkey; Type: FK CONSTRAINT; Schema: amortissement; Owner: dany
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_f_id_fkey FOREIGN KEY (f_id) REFERENCES public.fiche(f_id);


--
-- TOC entry 2391 (class 2606 OID 6040056)
-- Dependencies: 2080 2086 2380
-- Name: amortissement_histo_a_id_fkey; Type: FK CONSTRAINT; Schema: amortissement; Owner: dany
--

ALTER TABLE ONLY amortissement_histo
    ADD CONSTRAINT amortissement_histo_a_id_fkey FOREIGN KEY (a_id) REFERENCES amortissement(a_id) ON UPDATE CASCADE ON DELETE CASCADE;


-- Completed on 2010-12-13 21:25:44 CET

--
-- PostgreSQL database dump complete
--


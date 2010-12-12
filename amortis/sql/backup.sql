--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.5
-- Dumped by pg_dump version 9.0.1
-- Started on 2010-12-12 19:25:19 CET

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
-- Dependencies: 703 9
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
-- TOC entry 2384 (class 0 OID 0)
-- Dependencies: 87
-- Name: FUNCTION amortissement_ins(); Type: COMMENT; Schema: amortissement; Owner: dany
--

COMMENT ON FUNCTION amortissement_ins() IS 'Fill the table amortissement_detail after an insert';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 2075 (class 1259 OID 5578174)
-- Dependencies: 2365 2366 2367 372 372 9
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
-- TOC entry 2074 (class 1259 OID 5578172)
-- Dependencies: 2075 9
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
-- TOC entry 2385 (class 0 OID 0)
-- Dependencies: 2074
-- Name: amortissement_a_id_seq; Type: SEQUENCE OWNED BY; Schema: amortissement; Owner: dany
--

ALTER SEQUENCE amortissement_a_id_seq OWNED BY amortissement.a_id;


--
-- TOC entry 2077 (class 1259 OID 5578200)
-- Dependencies: 2369 9
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
-- TOC entry 2076 (class 1259 OID 5578198)
-- Dependencies: 2077 9
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
-- TOC entry 2386 (class 0 OID 0)
-- Dependencies: 2076
-- Name: amortissement_detail_ad_id_seq; Type: SEQUENCE OWNED BY; Schema: amortissement; Owner: dany
--

ALTER SEQUENCE amortissement_detail_ad_id_seq OWNED BY amortissement_detail.ad_id;


--
-- TOC entry 2364 (class 2604 OID 5578177)
-- Dependencies: 2074 2075 2075
-- Name: a_id; Type: DEFAULT; Schema: amortissement; Owner: dany
--

ALTER TABLE amortissement ALTER COLUMN a_id SET DEFAULT nextval('amortissement_a_id_seq'::regclass);


--
-- TOC entry 2368 (class 2604 OID 5578203)
-- Dependencies: 2077 2076 2077
-- Name: ad_id; Type: DEFAULT; Schema: amortissement; Owner: dany
--

ALTER TABLE amortissement_detail ALTER COLUMN ad_id SET DEFAULT nextval('amortissement_detail_ad_id_seq'::regclass);


--
-- TOC entry 2375 (class 2606 OID 5578206)
-- Dependencies: 2077 2077
-- Name: amortissement_detail_pkey; Type: CONSTRAINT; Schema: amortissement; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY amortissement_detail
    ADD CONSTRAINT amortissement_detail_pkey PRIMARY KEY (ad_id);


--
-- TOC entry 2371 (class 2606 OID 6040008)
-- Dependencies: 2075 2075
-- Name: amortissement_f_id_key; Type: CONSTRAINT; Schema: amortissement; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_f_id_key UNIQUE (f_id);


--
-- TOC entry 2373 (class 2606 OID 5578182)
-- Dependencies: 2075 2075
-- Name: amortissement_pkey; Type: CONSTRAINT; Schema: amortissement; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_pkey PRIMARY KEY (a_id);


--
-- TOC entry 2376 (class 1259 OID 6039977)
-- Dependencies: 2077
-- Name: fki_amortissement; Type: INDEX; Schema: amortissement; Owner: dany; Tablespace: 
--

CREATE INDEX fki_amortissement ON amortissement_detail USING btree (a_id);


--
-- TOC entry 2381 (class 2620 OID 6040041)
-- Dependencies: 2075 87
-- Name: amortissement_after_ins; Type: TRIGGER; Schema: amortissement; Owner: dany
--

CREATE TRIGGER amortissement_after_ins
    AFTER INSERT ON amortissement
    FOR EACH ROW
    EXECUTE PROCEDURE amortissement_ins();


--
-- TOC entry 2379 (class 2606 OID 5578193)
-- Dependencies: 2046 2075
-- Name: amortissement_account_cred_fkey; Type: FK CONSTRAINT; Schema: amortissement; Owner: dany
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_account_cred_fkey FOREIGN KEY (account_cred) REFERENCES public.tmp_pcmn(pcm_val);


--
-- TOC entry 2378 (class 2606 OID 5578188)
-- Dependencies: 2075 2046
-- Name: amortissement_account_deb_fkey; Type: FK CONSTRAINT; Schema: amortissement; Owner: dany
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_account_deb_fkey FOREIGN KEY (account_deb) REFERENCES public.tmp_pcmn(pcm_val);


--
-- TOC entry 2380 (class 2606 OID 6039972)
-- Dependencies: 2075 2372 2077
-- Name: amortissement_detail_a_id_fkey; Type: FK CONSTRAINT; Schema: amortissement; Owner: dany
--

ALTER TABLE ONLY amortissement_detail
    ADD CONSTRAINT amortissement_detail_a_id_fkey FOREIGN KEY (a_id) REFERENCES amortissement(a_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2377 (class 2606 OID 5578183)
-- Dependencies: 1917 2075
-- Name: amortissement_f_id_fkey; Type: FK CONSTRAINT; Schema: amortissement; Owner: dany
--

ALTER TABLE ONLY amortissement
    ADD CONSTRAINT amortissement_f_id_fkey FOREIGN KEY (f_id) REFERENCES public.fiche(f_id);


-- Completed on 2010-12-12 19:25:19 CET

--
-- PostgreSQL database dump complete
--


--
-- PostgreSQL database dump
--

-- Dumped from database version 9.0.4
-- Dumped by pg_dump version 9.0.4
-- Started on 2011-12-27 13:17:14 CET

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 8 (class 2615 OID 7283076)
-- Name: coprop; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA coprop;


SET search_path = coprop, pg_catalog;

SET default_with_oids = false;

--
-- TOC entry 2090 (class 1259 OID 7283087)
-- Dependencies: 8
-- Name: clef_repartition; Type: TABLE; Schema: coprop; Owner: -
--

CREATE TABLE clef_repartition (
    cr_id integer NOT NULL,
    cr_name text NOT NULL,
    cr_note text,
    cr_start date,
    cr_end date
);


--
-- TOC entry 2089 (class 1259 OID 7283085)
-- Dependencies: 8 2090
-- Name: clef_repartition_cr_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE clef_repartition_cr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2406 (class 0 OID 0)
-- Dependencies: 2089
-- Name: clef_repartition_cr_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE clef_repartition_cr_id_seq OWNED BY clef_repartition.cr_id;


--
-- TOC entry 2092 (class 1259 OID 7283136)
-- Dependencies: 2384 8
-- Name: clef_repartition_detail; Type: TABLE; Schema: coprop; Owner: -
--

CREATE TABLE clef_repartition_detail (
    crd_id integer NOT NULL,
    lot_fk bigint,
    crd_amount numeric(20,4) DEFAULT 0,
    cr_id bigint
);


--
-- TOC entry 2091 (class 1259 OID 7283134)
-- Dependencies: 8 2092
-- Name: clef_repartition_detail_crd_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE clef_repartition_detail_crd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2407 (class 0 OID 0)
-- Dependencies: 2091
-- Name: clef_repartition_detail_crd_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE clef_repartition_detail_crd_id_seq OWNED BY clef_repartition_detail.crd_id;


--
-- TOC entry 2096 (class 1259 OID 7302202)
-- Dependencies: 8
-- Name: coproprietaire; Type: TABLE; Schema: coprop; Owner: -
--

CREATE TABLE coproprietaire (
    c_id integer NOT NULL,
    c_fiche_id bigint
);


--
-- TOC entry 2408 (class 0 OID 0)
-- Dependencies: 2096
-- Name: TABLE coproprietaire; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON TABLE coproprietaire IS 'Liste des copropriétaires';


--
-- TOC entry 2095 (class 1259 OID 7302200)
-- Dependencies: 8 2096
-- Name: coproprietaire_c_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE coproprietaire_c_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2409 (class 0 OID 0)
-- Dependencies: 2095
-- Name: coproprietaire_c_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE coproprietaire_c_id_seq OWNED BY coproprietaire.c_id;


--
-- TOC entry 2094 (class 1259 OID 7302189)
-- Dependencies: 2386 8
-- Name: lot; Type: TABLE; Schema: coprop; Owner: -
--

CREATE TABLE lot (
    l_id integer NOT NULL,
    l_fiche_id bigint,
    l_part numeric(20,4) DEFAULT 0,
    coprop_fk bigint NOT NULL
);


--
-- TOC entry 2410 (class 0 OID 0)
-- Dependencies: 2094
-- Name: TABLE lot; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON TABLE lot IS 'liste des Lots';


--
-- TOC entry 2093 (class 1259 OID 7302187)
-- Dependencies: 2094 8
-- Name: lot_l_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE lot_l_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2411 (class 0 OID 0)
-- Dependencies: 2093
-- Name: lot_l_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE lot_l_id_seq OWNED BY lot.l_id;


--
-- TOC entry 2088 (class 1259 OID 7283077)
-- Dependencies: 8
-- Name: parameter; Type: TABLE; Schema: coprop; Owner: -
--

CREATE TABLE parameter (
    pr_id text NOT NULL,
    pr_value text
);


--
-- TOC entry 2382 (class 2604 OID 7283090)
-- Dependencies: 2089 2090 2090
-- Name: cr_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE clef_repartition ALTER COLUMN cr_id SET DEFAULT nextval('clef_repartition_cr_id_seq'::regclass);


--
-- TOC entry 2383 (class 2604 OID 7283139)
-- Dependencies: 2091 2092 2092
-- Name: crd_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE clef_repartition_detail ALTER COLUMN crd_id SET DEFAULT nextval('clef_repartition_detail_crd_id_seq'::regclass);


--
-- TOC entry 2387 (class 2604 OID 7302205)
-- Dependencies: 2095 2096 2096
-- Name: c_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE coproprietaire ALTER COLUMN c_id SET DEFAULT nextval('coproprietaire_c_id_seq'::regclass);


--
-- TOC entry 2385 (class 2604 OID 7302192)
-- Dependencies: 2094 2093 2094
-- Name: l_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE lot ALTER COLUMN l_id SET DEFAULT nextval('lot_l_id_seq'::regclass);


--
-- TOC entry 2393 (class 2606 OID 7283142)
-- Dependencies: 2092 2092
-- Name: clef_repartition_detail_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY clef_repartition_detail
    ADD CONSTRAINT clef_repartition_detail_pkey PRIMARY KEY (crd_id);


--
-- TOC entry 2391 (class 2606 OID 7283095)
-- Dependencies: 2090 2090
-- Name: clef_repartition_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY clef_repartition
    ADD CONSTRAINT clef_repartition_pkey PRIMARY KEY (cr_id);


--
-- TOC entry 2389 (class 2606 OID 7283084)
-- Dependencies: 2088 2088
-- Name: copro_parameter_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY parameter
    ADD CONSTRAINT copro_parameter_pkey PRIMARY KEY (pr_id);


--
-- TOC entry 2397 (class 2606 OID 7302219)
-- Dependencies: 2096 2096
-- Name: coproprietaire_c_fiche_id_key; Type: CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY coproprietaire
    ADD CONSTRAINT coproprietaire_c_fiche_id_key UNIQUE (c_fiche_id);


--
-- TOC entry 2399 (class 2606 OID 7302207)
-- Dependencies: 2096 2096
-- Name: coproprietaire_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY coproprietaire
    ADD CONSTRAINT coproprietaire_pkey PRIMARY KEY (c_id);


--
-- TOC entry 2395 (class 2606 OID 7302194)
-- Dependencies: 2094 2094
-- Name: lot_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY lot
    ADD CONSTRAINT lot_pkey PRIMARY KEY (l_id);


--
-- TOC entry 2400 (class 2606 OID 7283148)
-- Dependencies: 2090 2390 2092
-- Name: clef_repartition_detail_cr_id_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY clef_repartition_detail
    ADD CONSTRAINT clef_repartition_detail_cr_id_fkey FOREIGN KEY (cr_id) REFERENCES clef_repartition(cr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2403 (class 2606 OID 7302208)
-- Dependencies: 2096 1950
-- Name: coproprietaire_c_fiche_id_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY coproprietaire
    ADD CONSTRAINT coproprietaire_c_fiche_id_fkey FOREIGN KEY (c_fiche_id) REFERENCES public.fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;


--
-- TOC entry 2402 (class 2606 OID 7302230)
-- Dependencies: 2396 2094 2096
-- Name: lot_coprop_fk_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY lot
    ADD CONSTRAINT lot_coprop_fk_fkey FOREIGN KEY (coprop_fk) REFERENCES coproprietaire(c_fiche_id) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;


--
-- TOC entry 2401 (class 2606 OID 7302195)
-- Dependencies: 1950 2094
-- Name: lot_fiche_fk; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY lot
    ADD CONSTRAINT lot_fiche_fk FOREIGN KEY (l_fiche_id) REFERENCES public.fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;


-- Completed on 2011-12-27 13:17:14 CET

--
-- PostgreSQL database dump complete
--
INSERT INTO parameter (pr_id, pr_value) VALUES ('categorie_lot', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('categorie_coprop', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('journal_appel', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('categorie_appel', '');

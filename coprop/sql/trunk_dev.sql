--
-- PostgreSQL database dump
--

-- Dumped from database version 9.0.4
-- Dumped by pg_dump version 9.0.4
-- Started on 2011-12-11 16:40:35 CET

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 8 (class 2615 OID 7283076)
-- Name: coprop; Type: SCHEMA; Schema: -; Owner: dany
--

CREATE SCHEMA coprop;


ALTER SCHEMA coprop OWNER TO dany;

SET search_path = coprop, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 2087 (class 1259 OID 7283087)
-- Dependencies: 8
-- Name: clef_repartition; Type: TABLE; Schema: coprop; Owner: dany; Tablespace: 
--

CREATE TABLE clef_repartition (
    cr_id integer NOT NULL,
    cr_name text NOT NULL,
    cr_note text,
    cr_start date,
    cr_end date
);


ALTER TABLE coprop.clef_repartition OWNER TO dany;

--
-- TOC entry 2086 (class 1259 OID 7283085)
-- Dependencies: 2087 8
-- Name: clef_repartition_cr_id_seq; Type: SEQUENCE; Schema: coprop; Owner: dany
--

CREATE SEQUENCE clef_repartition_cr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE coprop.clef_repartition_cr_id_seq OWNER TO dany;

--
-- TOC entry 2399 (class 0 OID 0)
-- Dependencies: 2086
-- Name: clef_repartition_cr_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: dany
--

ALTER SEQUENCE clef_repartition_cr_id_seq OWNED BY clef_repartition.cr_id;


--
-- TOC entry 2400 (class 0 OID 0)
-- Dependencies: 2086
-- Name: clef_repartition_cr_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: dany
--

SELECT pg_catalog.setval('clef_repartition_cr_id_seq', 1, false);


--
-- TOC entry 2089 (class 1259 OID 7283136)
-- Dependencies: 2379 8
-- Name: clef_repartition_detail; Type: TABLE; Schema: coprop; Owner: dany; Tablespace: 
--

CREATE TABLE clef_repartition_detail (
    crd_id integer NOT NULL,
    crd_lot bigint,
    crd_amount numeric(20,4) DEFAULT 0,
    cr_id bigint
);


ALTER TABLE coprop.clef_repartition_detail OWNER TO dany;

--
-- TOC entry 2088 (class 1259 OID 7283134)
-- Dependencies: 8 2089
-- Name: clef_repartition_detail_crd_id_seq; Type: SEQUENCE; Schema: coprop; Owner: dany
--

CREATE SEQUENCE clef_repartition_detail_crd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE coprop.clef_repartition_detail_crd_id_seq OWNER TO dany;

--
-- TOC entry 2401 (class 0 OID 0)
-- Dependencies: 2088
-- Name: clef_repartition_detail_crd_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: dany
--

ALTER SEQUENCE clef_repartition_detail_crd_id_seq OWNED BY clef_repartition_detail.crd_id;


--
-- TOC entry 2402 (class 0 OID 0)
-- Dependencies: 2088
-- Name: clef_repartition_detail_crd_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: dany
--

SELECT pg_catalog.setval('clef_repartition_detail_crd_id_seq', 1, false);


--
-- TOC entry 2091 (class 1259 OID 7283155)
-- Dependencies: 8
-- Name: jnt_coprop_lot; Type: TABLE; Schema: coprop; Owner: dany; Tablespace: 
--

CREATE TABLE jnt_coprop_lot (
    jcl_id integer NOT NULL,
    jcl_copro bigint,
    jcl_lot bigint
);


ALTER TABLE coprop.jnt_coprop_lot OWNER TO dany;

--
-- TOC entry 2090 (class 1259 OID 7283153)
-- Dependencies: 8 2091
-- Name: jnt_coprop_lot_jlc_id_seq; Type: SEQUENCE; Schema: coprop; Owner: dany
--

CREATE SEQUENCE jnt_coprop_lot_jlc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE coprop.jnt_coprop_lot_jlc_id_seq OWNER TO dany;

--
-- TOC entry 2403 (class 0 OID 0)
-- Dependencies: 2090
-- Name: jnt_coprop_lot_jlc_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: dany
--

ALTER SEQUENCE jnt_coprop_lot_jlc_id_seq OWNED BY jnt_coprop_lot.jcl_id;


--
-- TOC entry 2404 (class 0 OID 0)
-- Dependencies: 2090
-- Name: jnt_coprop_lot_jlc_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: dany
--

SELECT pg_catalog.setval('jnt_coprop_lot_jlc_id_seq', 1, false);


--
-- TOC entry 2085 (class 1259 OID 7283077)
-- Dependencies: 8
-- Name: parameter; Type: TABLE; Schema: coprop; Owner: dany; Tablespace: 
--

CREATE TABLE parameter (
    pr_id text NOT NULL,
    pr_value text
);


ALTER TABLE coprop.parameter OWNER TO dany;

--
-- TOC entry 2377 (class 2604 OID 7283090)
-- Dependencies: 2086 2087 2087
-- Name: cr_id; Type: DEFAULT; Schema: coprop; Owner: dany
--

ALTER TABLE clef_repartition ALTER COLUMN cr_id SET DEFAULT nextval('clef_repartition_cr_id_seq'::regclass);


--
-- TOC entry 2378 (class 2604 OID 7283139)
-- Dependencies: 2088 2089 2089
-- Name: crd_id; Type: DEFAULT; Schema: coprop; Owner: dany
--

ALTER TABLE clef_repartition_detail ALTER COLUMN crd_id SET DEFAULT nextval('clef_repartition_detail_crd_id_seq'::regclass);


--
-- TOC entry 2380 (class 2604 OID 7283158)
-- Dependencies: 2090 2091 2091
-- Name: jcl_id; Type: DEFAULT; Schema: coprop; Owner: dany
--

ALTER TABLE jnt_coprop_lot ALTER COLUMN jcl_id SET DEFAULT nextval('jnt_coprop_lot_jlc_id_seq'::regclass);


--
-- TOC entry 2394 (class 0 OID 7283087)
-- Dependencies: 2087
-- Data for Name: clef_repartition; Type: TABLE DATA; Schema: coprop; Owner: dany
--

COPY clef_repartition (cr_id, cr_name, cr_note, cr_start, cr_end) FROM stdin;
\.


--
-- TOC entry 2395 (class 0 OID 7283136)
-- Dependencies: 2089
-- Data for Name: clef_repartition_detail; Type: TABLE DATA; Schema: coprop; Owner: dany
--

COPY clef_repartition_detail (crd_id, crd_lot, crd_amount, cr_id) FROM stdin;
\.


--
-- TOC entry 2396 (class 0 OID 7283155)
-- Dependencies: 2091
-- Data for Name: jnt_coprop_lot; Type: TABLE DATA; Schema: coprop; Owner: dany
--

COPY jnt_coprop_lot (jcl_id, jcl_copro, jcl_lot) FROM stdin;
\.


--
-- TOC entry 2393 (class 0 OID 7283077)
-- Dependencies: 2085
-- Data for Name: parameter; Type: TABLE DATA; Schema: coprop; Owner: dany
--

COPY parameter (pr_id, pr_value) FROM stdin;
categorie_lot	1
categorie_coprop	2
poste_appel	740
journal_appel	4
\.


--
-- TOC entry 2386 (class 2606 OID 7283142)
-- Dependencies: 2089 2089
-- Name: clef_repartition_detail_pkey; Type: CONSTRAINT; Schema: coprop; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY clef_repartition_detail
    ADD CONSTRAINT clef_repartition_detail_pkey PRIMARY KEY (crd_id);


--
-- TOC entry 2384 (class 2606 OID 7283095)
-- Dependencies: 2087 2087
-- Name: clef_repartition_pkey; Type: CONSTRAINT; Schema: coprop; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY clef_repartition
    ADD CONSTRAINT clef_repartition_pkey PRIMARY KEY (cr_id);


--
-- TOC entry 2382 (class 2606 OID 7283084)
-- Dependencies: 2085 2085
-- Name: copro_parameter_pkey; Type: CONSTRAINT; Schema: coprop; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY parameter
    ADD CONSTRAINT copro_parameter_pkey PRIMARY KEY (pr_id);


--
-- TOC entry 2388 (class 2606 OID 7283160)
-- Dependencies: 2091 2091
-- Name: jnt_coprop_lot_pkey; Type: CONSTRAINT; Schema: coprop; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY jnt_coprop_lot
    ADD CONSTRAINT jnt_coprop_lot_pkey PRIMARY KEY (jcl_id);


--
-- TOC entry 2390 (class 2606 OID 7283148)
-- Dependencies: 2089 2087 2383
-- Name: clef_repartition_detail_cr_id_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: dany
--

ALTER TABLE ONLY clef_repartition_detail
    ADD CONSTRAINT clef_repartition_detail_cr_id_fkey FOREIGN KEY (cr_id) REFERENCES clef_repartition(cr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2389 (class 2606 OID 7283143)
-- Dependencies: 2089 1947
-- Name: clef_repartition_detail_crl_lot_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: dany
--

ALTER TABLE ONLY clef_repartition_detail
    ADD CONSTRAINT clef_repartition_detail_crl_lot_fkey FOREIGN KEY (crd_lot) REFERENCES public.fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2391 (class 2606 OID 7283161)
-- Dependencies: 1947 2091
-- Name: jnt_coprop_lot_jcl_copro_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: dany
--

ALTER TABLE ONLY jnt_coprop_lot
    ADD CONSTRAINT jnt_coprop_lot_jcl_copro_fkey FOREIGN KEY (jcl_copro) REFERENCES public.fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2392 (class 2606 OID 7283166)
-- Dependencies: 1947 2091
-- Name: jnt_coprop_lot_jcl_lot_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: dany
--

ALTER TABLE ONLY jnt_coprop_lot
    ADD CONSTRAINT jnt_coprop_lot_jcl_lot_fkey FOREIGN KEY (jcl_lot) REFERENCES public.fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


-- Completed on 2011-12-11 16:40:35 CET

--
-- PostgreSQL database dump complete
--


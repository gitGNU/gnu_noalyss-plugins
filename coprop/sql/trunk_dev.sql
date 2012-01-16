--
-- PostgreSQL database dump
--

-- Started on 2012-01-16 10:49:01 CET

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 8 (class 2615 OID 194375)
-- Name: coprop; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA coprop;


SET search_path = coprop, pg_catalog;

--
-- TOC entry 93 (class 1255 OID 195141)
-- Dependencies: 8 712
-- Name: budget_check_date(); Type: FUNCTION; Schema: coprop; Owner: -
--

CREATE FUNCTION budget_check_date() RETURNS trigger
    LANGUAGE plpgsql
    AS $$declare
	n_check int;

begin
	n_check := comptaproc.find_periode(to_char (NEW.b_start,'DD.MM.YYYY'));
	if  n_check = -1 then
		raise exception '% n''est pas une date comprise dans les périodes définies',NEW.b_start;
		return null;
	end if;
	n_check := comptaproc.find_periode(to_char (NEW.b_end,'DD.MM.YYYY'));
	if  n_check = -1 then
		raise exception '% n''est pas une date comprise dans les périodes définies',NEW.b_end;
		return null;
	end if;
	return NEW;

end;
$$;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 2081 (class 1259 OID 195968)
-- Dependencies: 2384 2385 2386 2387 8
-- Name: appel_fond; Type: TABLE; Schema: coprop; Owner: -; Tablespace: 
--

CREATE TABLE appel_fond (
    af_id bigint NOT NULL,
    af_date date NOT NULL,
    af_confirmed character(1) DEFAULT 'N'::bpchar NOT NULL,
    af_percent numeric(4,2) DEFAULT 0 NOT NULL,
    af_amount numeric(20,4) DEFAULT 0 NOT NULL,
    af_card bigint,
    af_ledger bigint,
    tech_per timestamp with time zone DEFAULT now() NOT NULL,
    jr_internal text,
    b_id bigint,
    cr_id bigint
);


--
-- TOC entry 2433 (class 0 OID 0)
-- Dependencies: 2081
-- Name: TABLE appel_fond; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON TABLE appel_fond IS 'appel de fond';


--
-- TOC entry 2434 (class 0 OID 0)
-- Dependencies: 2081
-- Name: COLUMN appel_fond.af_date; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond.af_date IS 'date de l''appel de fond';


--
-- TOC entry 2435 (class 0 OID 0)
-- Dependencies: 2081
-- Name: COLUMN appel_fond.af_confirmed; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond.af_confirmed IS 'confirmé ou temp (Y/N)';


--
-- TOC entry 2436 (class 0 OID 0)
-- Dependencies: 2081
-- Name: COLUMN appel_fond.af_percent; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond.af_percent IS '% sur le budget';


--
-- TOC entry 2437 (class 0 OID 0)
-- Dependencies: 2081
-- Name: COLUMN appel_fond.af_amount; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond.af_amount IS 'montant donné ou calculé';


--
-- TOC entry 2438 (class 0 OID 0)
-- Dependencies: 2081
-- Name: COLUMN appel_fond.af_card; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond.af_card IS 'fiche appel fond';


--
-- TOC entry 2439 (class 0 OID 0)
-- Dependencies: 2081
-- Name: COLUMN appel_fond.af_ledger; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond.af_ledger IS 'journal pour enregistrer af';


--
-- TOC entry 2440 (class 0 OID 0)
-- Dependencies: 2081
-- Name: COLUMN appel_fond.jr_internal; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond.jr_internal IS 'opération correspondante';


--
-- TOC entry 2083 (class 1259 OID 196004)
-- Dependencies: 8
-- Name: appel_fond_detail; Type: TABLE; Schema: coprop; Owner: -; Tablespace: 
--

CREATE TABLE appel_fond_detail (
    afd_id integer NOT NULL,
    af_id bigint NOT NULL,
    lot_id bigint NOT NULL,
    key_id bigint NOT NULL,
    afd_amount numeric(20,4),
    key_tantieme numeric(20,4),
    lot_tantieme numeric(20,4)
);


--
-- TOC entry 2441 (class 0 OID 0)
-- Dependencies: 2083
-- Name: TABLE appel_fond_detail; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON TABLE appel_fond_detail IS 'detail af';


--
-- TOC entry 2442 (class 0 OID 0)
-- Dependencies: 2083
-- Name: COLUMN appel_fond_detail.af_id; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond_detail.af_id IS 'appel de fond';


--
-- TOC entry 2443 (class 0 OID 0)
-- Dependencies: 2083
-- Name: COLUMN appel_fond_detail.key_tantieme; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond_detail.key_tantieme IS 'tantième de la clef';


--
-- TOC entry 2444 (class 0 OID 0)
-- Dependencies: 2083
-- Name: COLUMN appel_fond_detail.lot_tantieme; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN appel_fond_detail.lot_tantieme IS 'tantieme du lot';


--
-- TOC entry 2082 (class 1259 OID 196002)
-- Dependencies: 2083 8
-- Name: appel_fond_detail_afd_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE appel_fond_detail_afd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2445 (class 0 OID 0)
-- Dependencies: 2082
-- Name: appel_fond_detail_afd_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE appel_fond_detail_afd_id_seq OWNED BY appel_fond_detail.afd_id;


--
-- TOC entry 2446 (class 0 OID 0)
-- Dependencies: 2082
-- Name: appel_fond_detail_afd_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: -
--

SELECT pg_catalog.setval('appel_fond_detail_afd_id_seq', 15, true);


--
-- TOC entry 2084 (class 1259 OID 196046)
-- Dependencies: 8
-- Name: appel_fond_id; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE appel_fond_id
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2447 (class 0 OID 0)
-- Dependencies: 2084
-- Name: appel_fond_id; Type: SEQUENCE SET; Schema: coprop; Owner: -
--

SELECT pg_catalog.setval('appel_fond_id', 1, true);


--
-- TOC entry 2077 (class 1259 OID 195110)
-- Dependencies: 2380 2381 8
-- Name: budget; Type: TABLE; Schema: coprop; Owner: -; Tablespace: 
--

CREATE TABLE budget (
    b_id integer NOT NULL,
    b_name text,
    b_start date NOT NULL,
    b_end date NOT NULL,
    b_amount numeric(20,4) DEFAULT 0 NOT NULL,
    CONSTRAINT ck_date CHECK ((b_end > b_start))
);


--
-- TOC entry 2448 (class 0 OID 0)
-- Dependencies: 2077
-- Name: TABLE budget; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON TABLE budget IS 'Budget parent';


--
-- TOC entry 2449 (class 0 OID 0)
-- Dependencies: 2077
-- Name: COLUMN budget.b_name; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN budget.b_name IS 'nom budget';


--
-- TOC entry 2450 (class 0 OID 0)
-- Dependencies: 2077
-- Name: COLUMN budget.b_amount; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN budget.b_amount IS 'Budget total';


--
-- TOC entry 2076 (class 1259 OID 195108)
-- Dependencies: 8 2077
-- Name: budget_b_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE budget_b_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2451 (class 0 OID 0)
-- Dependencies: 2076
-- Name: budget_b_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE budget_b_id_seq OWNED BY budget.b_id;


--
-- TOC entry 2452 (class 0 OID 0)
-- Dependencies: 2076
-- Name: budget_b_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: -
--

SELECT pg_catalog.setval('budget_b_id_seq', 8, true);


--
-- TOC entry 2079 (class 1259 OID 195121)
-- Dependencies: 2383 8
-- Name: budget_detail; Type: TABLE; Schema: coprop; Owner: -; Tablespace: 
--

CREATE TABLE budget_detail (
    bt_id integer NOT NULL,
    bt_label character varying(60) NOT NULL,
    f_id bigint NOT NULL,
    b_id bigint,
    bt_amount numeric(20,4),
    cr_id bigint,
    CONSTRAINT bt_amount_ck CHECK ((bt_amount > (0)::numeric))
);


--
-- TOC entry 2453 (class 0 OID 0)
-- Dependencies: 2079
-- Name: TABLE budget_detail; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON TABLE budget_detail IS 'Detail budget';


--
-- TOC entry 2454 (class 0 OID 0)
-- Dependencies: 2079
-- Name: COLUMN budget_detail.f_id; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN budget_detail.f_id IS 'fk fiche.f_id';


--
-- TOC entry 2455 (class 0 OID 0)
-- Dependencies: 2079
-- Name: COLUMN budget_detail.b_id; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN budget_detail.b_id IS 'fk budget.b_id';


--
-- TOC entry 2456 (class 0 OID 0)
-- Dependencies: 2079
-- Name: COLUMN budget_detail.cr_id; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN budget_detail.cr_id IS 'Fk vers clef_repartition';


--
-- TOC entry 2078 (class 1259 OID 195119)
-- Dependencies: 8 2079
-- Name: budget_detail_bt_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE budget_detail_bt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2457 (class 0 OID 0)
-- Dependencies: 2078
-- Name: budget_detail_bt_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE budget_detail_bt_id_seq OWNED BY budget_detail.bt_id;


--
-- TOC entry 2458 (class 0 OID 0)
-- Dependencies: 2078
-- Name: budget_detail_bt_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: -
--

SELECT pg_catalog.setval('budget_detail_bt_id_seq', 2, true);


--
-- TOC entry 2065 (class 1259 OID 194376)
-- Dependencies: 2373 8
-- Name: clef_repartition; Type: TABLE; Schema: coprop; Owner: -; Tablespace: 
--

CREATE TABLE clef_repartition (
    cr_id integer NOT NULL,
    cr_name text NOT NULL,
    cr_note text,
    cr_tantieme bigint DEFAULT 0 NOT NULL
);


--
-- TOC entry 2459 (class 0 OID 0)
-- Dependencies: 2065
-- Name: COLUMN clef_repartition.cr_tantieme; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON COLUMN clef_repartition.cr_tantieme IS 'tantieme totaux';


--
-- TOC entry 2066 (class 1259 OID 194382)
-- Dependencies: 2065 8
-- Name: clef_repartition_cr_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE clef_repartition_cr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2460 (class 0 OID 0)
-- Dependencies: 2066
-- Name: clef_repartition_cr_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE clef_repartition_cr_id_seq OWNED BY clef_repartition.cr_id;


--
-- TOC entry 2461 (class 0 OID 0)
-- Dependencies: 2066
-- Name: clef_repartition_cr_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: -
--

SELECT pg_catalog.setval('clef_repartition_cr_id_seq', 5, true);


--
-- TOC entry 2067 (class 1259 OID 194384)
-- Dependencies: 2375 8
-- Name: clef_repartition_detail; Type: TABLE; Schema: coprop; Owner: -; Tablespace: 
--

CREATE TABLE clef_repartition_detail (
    crd_id integer NOT NULL,
    lot_fk bigint,
    crd_amount numeric(20,4) DEFAULT 0,
    cr_id bigint
);


--
-- TOC entry 2068 (class 1259 OID 194388)
-- Dependencies: 8 2067
-- Name: clef_repartition_detail_crd_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE clef_repartition_detail_crd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2462 (class 0 OID 0)
-- Dependencies: 2068
-- Name: clef_repartition_detail_crd_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE clef_repartition_detail_crd_id_seq OWNED BY clef_repartition_detail.crd_id;


--
-- TOC entry 2463 (class 0 OID 0)
-- Dependencies: 2068
-- Name: clef_repartition_detail_crd_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: -
--

SELECT pg_catalog.setval('clef_repartition_detail_crd_id_seq', 125, true);


--
-- TOC entry 2069 (class 1259 OID 194390)
-- Dependencies: 8
-- Name: coproprietaire; Type: TABLE; Schema: coprop; Owner: -; Tablespace: 
--

CREATE TABLE coproprietaire (
    c_id integer NOT NULL,
    c_fiche_id bigint
);


--
-- TOC entry 2464 (class 0 OID 0)
-- Dependencies: 2069
-- Name: TABLE coproprietaire; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON TABLE coproprietaire IS 'Liste des copropriétaires';


--
-- TOC entry 2070 (class 1259 OID 194393)
-- Dependencies: 2069 8
-- Name: coproprietaire_c_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE coproprietaire_c_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2465 (class 0 OID 0)
-- Dependencies: 2070
-- Name: coproprietaire_c_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE coproprietaire_c_id_seq OWNED BY coproprietaire.c_id;


--
-- TOC entry 2466 (class 0 OID 0)
-- Dependencies: 2070
-- Name: coproprietaire_c_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: -
--

SELECT pg_catalog.setval('coproprietaire_c_id_seq', 6, true);


--
-- TOC entry 2071 (class 1259 OID 194395)
-- Dependencies: 2378 8
-- Name: lot; Type: TABLE; Schema: coprop; Owner: -; Tablespace: 
--

CREATE TABLE lot (
    l_id integer NOT NULL,
    l_fiche_id bigint,
    l_part numeric(20,4) DEFAULT 0,
    coprop_fk bigint NOT NULL
);


--
-- TOC entry 2467 (class 0 OID 0)
-- Dependencies: 2071
-- Name: TABLE lot; Type: COMMENT; Schema: coprop; Owner: -
--

COMMENT ON TABLE lot IS 'liste des Lots';


--
-- TOC entry 2072 (class 1259 OID 194399)
-- Dependencies: 8 2071
-- Name: lot_l_id_seq; Type: SEQUENCE; Schema: coprop; Owner: -
--

CREATE SEQUENCE lot_l_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2468 (class 0 OID 0)
-- Dependencies: 2072
-- Name: lot_l_id_seq; Type: SEQUENCE OWNED BY; Schema: coprop; Owner: -
--

ALTER SEQUENCE lot_l_id_seq OWNED BY lot.l_id;


--
-- TOC entry 2469 (class 0 OID 0)
-- Dependencies: 2072
-- Name: lot_l_id_seq; Type: SEQUENCE SET; Schema: coprop; Owner: -
--

SELECT pg_catalog.setval('lot_l_id_seq', 18, true);


--
-- TOC entry 2073 (class 1259 OID 194401)
-- Dependencies: 8
-- Name: parameter; Type: TABLE; Schema: coprop; Owner: -; Tablespace: 
--

CREATE TABLE parameter (
    pr_id text NOT NULL,
    pr_value text
);


--
-- TOC entry 2080 (class 1259 OID 195904)
-- Dependencies: 2180 8
-- Name: summary; Type: VIEW; Schema: coprop; Owner: -
--

CREATE VIEW summary AS
    SELECT a.f_id AS lot_id, m.ad_value AS building_id, c.ad_value AS coprop_id FROM (((public.fiche_detail a JOIN public.fiche f1 ON ((f1.f_id = a.f_id))) JOIN (SELECT fd1.f_id, fd1.ad_value FROM public.fiche_detail fd1 WHERE (fd1.ad_id = 70)) m ON ((m.f_id = a.f_id))) JOIN (SELECT fd1.f_id, fd1.ad_value FROM public.fiche_detail fd1 WHERE (fd1.ad_id = 71)) c ON ((c.f_id = a.f_id))) WHERE ((f1.fd_id = 8) AND (a.ad_id = 1));


--
-- TOC entry 2388 (class 2604 OID 196007)
-- Dependencies: 2082 2083 2083
-- Name: afd_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE appel_fond_detail ALTER COLUMN afd_id SET DEFAULT nextval('appel_fond_detail_afd_id_seq'::regclass);


--
-- TOC entry 2379 (class 2604 OID 195113)
-- Dependencies: 2077 2076 2077
-- Name: b_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE budget ALTER COLUMN b_id SET DEFAULT nextval('budget_b_id_seq'::regclass);


--
-- TOC entry 2382 (class 2604 OID 195124)
-- Dependencies: 2078 2079 2079
-- Name: bt_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE budget_detail ALTER COLUMN bt_id SET DEFAULT nextval('budget_detail_bt_id_seq'::regclass);


--
-- TOC entry 2372 (class 2604 OID 194407)
-- Dependencies: 2066 2065
-- Name: cr_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE clef_repartition ALTER COLUMN cr_id SET DEFAULT nextval('clef_repartition_cr_id_seq'::regclass);


--
-- TOC entry 2374 (class 2604 OID 194408)
-- Dependencies: 2068 2067
-- Name: crd_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE clef_repartition_detail ALTER COLUMN crd_id SET DEFAULT nextval('clef_repartition_detail_crd_id_seq'::regclass);


--
-- TOC entry 2376 (class 2604 OID 194409)
-- Dependencies: 2070 2069
-- Name: c_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE coproprietaire ALTER COLUMN c_id SET DEFAULT nextval('coproprietaire_c_id_seq'::regclass);


--
-- TOC entry 2377 (class 2604 OID 194410)
-- Dependencies: 2072 2071
-- Name: l_id; Type: DEFAULT; Schema: coprop; Owner: -
--

ALTER TABLE lot ALTER COLUMN l_id SET DEFAULT nextval('lot_l_id_seq'::regclass);


--
-- TOC entry 2429 (class 0 OID 195968)
-- Dependencies: 2081
-- Data for Name: appel_fond; Type: TABLE DATA; Schema: coprop; Owner: -
--

INSERT INTO appel_fond VALUES (1, '2012-01-16', 'N', 1.00, 400.0000, 124, 5, '2012-01-16 10:45:11.897337+01', NULL, NULL, 1);


--
-- TOC entry 2430 (class 0 OID 196004)
-- Dependencies: 2083
-- Data for Name: appel_fond_detail; Type: TABLE DATA; Schema: coprop; Owner: -
--

INSERT INTO appel_fond_detail VALUES (1, 1, 86, 1, 8.0000, 2339.0000, 47.0000);
INSERT INTO appel_fond_detail VALUES (2, 1, 84, 1, 1.6800, 2339.0000, 10.0000);
INSERT INTO appel_fond_detail VALUES (3, 1, 91, 1, 259.9200, 2339.0000, 1520.0000);
INSERT INTO appel_fond_detail VALUES (4, 1, 89, 1, 7.6800, 2339.0000, 45.0000);
INSERT INTO appel_fond_detail VALUES (5, 1, 100, 1, 1.1600, 2339.0000, 7.0000);
INSERT INTO appel_fond_detail VALUES (6, 1, 88, 1, 0.3200, 2339.0000, 2.0000);
INSERT INTO appel_fond_detail VALUES (7, 1, 94, 1, 1.1600, 2339.0000, 7.0000);
INSERT INTO appel_fond_detail VALUES (8, 1, 90, 1, 99.8400, 2339.0000, 584.0000);
INSERT INTO appel_fond_detail VALUES (9, 1, 93, 1, 0.6800, 2339.0000, 4.0000);
INSERT INTO appel_fond_detail VALUES (10, 1, 85, 1, 5.9600, 2339.0000, 35.0000);
INSERT INTO appel_fond_detail VALUES (11, 1, 127, 1, 0.0000, 2339.0000, 0.0000);
INSERT INTO appel_fond_detail VALUES (12, 1, 126, 1, 0.0000, 2339.0000, 0.0000);
INSERT INTO appel_fond_detail VALUES (13, 1, 87, 1, 11.6000, 2339.0000, 68.0000);
INSERT INTO appel_fond_detail VALUES (14, 1, 92, 1, 0.3200, 2339.0000, 2.0000);
INSERT INTO appel_fond_detail VALUES (15, 1, 101, 1, 1.3600, 2339.0000, 8.0000);


--
-- TOC entry 2427 (class 0 OID 195110)
-- Dependencies: 2077
-- Data for Name: budget; Type: TABLE DATA; Schema: coprop; Owner: -
--

INSERT INTO budget VALUES (8, 'test', '2011-04-01', '2011-05-01', 1200.0000);


--
-- TOC entry 2428 (class 0 OID 195121)
-- Dependencies: 2079
-- Data for Name: budget_detail; Type: TABLE DATA; Schema: coprop; Owner: -
--

INSERT INTO budget_detail VALUES (2, 'Eau', 103, 8, 200.0000, 1);
INSERT INTO budget_detail VALUES (1, 'Electricité', 104, 8, 1000.0000, 1);


--
-- TOC entry 2422 (class 0 OID 194376)
-- Dependencies: 2065
-- Data for Name: clef_repartition; Type: TABLE DATA; Schema: coprop; Owner: -
--

INSERT INTO clef_repartition VALUES (1, 'Charges générales tous bâtiments', '', 2339);
INSERT INTO clef_repartition VALUES (5, 'Charge Eau', 'Clef de répartition pour répartir les charges. 

Pour la Maison Horta', 6000);


--
-- TOC entry 2423 (class 0 OID 194384)
-- Dependencies: 2067
-- Data for Name: clef_repartition_detail; Type: TABLE DATA; Schema: coprop; Owner: -
--

INSERT INTO clef_repartition_detail VALUES (96, 86, 47.0000, 1);
INSERT INTO clef_repartition_detail VALUES (97, 84, 10.0000, 1);
INSERT INTO clef_repartition_detail VALUES (98, 91, 1520.0000, 1);
INSERT INTO clef_repartition_detail VALUES (99, 89, 45.0000, 1);
INSERT INTO clef_repartition_detail VALUES (100, 100, 7.0000, 1);
INSERT INTO clef_repartition_detail VALUES (101, 88, 2.0000, 1);
INSERT INTO clef_repartition_detail VALUES (102, 94, 7.0000, 1);
INSERT INTO clef_repartition_detail VALUES (103, 90, 584.0000, 1);
INSERT INTO clef_repartition_detail VALUES (104, 93, 4.0000, 1);
INSERT INTO clef_repartition_detail VALUES (105, 85, 35.0000, 1);
INSERT INTO clef_repartition_detail VALUES (106, 127, 0.0000, 1);
INSERT INTO clef_repartition_detail VALUES (107, 126, 0.0000, 1);
INSERT INTO clef_repartition_detail VALUES (108, 87, 68.0000, 1);
INSERT INTO clef_repartition_detail VALUES (109, 92, 2.0000, 1);
INSERT INTO clef_repartition_detail VALUES (110, 101, 8.0000, 1);
INSERT INTO clef_repartition_detail VALUES (111, 84, 1000.0000, 5);
INSERT INTO clef_repartition_detail VALUES (112, 85, 20.0000, 5);
INSERT INTO clef_repartition_detail VALUES (113, 86, 100.0000, 5);
INSERT INTO clef_repartition_detail VALUES (114, 87, 20.0000, 5);
INSERT INTO clef_repartition_detail VALUES (115, 88, 1000.0000, 5);
INSERT INTO clef_repartition_detail VALUES (116, 89, 20.0000, 5);
INSERT INTO clef_repartition_detail VALUES (117, 90, 100.0000, 5);
INSERT INTO clef_repartition_detail VALUES (118, 91, 0.0000, 5);
INSERT INTO clef_repartition_detail VALUES (119, 92, 1200.0000, 5);
INSERT INTO clef_repartition_detail VALUES (120, 93, 20.0000, 5);
INSERT INTO clef_repartition_detail VALUES (121, 94, 1151.0000, 5);
INSERT INTO clef_repartition_detail VALUES (122, 100, 0.0000, 5);
INSERT INTO clef_repartition_detail VALUES (123, 101, 1251.0000, 5);
INSERT INTO clef_repartition_detail VALUES (124, 126, 18.0000, 5);
INSERT INTO clef_repartition_detail VALUES (125, 127, 100.0000, 5);


--
-- TOC entry 2424 (class 0 OID 194390)
-- Dependencies: 2069
-- Data for Name: coproprietaire; Type: TABLE DATA; Schema: coprop; Owner: -
--

INSERT INTO coproprietaire VALUES (5, 115);
INSERT INTO coproprietaire VALUES (6, 96);


--
-- TOC entry 2425 (class 0 OID 194395)
-- Dependencies: 2071
-- Data for Name: lot; Type: TABLE DATA; Schema: coprop; Owner: -
--

INSERT INTO lot VALUES (12, 84, NULL, 115);
INSERT INTO lot VALUES (13, 85, NULL, 115);
INSERT INTO lot VALUES (14, 86, NULL, 115);
INSERT INTO lot VALUES (17, 87, 0.0000, 96);
INSERT INTO lot VALUES (18, 88, 0.0000, 96);


--
-- TOC entry 2426 (class 0 OID 194401)
-- Dependencies: 2073
-- Data for Name: parameter; Type: TABLE DATA; Schema: coprop; Owner: -
--

INSERT INTO parameter VALUES ('poste_appel', '701');
INSERT INTO parameter VALUES ('categorie_lot', '8');
INSERT INTO parameter VALUES ('categorie_coprop', '7');
INSERT INTO parameter VALUES ('journal_appel', '5');
INSERT INTO parameter VALUES ('categorie_appel', '16');
INSERT INTO parameter VALUES ('categorie_immeuble', '21');


--
-- TOC entry 2408 (class 2606 OID 196009)
-- Dependencies: 2083 2083
-- Name: appel_fond_detail_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY appel_fond_detail
    ADD CONSTRAINT appel_fond_detail_pkey PRIMARY KEY (afd_id);


--
-- TOC entry 2406 (class 2606 OID 195976)
-- Dependencies: 2081 2081
-- Name: appel_fond_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY appel_fond
    ADD CONSTRAINT appel_fond_pkey PRIMARY KEY (af_id);


--
-- TOC entry 2404 (class 2606 OID 195126)
-- Dependencies: 2079 2079
-- Name: budget_detail_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY budget_detail
    ADD CONSTRAINT budget_detail_pkey PRIMARY KEY (bt_id);


--
-- TOC entry 2402 (class 2606 OID 195118)
-- Dependencies: 2077 2077
-- Name: budget_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY budget
    ADD CONSTRAINT budget_pkey PRIMARY KEY (b_id);


--
-- TOC entry 2392 (class 2606 OID 194412)
-- Dependencies: 2067 2067
-- Name: clef_repartition_detail_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY clef_repartition_detail
    ADD CONSTRAINT clef_repartition_detail_pkey PRIMARY KEY (crd_id);


--
-- TOC entry 2390 (class 2606 OID 194414)
-- Dependencies: 2065 2065
-- Name: clef_repartition_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY clef_repartition
    ADD CONSTRAINT clef_repartition_pkey PRIMARY KEY (cr_id);


--
-- TOC entry 2400 (class 2606 OID 194416)
-- Dependencies: 2073 2073
-- Name: copro_parameter_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY parameter
    ADD CONSTRAINT copro_parameter_pkey PRIMARY KEY (pr_id);


--
-- TOC entry 2394 (class 2606 OID 194418)
-- Dependencies: 2069 2069
-- Name: coproprietaire_c_fiche_id_key; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY coproprietaire
    ADD CONSTRAINT coproprietaire_c_fiche_id_key UNIQUE (c_fiche_id);


--
-- TOC entry 2396 (class 2606 OID 194420)
-- Dependencies: 2069 2069
-- Name: coproprietaire_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY coproprietaire
    ADD CONSTRAINT coproprietaire_pkey PRIMARY KEY (c_id);


--
-- TOC entry 2398 (class 2606 OID 194422)
-- Dependencies: 2071 2071
-- Name: lot_pkey; Type: CONSTRAINT; Schema: coprop; Owner: -; Tablespace: 
--

ALTER TABLE ONLY lot
    ADD CONSTRAINT lot_pkey PRIMARY KEY (l_id);


--
-- TOC entry 2421 (class 2620 OID 195143)
-- Dependencies: 2077 93
-- Name: ck_date_trigger; Type: TRIGGER; Schema: coprop; Owner: -
--

CREATE TRIGGER ck_date_trigger
    BEFORE INSERT OR UPDATE ON budget
    FOR EACH ROW
    EXECUTE PROCEDURE budget_check_date();


--
-- TOC entry 2416 (class 2606 OID 195987)
-- Dependencies: 2081 1927
-- Name: appel_fond_af_card_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY appel_fond
    ADD CONSTRAINT appel_fond_af_card_fkey FOREIGN KEY (af_card) REFERENCES public.fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2417 (class 2606 OID 195992)
-- Dependencies: 1948 2081
-- Name: appel_fond_af_ledger_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY appel_fond
    ADD CONSTRAINT appel_fond_af_ledger_fkey FOREIGN KEY (af_ledger) REFERENCES public.jrn_def(jrn_def_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 2419 (class 2606 OID 196010)
-- Dependencies: 2081 2401 2077
-- Name: appel_fond_b_id_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY appel_fond
    ADD CONSTRAINT appel_fond_b_id_fkey FOREIGN KEY (b_id) REFERENCES budget(b_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2420 (class 2606 OID 196015)
-- Dependencies: 2389 2081 2065
-- Name: appel_fond_cr_id_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY appel_fond
    ADD CONSTRAINT appel_fond_cr_id_fkey FOREIGN KEY (cr_id) REFERENCES clef_repartition(cr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2418 (class 2606 OID 195997)
-- Dependencies: 2081 1946
-- Name: appel_fond_jr_internal_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY appel_fond
    ADD CONSTRAINT appel_fond_jr_internal_fkey FOREIGN KEY (jr_internal) REFERENCES public.jrn(jr_internal);


--
-- TOC entry 2414 (class 2606 OID 195132)
-- Dependencies: 2401 2079 2077
-- Name: budget_detail_budget_fk; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY budget_detail
    ADD CONSTRAINT budget_detail_budget_fk FOREIGN KEY (b_id) REFERENCES budget(b_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2415 (class 2606 OID 195172)
-- Dependencies: 2079 2389 2065
-- Name: budget_detail_clef; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY budget_detail
    ADD CONSTRAINT budget_detail_clef FOREIGN KEY (cr_id) REFERENCES clef_repartition(cr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2413 (class 2606 OID 195127)
-- Dependencies: 1927 2079
-- Name: budget_detail_fiche_fk; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY budget_detail
    ADD CONSTRAINT budget_detail_fiche_fk FOREIGN KEY (f_id) REFERENCES public.fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2409 (class 2606 OID 194423)
-- Dependencies: 2389 2067 2065
-- Name: clef_repartition_detail_cr_id_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY clef_repartition_detail
    ADD CONSTRAINT clef_repartition_detail_cr_id_fkey FOREIGN KEY (cr_id) REFERENCES clef_repartition(cr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2410 (class 2606 OID 194428)
-- Dependencies: 1927 2069
-- Name: coproprietaire_c_fiche_id_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY coproprietaire
    ADD CONSTRAINT coproprietaire_c_fiche_id_fkey FOREIGN KEY (c_fiche_id) REFERENCES public.fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;


--
-- TOC entry 2411 (class 2606 OID 194433)
-- Dependencies: 2393 2069 2071
-- Name: lot_coprop_fk_fkey; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY lot
    ADD CONSTRAINT lot_coprop_fk_fkey FOREIGN KEY (coprop_fk) REFERENCES coproprietaire(c_fiche_id) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;


--
-- TOC entry 2412 (class 2606 OID 194438)
-- Dependencies: 1927 2071
-- Name: lot_fiche_fk; Type: FK CONSTRAINT; Schema: coprop; Owner: -
--

ALTER TABLE ONLY lot
    ADD CONSTRAINT lot_fiche_fk FOREIGN KEY (l_fiche_id) REFERENCES public.fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;


-- Completed on 2012-01-16 10:49:01 CET

--
-- PostgreSQL database dump complete
--


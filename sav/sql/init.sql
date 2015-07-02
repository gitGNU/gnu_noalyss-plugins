--
-- PostgreSQL database dump
--

-- Dumped from database version 9.1.15
-- Dumped by pg_dump version 9.1.15
-- Started on 2015-04-04 12:34:59 CEST

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 14 (class 2615 OID 5482319)
-- Name: service_after_sale; Type: SCHEMA; Schema: -; Owner: dany
--

CREATE SCHEMA service_after_sale;


ALTER SCHEMA service_after_sale OWNER TO dany;

--
-- TOC entry 2969 (class 0 OID 0)
-- Dependencies: 14
-- Name: SCHEMA service_after_sale; Type: COMMENT; Schema: -; Owner: dany
--

COMMENT ON SCHEMA service_after_sale IS 'Contains element for the SAV plugin';


SET search_path = service_after_sale, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 492 (class 1259 OID 5482452)
-- Dependencies: 14
-- Name: sav_workhour; Type: TABLE; Schema: service_after_sale; Owner: dany; Tablespace: 
--

CREATE TABLE sav_workhour (
    id integer NOT NULL,
    total_workhour numeric(20,4),
    repair_card_id integer,
    work_description text,
    f_id_workhour bigint
);


ALTER TABLE service_after_sale.sav_workhour OWNER TO dany;

--
-- TOC entry 2970 (class 0 OID 0)
-- Dependencies: 492
-- Name: TABLE sav_workhour; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON TABLE sav_workhour IS 'Workhours';


--
-- TOC entry 2971 (class 0 OID 0)
-- Dependencies: 492
-- Name: COLUMN sav_workhour.total_workhour; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_workhour.total_workhour IS 'amount of workhour';


--
-- TOC entry 2972 (class 0 OID 0)
-- Dependencies: 492
-- Name: COLUMN sav_workhour.repair_card_id; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_workhour.repair_card_id IS 'FK to sav_repair_card';


--
-- TOC entry 2973 (class 0 OID 0)
-- Dependencies: 492
-- Name: COLUMN sav_workhour.work_description; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_workhour.work_description IS 'Description of the work (optionnal)';


--
-- TOC entry 2974 (class 0 OID 0)
-- Dependencies: 492
-- Name: COLUMN sav_workhour.f_id_workhour; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_workhour.f_id_workhour IS 'Card for workhour';


--
-- TOC entry 491 (class 1259 OID 5482450)
-- Dependencies: 14 492
-- Name: intervention_id_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: dany
--

CREATE SEQUENCE intervention_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE service_after_sale.intervention_id_seq OWNER TO dany;

--
-- TOC entry 2975 (class 0 OID 0)
-- Dependencies: 491
-- Name: intervention_id_seq; Type: SEQUENCE OWNED BY; Schema: service_after_sale; Owner: dany
--

ALTER SEQUENCE intervention_id_seq OWNED BY sav_workhour.id;


--
-- TOC entry 496 (class 1259 OID 5482491)
-- Dependencies: 14
-- Name: sav_parameter; Type: TABLE; Schema: service_after_sale; Owner: dany; Tablespace: 
--

CREATE TABLE sav_parameter (
    code text,
    value text,
    description text,
    id integer NOT NULL
);


ALTER TABLE service_after_sale.sav_parameter OWNER TO dany;

--
-- TOC entry 2976 (class 0 OID 0)
-- Dependencies: 496
-- Name: TABLE sav_parameter; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON TABLE sav_parameter IS 'Parameter of the plugin';


--
-- TOC entry 495 (class 1259 OID 5482489)
-- Dependencies: 496 14
-- Name: parameter_id_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: dany
--

CREATE SEQUENCE parameter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE service_after_sale.parameter_id_seq OWNER TO dany;

--
-- TOC entry 2977 (class 0 OID 0)
-- Dependencies: 495
-- Name: parameter_id_seq; Type: SEQUENCE OWNED BY; Schema: service_after_sale; Owner: dany
--

ALTER SEQUENCE parameter_id_seq OWNED BY sav_parameter.id;


--
-- TOC entry 497 (class 1259 OID 5482508)
-- Dependencies: 14
-- Name: repair_card_number_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: dany
--

CREATE SEQUENCE repair_card_number_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE service_after_sale.repair_card_number_seq OWNER TO dany;

--
-- TOC entry 499 (class 1259 OID 5483324)
-- Dependencies: 2822 14
-- Name: sav_repair_card; Type: TABLE; Schema: service_after_sale; Owner: dany; Tablespace: 
--

CREATE TABLE sav_repair_card (
    id integer NOT NULL,
    f_id_customer integer,
    f_id_personnel_received integer,
    f_id_personnel_done integer,
    date_reception timestamp without time zone,
    date_start timestamp without time zone,
    date_end timestamp without time zone,
    garantie character varying(180),
    description_failure text,
    jr_id integer,
    tech_creation_date timestamp without time zone DEFAULT now(),
    repair_number text,
    card_status character(1),
    f_id_good bigint
);


ALTER TABLE service_after_sale.sav_repair_card OWNER TO dany;

--
-- TOC entry 2978 (class 0 OID 0)
-- Dependencies: 499
-- Name: TABLE sav_repair_card; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON TABLE sav_repair_card IS 'Main table : contains the repair card';


--
-- TOC entry 2979 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.f_id_customer; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.f_id_customer IS 'Customer card';


--
-- TOC entry 2980 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.f_id_personnel_received; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.f_id_personnel_received IS 'Not used : card for crew';


--
-- TOC entry 2981 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.f_id_personnel_done; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.f_id_personnel_done IS 'Not used card for crew';


--
-- TOC entry 2982 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.date_reception; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.date_reception IS 'Reception of the good';


--
-- TOC entry 2983 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.date_start; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.date_start IS 'Start of the work';


--
-- TOC entry 2984 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.date_end; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.date_end IS 'Date end of the repair';


--
-- TOC entry 2985 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.garantie; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.garantie IS 'Warranty number - code';


--
-- TOC entry 2986 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.description_failure; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.description_failure IS 'Description of the issue';


--
-- TOC entry 2987 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.jr_id; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.jr_id IS 'Link to the invoice';


--
-- TOC entry 2988 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.tech_creation_date; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.tech_creation_date IS 'Not used';


--
-- TOC entry 2989 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.repair_number; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.repair_number IS 'Not used
';


--
-- TOC entry 2990 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.card_status; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.card_status IS 'Status is En-cours Draft Closed';


--
-- TOC entry 2991 (class 0 OID 0)
-- Dependencies: 499
-- Name: COLUMN sav_repair_card.f_id_good; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_repair_card.f_id_good IS 'Card of returned good';


--
-- TOC entry 498 (class 1259 OID 5483322)
-- Dependencies: 14 499
-- Name: sav_repair_card_id_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: dany
--

CREATE SEQUENCE sav_repair_card_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE service_after_sale.sav_repair_card_id_seq OWNER TO dany;

--
-- TOC entry 2992 (class 0 OID 0)
-- Dependencies: 498
-- Name: sav_repair_card_id_seq; Type: SEQUENCE OWNED BY; Schema: service_after_sale; Owner: dany
--

ALTER SEQUENCE sav_repair_card_id_seq OWNED BY sav_repair_card.id;


--
-- TOC entry 494 (class 1259 OID 5482468)
-- Dependencies: 14
-- Name: sav_spare_part; Type: TABLE; Schema: service_after_sale; Owner: dany; Tablespace: 
--

CREATE TABLE sav_spare_part (
    id bigint NOT NULL,
    f_id_material integer,
    repair_card_id integer,
    quantity numeric(6,2) NOT NULL
);


ALTER TABLE service_after_sale.sav_spare_part OWNER TO dany;

--
-- TOC entry 2993 (class 0 OID 0)
-- Dependencies: 494
-- Name: TABLE sav_spare_part; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON TABLE sav_spare_part IS 'Spare_part';


--
-- TOC entry 2994 (class 0 OID 0)
-- Dependencies: 494
-- Name: COLUMN sav_spare_part.f_id_material; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_spare_part.f_id_material IS 'FK to Fiche ';


--
-- TOC entry 2995 (class 0 OID 0)
-- Dependencies: 494
-- Name: COLUMN sav_spare_part.repair_card_id; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_spare_part.repair_card_id IS 'FK to sav_repair_card';


--
-- TOC entry 2996 (class 0 OID 0)
-- Dependencies: 494
-- Name: COLUMN sav_spare_part.quantity; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_spare_part.quantity IS 'quantity of spare_part';


--
-- TOC entry 500 (class 1259 OID 5483413)
-- Dependencies: 2823 14
-- Name: sav_version; Type: TABLE; Schema: service_after_sale; Owner: dany; Tablespace: 
--

CREATE TABLE sav_version (
    version_id bigint NOT NULL,
    version_comment text,
    version_date timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE service_after_sale.sav_version OWNER TO dany;

--
-- TOC entry 2997 (class 0 OID 0)
-- Dependencies: 500
-- Name: TABLE sav_version; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON TABLE sav_version IS 'Version of the schema';


--
-- TOC entry 2998 (class 0 OID 0)
-- Dependencies: 500
-- Name: COLUMN sav_version.version_id; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_version.version_id IS 'PK : version id';


--
-- TOC entry 2999 (class 0 OID 0)
-- Dependencies: 500
-- Name: COLUMN sav_version.version_comment; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_version.version_comment IS 'Comment about version';


--
-- TOC entry 3000 (class 0 OID 0)
-- Dependencies: 500
-- Name: COLUMN sav_version.version_date; Type: COMMENT; Schema: service_after_sale; Owner: dany
--

COMMENT ON COLUMN sav_version.version_date IS 'Date of update';


--
-- TOC entry 493 (class 1259 OID 5482466)
-- Dependencies: 494 14
-- Name: spare_part_id_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: dany
--

CREATE SEQUENCE spare_part_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE service_after_sale.spare_part_id_seq OWNER TO dany;

--
-- TOC entry 3001 (class 0 OID 0)
-- Dependencies: 493
-- Name: spare_part_id_seq; Type: SEQUENCE OWNED BY; Schema: service_after_sale; Owner: dany
--

ALTER SEQUENCE spare_part_id_seq OWNED BY sav_spare_part.id;


--
-- TOC entry 2820 (class 2604 OID 5482494)
-- Dependencies: 496 495 496
-- Name: id; Type: DEFAULT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_parameter ALTER COLUMN id SET DEFAULT nextval('parameter_id_seq'::regclass);


--
-- TOC entry 2821 (class 2604 OID 5483327)
-- Dependencies: 498 499 499
-- Name: id; Type: DEFAULT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_repair_card ALTER COLUMN id SET DEFAULT nextval('sav_repair_card_id_seq'::regclass);


--
-- TOC entry 2819 (class 2604 OID 5482471)
-- Dependencies: 494 493 494
-- Name: id; Type: DEFAULT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_spare_part ALTER COLUMN id SET DEFAULT nextval('spare_part_id_seq'::regclass);


--
-- TOC entry 2818 (class 2604 OID 5482455)
-- Dependencies: 491 492 492
-- Name: id; Type: DEFAULT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_workhour ALTER COLUMN id SET DEFAULT nextval('intervention_id_seq'::regclass);


--
-- TOC entry 3002 (class 0 OID 0)
-- Dependencies: 491
-- Name: intervention_id_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: dany
--

SELECT pg_catalog.setval('intervention_id_seq', 19, true);


--
-- TOC entry 3003 (class 0 OID 0)
-- Dependencies: 495
-- Name: parameter_id_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: dany
--

SELECT pg_catalog.setval('parameter_id_seq', 1, true);


--
-- TOC entry 3004 (class 0 OID 0)
-- Dependencies: 497
-- Name: repair_card_number_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: dany
--

SELECT pg_catalog.setval('repair_card_number_seq', 1, false);


--
-- TOC entry 2960 (class 0 OID 5482491)
-- Dependencies: 496 2965
-- Data for Name: sav_parameter; Type: TABLE DATA; Schema: service_after_sale; Owner: dany
--

INSERT INTO sav_parameter (code, value, description, id) VALUES ('good', '1,2', 'matériel retourné', 1);
INSERT INTO sav_parameter (code, value, description, id) VALUES ('spare', '1,2,3', 'Spare part', 2);
INSERT INTO sav_parameter (code, value, description, id) VALUES ('workhour', '10', 'Workhour card id', 3);
INSERT INTO sav_parameter (code, value, description, id) VALUES ('ledger', '2', 'Default ledger of sales', 4);


--
-- TOC entry 2963 (class 0 OID 5483324)
-- Dependencies: 499 2965
-- Data for Name: sav_repair_card; Type: TABLE DATA; Schema: service_after_sale; Owner: dany
--

INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (10, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (11, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (12, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (13, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (14, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (15, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (16, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (17, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (18, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (19, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (20, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (21, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (22, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (23, 279, NULL, NULL, '2015-03-22 00:00:00', NULL, NULL, 'aa', 'aaaa', NULL, NULL, NULL, 'E', 147);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (26, NULL, NULL, NULL, NULL, NULL, NULL, '57', '57', NULL, NULL, NULL, 'E', NULL);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (27, NULL, NULL, NULL, NULL, NULL, NULL, '57', '57', NULL, NULL, NULL, 'E', NULL);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (28, NULL, NULL, NULL, NULL, NULL, NULL, '100', '100', NULL, NULL, NULL, 'E', NULL);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (9, 322, NULL, NULL, '2015-03-21 00:00:00', NULL, NULL, 'test', 'test', NULL, NULL, NULL, 'E', 287);
INSERT INTO sav_repair_card (id, f_id_customer, f_id_personnel_received, f_id_personnel_done, date_reception, date_start, date_end, garantie, description_failure, jr_id, tech_creation_date, repair_number, card_status, f_id_good) VALUES (24, 279, NULL, NULL, '2015-03-22 00:00:00', '2015-01-01 00:00:00', '2016-01-01 00:00:00', '100', '100', NULL, NULL, NULL, 'C', 147);


--
-- TOC entry 3005 (class 0 OID 0)
-- Dependencies: 498
-- Name: sav_repair_card_id_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: dany
--

SELECT pg_catalog.setval('sav_repair_card_id_seq', 28, true);


--
-- TOC entry 2958 (class 0 OID 5482468)
-- Dependencies: 494 2965
-- Data for Name: sav_spare_part; Type: TABLE DATA; Schema: service_after_sale; Owner: dany
--

INSERT INTO sav_spare_part (id, f_id_material, repair_card_id, quantity) VALUES (64, 147, 24, 0.00);
INSERT INTO sav_spare_part (id, f_id_material, repair_card_id, quantity) VALUES (72, 287, 9, 10.00);
INSERT INTO sav_spare_part (id, f_id_material, repair_card_id, quantity) VALUES (73, 287, 24, 10.00);
INSERT INTO sav_spare_part (id, f_id_material, repair_card_id, quantity) VALUES (74, 168, 23, 10.00);


--
-- TOC entry 2964 (class 0 OID 5483413)
-- Dependencies: 500 2965
-- Data for Name: sav_version; Type: TABLE DATA; Schema: service_after_sale; Owner: dany
--



--
-- TOC entry 2956 (class 0 OID 5482452)
-- Dependencies: 492 2965
-- Data for Name: sav_workhour; Type: TABLE DATA; Schema: service_after_sale; Owner: dany
--

INSERT INTO sav_workhour (id, total_workhour, repair_card_id, work_description, f_id_workhour) VALUES (7, 3.0000, 24, 'desc', NULL);
INSERT INTO sav_workhour (id, total_workhour, repair_card_id, work_description, f_id_workhour) VALUES (12, 52.0000, 24, 'es', NULL);
INSERT INTO sav_workhour (id, total_workhour, repair_card_id, work_description, f_id_workhour) VALUES (14, 20.0000, 24, 'Test', NULL);
INSERT INTO sav_workhour (id, total_workhour, repair_card_id, work_description, f_id_workhour) VALUES (15, 20.0000, 24, 'test', NULL);
INSERT INTO sav_workhour (id, total_workhour, repair_card_id, work_description, f_id_workhour) VALUES (18, 232.0000, 24, 'voilà ça marche maintenant !!! ''''''', NULL);
INSERT INTO sav_workhour (id, total_workhour, repair_card_id, work_description, f_id_workhour) VALUES (19, 20.0000, 24, 'test ''', NULL);


--
-- TOC entry 3006 (class 0 OID 0)
-- Dependencies: 493
-- Name: spare_part_id_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: dany
--

SELECT pg_catalog.setval('spare_part_id_seq', 74, true);


--
-- TOC entry 2825 (class 2606 OID 5482457)
-- Dependencies: 492 492 2966
-- Name: intervention_pkey; Type: CONSTRAINT; Schema: service_after_sale; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY sav_workhour
    ADD CONSTRAINT intervention_pkey PRIMARY KEY (id);


--
-- TOC entry 2829 (class 2606 OID 5482499)
-- Dependencies: 496 496 2966
-- Name: parameter_pkey; Type: CONSTRAINT; Schema: service_after_sale; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY sav_parameter
    ADD CONSTRAINT parameter_pkey PRIMARY KEY (id);


--
-- TOC entry 2831 (class 2606 OID 5483333)
-- Dependencies: 499 499 2966
-- Name: repair_card_pkey; Type: CONSTRAINT; Schema: service_after_sale; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_pkey PRIMARY KEY (id);


--
-- TOC entry 2833 (class 2606 OID 5483421)
-- Dependencies: 500 500 2966
-- Name: sav_version_pkey; Type: CONSTRAINT; Schema: service_after_sale; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY sav_version
    ADD CONSTRAINT sav_version_pkey PRIMARY KEY (version_id);


--
-- TOC entry 2827 (class 2606 OID 5482473)
-- Dependencies: 494 494 2966
-- Name: spare_part_pkey; Type: CONSTRAINT; Schema: service_after_sale; Owner: dany; Tablespace: 
--

ALTER TABLE ONLY sav_spare_part
    ADD CONSTRAINT spare_part_pkey PRIMARY KEY (id);


--
-- TOC entry 2839 (class 2606 OID 5483334)
-- Dependencies: 499 232 2966
-- Name: repair_card_f_id_customer_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_f_id_customer_fkey FOREIGN KEY (f_id_customer) REFERENCES public.fiche(f_id);


--
-- TOC entry 2838 (class 2606 OID 5483339)
-- Dependencies: 232 499 2966
-- Name: repair_card_f_id_personnel_done_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_f_id_personnel_done_fkey FOREIGN KEY (f_id_personnel_done) REFERENCES public.fiche(f_id);


--
-- TOC entry 2837 (class 2606 OID 5483344)
-- Dependencies: 232 499 2966
-- Name: repair_card_f_id_personnel_received_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_f_id_personnel_received_fkey FOREIGN KEY (f_id_personnel_received) REFERENCES public.fiche(f_id);


--
-- TOC entry 2836 (class 2606 OID 5483349)
-- Dependencies: 499 251 2966
-- Name: repair_card_jr_id_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_jr_id_fkey FOREIGN KEY (jr_id) REFERENCES public.jrn(jr_id);


--
-- TOC entry 2835 (class 2606 OID 5483354)
-- Dependencies: 494 232 2966
-- Name: sav_spare_part_material_fk; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_spare_part
    ADD CONSTRAINT sav_spare_part_material_fk FOREIGN KEY (f_id_material) REFERENCES public.fiche(f_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 2834 (class 2606 OID 5483359)
-- Dependencies: 499 494 2830 2966
-- Name: sav_spare_part_repair; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: dany
--

ALTER TABLE ONLY sav_spare_part
    ADD CONSTRAINT sav_spare_part_repair FOREIGN KEY (repair_card_id) REFERENCES sav_repair_card(id) ON UPDATE CASCADE ON DELETE CASCADE;


-- Completed on 2015-04-04 12:34:59 CEST

--
-- PostgreSQL database dump complete
--


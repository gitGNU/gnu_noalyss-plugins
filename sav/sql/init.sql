--
-- PostgreSQL database dump
--

-- Dumped from database version 9.1.14
-- Dumped by pg_dump version 9.1.14
-- Started on 2015-02-19 21:20:44 CET

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 14 (class 2615 OID 5482319)
-- Name: service_after_sale; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA service_after_sale;


--
-- TOC entry 2961 (class 0 OID 0)
-- Dependencies: 14
-- Name: SCHEMA service_after_sale; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA service_after_sale IS 'Contains element for the SAV plugin';


SET search_path = service_after_sale, pg_catalog;

SET default_with_oids = false;

--
-- TOC entry 494 (class 1259 OID 5482452)
-- Dependencies: 14
-- Name: sav_workhour; Type: TABLE; Schema: service_after_sale; Owner: -
--

CREATE TABLE sav_workhour (
    id integer NOT NULL,
    total_workhour numeric(5,4),
    repair_card_id integer,
    work_description text
);


--
-- TOC entry 493 (class 1259 OID 5482450)
-- Dependencies: 14 494
-- Name: intervention_id_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: -
--

CREATE SEQUENCE intervention_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2962 (class 0 OID 0)
-- Dependencies: 493
-- Name: intervention_id_seq; Type: SEQUENCE OWNED BY; Schema: service_after_sale; Owner: -
--

ALTER SEQUENCE intervention_id_seq OWNED BY sav_workhour.id;


--
-- TOC entry 498 (class 1259 OID 5482491)
-- Dependencies: 14
-- Name: sav_parameter; Type: TABLE; Schema: service_after_sale; Owner: -
--

CREATE TABLE sav_parameter (
    code text,
    value text,
    description text,
    id integer NOT NULL
);


--
-- TOC entry 497 (class 1259 OID 5482489)
-- Dependencies: 498 14
-- Name: parameter_id_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: -
--

CREATE SEQUENCE parameter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2963 (class 0 OID 0)
-- Dependencies: 497
-- Name: parameter_id_seq; Type: SEQUENCE OWNED BY; Schema: service_after_sale; Owner: -
--

ALTER SEQUENCE parameter_id_seq OWNED BY sav_parameter.id;


--
-- TOC entry 492 (class 1259 OID 5482421)
-- Dependencies: 2814 14
-- Name: sav_repair_card; Type: TABLE; Schema: service_after_sale; Owner: -
--

CREATE TABLE sav_repair_card (
    id integer NOT NULL,
    f_id_customer integer,
    f_id_personnel_received integer,
    f_id_personnel_done integer,
    f_id_good integer,
    date_reception time without time zone,
    date_start time without time zone,
    date_end time without time zone,
    garantie character varying(180),
    description_failure text,
    jr_id integer,
    tech_creation_date timestamp without time zone DEFAULT now(),
    repair_number text,
    card_status character(1)
);


--
-- TOC entry 491 (class 1259 OID 5482419)
-- Dependencies: 492 14
-- Name: repair_card_id_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: -
--

CREATE SEQUENCE repair_card_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2964 (class 0 OID 0)
-- Dependencies: 491
-- Name: repair_card_id_seq; Type: SEQUENCE OWNED BY; Schema: service_after_sale; Owner: -
--

ALTER SEQUENCE repair_card_id_seq OWNED BY sav_repair_card.id;


--
-- TOC entry 499 (class 1259 OID 5482508)
-- Dependencies: 14
-- Name: repair_card_number_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: -
--

CREATE SEQUENCE repair_card_number_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 496 (class 1259 OID 5482468)
-- Dependencies: 14
-- Name: sav_spare_part; Type: TABLE; Schema: service_after_sale; Owner: -
--

CREATE TABLE sav_spare_part (
    id bigint NOT NULL,
    f_id_materiel integer,
    repair_card_id integer
);


--
-- TOC entry 495 (class 1259 OID 5482466)
-- Dependencies: 496 14
-- Name: spare_part_id_seq; Type: SEQUENCE; Schema: service_after_sale; Owner: -
--

CREATE SEQUENCE spare_part_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2965 (class 0 OID 0)
-- Dependencies: 495
-- Name: spare_part_id_seq; Type: SEQUENCE OWNED BY; Schema: service_after_sale; Owner: -
--

ALTER SEQUENCE spare_part_id_seq OWNED BY sav_spare_part.id;


--
-- TOC entry 2817 (class 2604 OID 5482494)
-- Dependencies: 497 498 498
-- Name: id; Type: DEFAULT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_parameter ALTER COLUMN id SET DEFAULT nextval('parameter_id_seq'::regclass);


--
-- TOC entry 2813 (class 2604 OID 5482424)
-- Dependencies: 491 492 492
-- Name: id; Type: DEFAULT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_repair_card ALTER COLUMN id SET DEFAULT nextval('repair_card_id_seq'::regclass);


--
-- TOC entry 2816 (class 2604 OID 5482471)
-- Dependencies: 496 495 496
-- Name: id; Type: DEFAULT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_spare_part ALTER COLUMN id SET DEFAULT nextval('spare_part_id_seq'::regclass);


--
-- TOC entry 2815 (class 2604 OID 5482455)
-- Dependencies: 494 493 494
-- Name: id; Type: DEFAULT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_workhour ALTER COLUMN id SET DEFAULT nextval('intervention_id_seq'::regclass);


--
-- TOC entry 2966 (class 0 OID 0)
-- Dependencies: 493
-- Name: intervention_id_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: -
--

SELECT pg_catalog.setval('intervention_id_seq', 1, false);


--
-- TOC entry 2967 (class 0 OID 0)
-- Dependencies: 497
-- Name: parameter_id_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: -
--

SELECT pg_catalog.setval('parameter_id_seq', 1, false);


--
-- TOC entry 2968 (class 0 OID 0)
-- Dependencies: 491
-- Name: repair_card_id_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: -
--

SELECT pg_catalog.setval('repair_card_id_seq', 1, false);


--
-- TOC entry 2969 (class 0 OID 0)
-- Dependencies: 499
-- Name: repair_card_number_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: -
--

SELECT pg_catalog.setval('repair_card_number_seq', 1, false);


--
-- TOC entry 2955 (class 0 OID 5482491)
-- Dependencies: 498 2957
-- Data for Name: sav_parameter; Type: TABLE DATA; Schema: service_after_sale; Owner: -
--



--
-- TOC entry 2949 (class 0 OID 5482421)
-- Dependencies: 492 2957
-- Data for Name: sav_repair_card; Type: TABLE DATA; Schema: service_after_sale; Owner: -
--



--
-- TOC entry 2953 (class 0 OID 5482468)
-- Dependencies: 496 2957
-- Data for Name: sav_spare_part; Type: TABLE DATA; Schema: service_after_sale; Owner: -
--



--
-- TOC entry 2951 (class 0 OID 5482452)
-- Dependencies: 494 2957
-- Data for Name: sav_workhour; Type: TABLE DATA; Schema: service_after_sale; Owner: -
--



--
-- TOC entry 2970 (class 0 OID 0)
-- Dependencies: 495
-- Name: spare_part_id_seq; Type: SEQUENCE SET; Schema: service_after_sale; Owner: -
--

SELECT pg_catalog.setval('spare_part_id_seq', 1, false);


--
-- TOC entry 2821 (class 2606 OID 5482457)
-- Dependencies: 494 494 2958
-- Name: intervention_pkey; Type: CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_workhour
    ADD CONSTRAINT intervention_pkey PRIMARY KEY (id);


--
-- TOC entry 2825 (class 2606 OID 5482499)
-- Dependencies: 498 498 2958
-- Name: parameter_pkey; Type: CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_parameter
    ADD CONSTRAINT parameter_pkey PRIMARY KEY (id);


--
-- TOC entry 2819 (class 2606 OID 5482429)
-- Dependencies: 492 492 2958
-- Name: repair_card_pkey; Type: CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_pkey PRIMARY KEY (id);


--
-- TOC entry 2823 (class 2606 OID 5482473)
-- Dependencies: 496 496 2958
-- Name: spare_part_pkey; Type: CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_spare_part
    ADD CONSTRAINT spare_part_pkey PRIMARY KEY (id);


--
-- TOC entry 2830 (class 2606 OID 5482458)
-- Dependencies: 2818 492 494 2958
-- Name: intervention_repair_card_id_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_workhour
    ADD CONSTRAINT intervention_repair_card_id_fkey FOREIGN KEY (repair_card_id) REFERENCES sav_repair_card(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2829 (class 2606 OID 5482430)
-- Dependencies: 492 232 2958
-- Name: repair_card_f_id_customer_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_f_id_customer_fkey FOREIGN KEY (f_id_customer) REFERENCES public.fiche(f_id);


--
-- TOC entry 2828 (class 2606 OID 5482435)
-- Dependencies: 232 492 2958
-- Name: repair_card_f_id_personnel_done_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_f_id_personnel_done_fkey FOREIGN KEY (f_id_personnel_done) REFERENCES public.fiche(f_id);


--
-- TOC entry 2827 (class 2606 OID 5482440)
-- Dependencies: 492 232 2958
-- Name: repair_card_f_id_personnel_received_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_f_id_personnel_received_fkey FOREIGN KEY (f_id_personnel_received) REFERENCES public.fiche(f_id);


--
-- TOC entry 2826 (class 2606 OID 5482445)
-- Dependencies: 492 251 2958
-- Name: repair_card_jr_id_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_repair_card
    ADD CONSTRAINT repair_card_jr_id_fkey FOREIGN KEY (jr_id) REFERENCES public.jrn(jr_id);


--
-- TOC entry 2832 (class 2606 OID 5482474)
-- Dependencies: 232 496 2958
-- Name: spare_part_id_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_spare_part
    ADD CONSTRAINT spare_part_id_fkey FOREIGN KEY (id) REFERENCES public.fiche(f_id);


--
-- TOC entry 2831 (class 2606 OID 5482484)
-- Dependencies: 492 496 2818 2958
-- Name: spare_part_repair_card_id_fkey; Type: FK CONSTRAINT; Schema: service_after_sale; Owner: -
--

ALTER TABLE ONLY sav_spare_part
    ADD CONSTRAINT spare_part_repair_card_id_fkey FOREIGN KEY (repair_card_id) REFERENCES sav_repair_card(id) ON UPDATE CASCADE ON DELETE CASCADE;


-- Completed on 2015-02-19 21:20:44 CET

--
-- PostgreSQL database dump complete
--


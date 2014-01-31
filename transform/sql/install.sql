create schema transform;

create table transform.request(
	r_id bigserial primary key,
        r_date timestamp default now()
);
create table transform.version(
	v_id bigint primary key,
	v_note text);
create table transform.intervat_representative	
(
	rp_id bigserial primary key,
	r_id bigint references transform.request(r_id) on update cascade on delete cascade,
	rp_listing_id text,
	rp_issued text,
	rp_type text
	rp_name text,
	rp_street text,
	rp_postcode text,
	rp_city text,
	rp_email text,
	rp_phone text,
	rp_countrycode char(2) not null

);
create table transform.intervat_declarant
(
	d_id bigserial primary key,
	r_id bigint references transform.request(r_id) on update cascade on delete cascade,
	d_name text,
	d_street text,
	d_postcode text,
	d_city text,
	d_email text,
	d_phone text,
	d_vat_number text,
	d_countrycode char(2) not null,
	d_periode text
);
create table transform.intervat_client
(
	c_id bigserial primary key,
	d_id bigint references transform.intervat_declarant on update cascade on delete cascade,
	c_name text,
	c_vatnumber text,
	c_amount_vat text,
	c_amount_novat text,
	c_issuedby char(2) default 'BE'
);
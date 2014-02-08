begin;
create schema transform;
create table transform.request(
	r_id bigserial primary key,
        r_date date default now(),
        r_type text,
        r_start_date date ,
        r_end_date date 
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
	rp_type text,
	rp_name text,
	rp_street text,
	rp_postcode text,
	rp_city text,
	rp_email text,
	rp_phone text,
	rp_countrycode char(2) default null

);
create table transform.intervat_declarant
(
	d_id bigserial primary key,
	r_id bigint references transform.request(r_id) on update cascade on delete cascade,
	d_name text not null,
	d_street text not null,
	d_postcode text not null,
	d_city text not null,
	d_email text not null,
	d_phone text not null,
	d_vat_number text not null,
	d_countrycode char(2) not null,
	d_periode text not null
);
create table transform.intervat_client
(
	c_id bigserial primary key,
	d_id bigint references transform.intervat_declarant on update cascade on delete cascade,
	c_name text,
        c_comment text,
	c_vatnumber text,
	c_amount_vat text default '0',
	c_amount_novat text default '0',
	c_issuedby char(2) default 'BE'
);

insert into transform.version values(1,'Installation plugin');
commit;
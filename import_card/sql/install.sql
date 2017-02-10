begin;
create schema importcard;

CREATE TABLE importcard.file_csv
(
  id bigserial NOT NULL, -- Primary Key
  file_name text NOT NULL, -- name of the uploaded file
  file_timestamp timestamp with time zone NOT NULL DEFAULT now(), -- Timestamp of the upload . ...
  CONSTRAINT file_csv_pkey PRIMARY KEY (id)
);

COMMENT ON TABLE importcard.file_csv  IS 'Name of the uploaded file , use to avoid to reload several time the same file';
COMMENT ON COLUMN importcard.file_csv.id IS 'Primary Key';
COMMENT ON COLUMN importcard.file_csv.file_name IS 'name of the uploaded file';
COMMENT ON COLUMN importcard.file_csv.file_timestamp IS 'Timestamp of the upload . Permit to clean the old files';

CREATE TABLE importcard.format
(
  id bigserial NOT NULL,
  f_name text NOT NULL,
  f_card_category bigint,
  f_delimiter character(1) NOT NULL,
  f_surround character(1),
  f_unicode_encoding character(1) DEFAULT 'Y'::bpchar,
  f_position text NOT NULL,
  f_timestamp timestamp with time zone NOT NULL DEFAULT now(),
  f_skiprow integer NOT NULL DEFAULT 0, -- Numbre of rows to skip
  f_saved integer, -- Flag for saved...
  CONSTRAINT format_pkey PRIMARY KEY (id)
);
COMMENT ON COLUMN importcard.format.f_skiprow IS 'Numbre of rows to skip';
COMMENT ON COLUMN importcard.format.f_saved IS 'Flag for saved 1 for yes 0 for a temporary template';

create table importcard.version (id bigint primary key,message text , date_apply timestamp default now());

insert into importcard.version (id,message) values (1,'Installation');

commit;

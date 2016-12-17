create schema importcard;

create table importcard.format
(
id bigserial primary key,
f_name text not null,
f_card_category bigint,
f_skiprow char default 'N',
f_delimiter char not null,
f_surround char ,
f_unicode_encoding char default 'Y',
f_position text not null
);

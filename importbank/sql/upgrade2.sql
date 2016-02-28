begin;
alter table importbank.import drop constraint fk_format_bank;
insert into importbank.version values (2);
commit;
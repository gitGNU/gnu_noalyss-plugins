begin;

create table coprop.version
(
    v_id  bigint,
    v_note text,
    v_date date default now()
);

-- alter table budget
alter table coprop.budget drop column b_end;
alter table coprop.budget drop column b_start;
alter table coprop.budget add column b_exercice bigint;
alter table coprop.budget add column varchar(8) b_type;



commit;




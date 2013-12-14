create table rapport_advanced.rapav_parameter
(
    param_code text primary key,
    param_value text 
);
insert into rapport_advanced.rapav_parameter values ('FROM','phpcompta@localhost'), ('SMTP','localhost'),('PORT','25');
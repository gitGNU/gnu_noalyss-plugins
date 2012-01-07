create view coprop.summary as select a.f_id,
(select ad_value from fiche_detail as fd1 where fd1.f_id=a.f_id and fd1.ad_id=1) as lot_name,
(select ad_value from fiche_detail as fd1 where fd1.f_id=a.f_id and fd1.ad_id=23) as lot_qcode,
m.ad_value as building_id,
(select ad_value from fiche_detail as fd1 where fd1.f_id=m.ad_value::numeric and fd1.ad_id=1) as immeuble,
c.ad_value as coprop_id,
(select ad_value from fiche_detail as fd1 where fd1.f_id=c.ad_value::numeric and fd1.ad_id=1) as coprop

  from fiche_detail as a
   join fiche as f1 on (f1.f_id=a.f_id)
   join (select f_id,ad_value from fiche_detail as fd1 where ad_id=9003) as m on (m.f_id=a.f_id)
join (select f_id,ad_value from fiche_detail as fd1 where ad_id=9002) as c on (c.f_id=a.f_id)

  where fd_id=8 and ad_id=1;

CREATE OR REPLACE VIEW coprop.summary AS
 SELECT a.f_id AS lot_id, m.ad_value AS building_id, c.ad_value AS coprop_id
   FROM fiche_detail a
   JOIN fiche f1 ON f1.f_id = a.f_id
   JOIN ( SELECT fd1.f_id, fd1.ad_value
      FROM fiche_detail fd1
     WHERE fd1.ad_id = 70) m ON m.f_id = a.f_id
   JOIN ( SELECT fd1.f_id, fd1.ad_value
   FROM fiche_detail fd1
  WHERE fd1.ad_id = 71) c ON c.f_id = a.f_id
  WHERE f1.fd_id = 8 AND a.ad_id = 1;
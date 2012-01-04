<?php
global $cn;
$cn->exec_sql("delete from copro.clef_repartition where cr_id=$1",array($key_id));
?>

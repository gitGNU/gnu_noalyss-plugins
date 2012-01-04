<?php
global $cn;
$cn->exec_sql("delete from coprop.clef_repartition where cr_id=$1",array($key_id));
?>

<?php
/**
 * @file montre détail budget pour mise à jour
 */
require_once 'class_budget.php';

$bud=new Budget();
$bud->b_id=$bud_id;

$bud->detail();
?>

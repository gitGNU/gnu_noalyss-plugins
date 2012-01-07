<?php

/**
 * @file gestion des budgets
 */

require_once 'class_budget.php';
$bud=new Budget();

if ( isset ($_POST['budget_update']))
{
	$bud->save();
}




$bud->to_list();
<?php

require_once('coprop-constant.php');
require_once('class_database.php');

extract($_GET);
global $cn;
$html = '';
$extra = '';
$ctl = '';
switch ($act)
{
	case 'addkey':
		require_once 'include/ajax_add_key.php';
		break;
	case 'modkey':
		require_once 'include/ajax_mod_key.php';
		break;
	case 'removekey':
		require_once 'include/ajax_remove_key.php';
		break;
	case 'buddisplay':
		require_once 'include/ajax_bud_display.php';
		break;
	case 'budadd':
		$bud_id=0;
		require_once 'include/ajax_bud_display.php';
		break;
	case 'removebudget':
		$cn->exec_sql("delete from coprop.budget where b_id=$1",array($bud_id));
		break;
	default:

		var_dump($_GET);
}
?>

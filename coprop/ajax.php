<?php
require_once('coprop-constant.php');
require_once('class_database.php');

extract($_GET);
global $cn;
$html='';$extra='';$ctl='';
switch ($act)
{
	// ajout un lien copro + lot
	case 'modcopro':
		/* the hide button */
		require_once('include/ajax_mod_copro_lot.php');
		break;
	case 'removelot':
		$cn->exec_sql("delete from coprop.lot where l_id=$1",array($lot_id));
		break;
	default:

		var_dump($_GET);
}
?>

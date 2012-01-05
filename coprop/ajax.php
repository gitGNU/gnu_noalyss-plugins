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
	case 'addkey':
		require_once 'include/ajax_add_key.php';
		break;
	case 'modkey':
		require_once 'include/ajax_mod_key.php';
		break;
        case 'removekey':
            require_once 'include/ajax_remove_key.php';
        case 'buddisplay':
            require_once 'include/ajax_bud_display.php';
	default:

		var_dump($_GET);
}
?>

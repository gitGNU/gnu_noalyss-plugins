<?php
/*
 * Copyright 2010 De Bontridder Dany <dany@alchimerys.be>
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>
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

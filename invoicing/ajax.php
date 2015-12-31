<?php

/*
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
require_once NOALYSS_INCLUDE.'/lib/class_database.php';

extract($_GET);
global $cn;

switch ($act)
{
    case 'inv_get_message':
        /* the hide button */
        require_once('include/ajax_message.php');
        break;
    case 'inv_select_message':
        require_once 'include/ajax_message.php';
        break;
    case 'inv_display_message':
        require_once 'include/ajax_message.php';
        break;
    case 'inv_delete_message':
        require_once 'include/ajax_message.php';
        break;
}
?>

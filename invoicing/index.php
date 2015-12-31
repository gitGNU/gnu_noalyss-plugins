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
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/*!\file
 * \brief main file for tva
 */

/*
 * load javascript
 */

ob_start();
require_once('invoicing_javascript.js');
$j=ob_get_contents();
ob_end_clean();
echo create_script($j);
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(6913);
require_once NOALYSS_INCLUDE.'/class/class_acc_ledger_sold.php';
require_once NOALYSS_INCLUDE.'/lib/class_zip_extended.php';
require_once NOALYSS_INCLUDE.'/lib/class_sendmail.php';
require_once NOALYSS_INCLUDE.'/lib/class_filetosend.php';
/*
 * Check that schema is created
 */
if ( $cn->exist_schema ('invoicing') == false) {
    require_once 'include/class_install_plugin.php';
    $plugin_install=new Install_Plugin($cn);
    $plugin_install->create_schema();
}
require_once 'include/invoicing.inc.php';

?>

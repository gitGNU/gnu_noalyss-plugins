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
/**
 *@file
 *Contains all the needed variable for the plugin
 *is name is plugin_name_constant.php
 * You can use some globale variable, especially for the database
 *  connection
 */

require_once NOALYSS_INCLUDE.'/lib/class_database.php';
require_once 'include/class_copro_parameter.php';
require_once NOALYSS_INCLUDE.'/class/class_acc_ledger.php';

global $cn,$g_copro_parameter,$gDossier;
$cn=Dossier::connect();
$gDossier=Dossier::id();
$g_copro_parameter=new Copro_Parameter();
define ("COPRO_MAX_LOT",19);
define("MAXROWBUD",15);
define ('COPROP_VERSION',1);
?>
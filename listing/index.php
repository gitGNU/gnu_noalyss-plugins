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

/**
 * \file
 * \brief main file for tools
 * 
 */

global $version_plugin;
$version_plugin = SVNINFO;
Extension::check_version(6914);
require_once NOALYSS_INCLUDE.'/database/class_noalyss_sql.php';
require_once 'listing_constant.php';
require_once NOALYSS_INCLUDE.'/lib/class_impress.php';
if ($cn->exist_schema('rapport_advanced') == false )
{
	echo_warning(_("Installez d'abord l'extension Rapport Avancé et accèdez-y"));
        return;
}
if ( $cn->get_value('select max(version_id) from rapport_advanced.version') < $rapav_version )
{
	require_once('include/class_rapav_install.php');
	$iplugn = new Rapav_Install($cn);
	$iplugn->upgrade($rapav_version);
}
/*
 * load javascript
 */
ob_start();
require_once('listing_javascript.js');
$j = ob_get_contents();
ob_end_clean();
echo create_script($j);

$url = '?' . dossier::get() . "&plugin_code=" . $_REQUEST['plugin_code'] . "&ac=" . $_REQUEST['ac'];
$array = array(
	array($url . '&sa=li', _('Liste'), _('Création, modification, Paramètre de listes, mailing list'), 0),
 	array($url . '&sa=de', _('Génération'), _('Génération listing'), 2),
	array($url . '&sa=hi', _('Historique'), _('Historique listing '), 3)
);

$sa = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : "de";
$def = 0;
switch ($sa)
{
	case 'de':
		$def = 2;
		break;
	case 'hi':
		$def = 3;
		break;
        case 'li':
                $def=0;
                break;
}

$cn = Dossier::connect();
// show menu
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=listing" target="_blank">Aide</a>' .
 '<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>' .
 '</div>';

echo ShowItem($array, 'H', 'mtitle ', 'mtitle ', $def, ' style="width:80%;margin-left:10%;border-collapse: separate;border-spacing:  5px;"');
echo '<div class="content" style="width:80%;margin-left:10%">';
// include the right file
if ($def == 0)
{
	require_once('include/liste.inc.php');
	exit();
}

/* Déclaration */
if ($def == 2)
{
	require_once('include/declaration.inc.php');
	exit();
}
/* Historique */
if ($def == 3)
{
	require_once 'include/historique.inc.php';
	exit();
}
?>

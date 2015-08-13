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

// Copyright Author Dany De Bontridder danydb@aevalys.eu
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(4400);
$cn=new Database(dossier::id());
$transform_version=0;
/*
 * load javascript
 */
ob_start();
require_once('transform_javascript.js');
$j = ob_get_contents();
ob_end_clean();
echo create_script($j);

if ( $cn->exist_schema('transform') == false)
  {
    require_once('include/class_install_transform.php');

    $iplugn=new Install_Transform($cn);
    $iplugn->install();
 }
 if ( $cn->get_value('select max(v_id) from transform.version') < $transform_version)
{
	require_once('include/class_install_transform.php');
	$iplugn = new Install_Transform($cn);
	$iplugn->upgrade($transform_version);
}
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=transformateur" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';

require_once "include/intervat_listing_assujetti.inc.php";

?>

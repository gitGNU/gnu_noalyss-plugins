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

/* !\file
 * \brief main file for importing card
 */
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(6910);
/*
 * load javascript
 */
$dossier=HtmlInput::default_value_request("gDossier", "0");
$ac=HtmlInput::default_value_request("ac", "exit");
$plugin_code=HtmlInput::default_value_request("plugin_code", "");
echo "<script>";
?>
var js_dossier="<?php echo $dossier; ?>";
var js_ac="<?php echo $ac; ?>";
var js_plugin_code="<?php echo $plugin_code; ?>";
<?php
require_once __DIR__.'/importcard.js';
echo "</script>";
require_once('include/class_import_card.php');
global $cn;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=importation_de_fiche" target="_blank">Aide</a>'.'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';

$cn=Dossier::connect();

// retrieve file and format if they exist
$format_id=HtmlInput::default_value_request("format", -1);
$record_id=HtmlInput::default_value_request("record", -1);

$upload=new Import_Card($record_id, $format_id);

if (!isset($_REQUEST['sa']))
{
    $upload->new_import();
    exit();
}
if ($_REQUEST['sa']=='import')
{
    /* We receive a file , we save and record an id for it */
    $upload->save_file();
    $upload->get_post_format();
    $upload->propose_format();
    $upload->test_import();
    exit();
}

/**
 * apply the change of the format or record it
 */
if ($_REQUEST['sa']=="test")
{
    /**
     * apply the change 
     */
    if (isset($_POST["apply_format"]))
    {
        // retrieve information and store them into db
        $upload->get_post_format();

        // Display the parameter
        $upload->propose_format();
        // Show the result 
        $upload->test_import();
        exit();
    }
    /**
     * Record the data into the db
     */
    if (isset($_POST["import_file"]))
    {
        // retriev info 
        $upload->get_post_format();

        // Import
        // show imported row
        $upload->record_import();
        
        exit();
    }
}

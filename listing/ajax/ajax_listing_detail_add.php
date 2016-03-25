<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

/**
 * @file
 * @brief Ajax file
 */
require_once $g_listing_home.'/include/class_rapav_listing.php';
require_once $g_listing_home.'/include/class_rapav_condition.php';

ob_start();
echo HtmlInput::title_box("Nouveau paramètre", $cin);
$obj=new RAPAV_Listing($id);
$obj->add_parameter($id);
$response = ob_get_contents();
ob_end_clean();
$html = escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl></ctl>
<code>$html</code>
</data>
EOF;
?>        
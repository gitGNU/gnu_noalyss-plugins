<?php
/**
 * @file
 * @brief Ajax file
 */
require_once 'include/class_rapav_listing.php';

ob_start();
echo HtmlInput::title_box("Nouveau paramètre", $cin);
$obj=new RAPAV_Listing($id);
$obj->add_parameter();
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
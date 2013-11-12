<?php

require_once 'include/class_rapav_listing.php';
$obj=new Rapav_Listing($id);
ob_start();
$obj->display();
$obj->button_add_param();
$response = ob_get_flush();
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
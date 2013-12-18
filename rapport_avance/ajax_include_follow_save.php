<?php

require_once 'include/class_rapav_listing_compute.php';

ob_start();
$compute = new RAPAV_Listing_Compute();
$compute->load($_GET['lc_id']);
$compute->include_follow($_GET);
$response = ob_get_clean();
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
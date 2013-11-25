<?php
/**
 * @file
 * @brief delete a row in Listing_Param
 */
global $cn;
require_once 'include/class_rapav_listing_param.php';
$del=new RAPAV_Listing_Param($id);
$del->Param->delete();

header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<lp_id>$id</lp_id>
<code>ok</code>
</data>
EOF;
exit();
?>     

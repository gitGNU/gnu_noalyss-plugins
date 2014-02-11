<?php
require_once 'include/class_rapav_listing.php';
$obj=new Rapav_Listing($id);
$obj->remove_modele();

ob_start();
$file=new IFile('listing_mod');
echo $file->input();
$response = ob_get_clean();
$html = escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$cout</ctl>
<code>$html</code>
</data>
EOF;
?>
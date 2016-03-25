<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

require_once $g_listing_home.'/include/class_rapav_listing_compute.php';

ob_start();
$compute = new RAPAV_Listing_Compute();
$compute->load($_GET['lc_id']);
$a_result = $compute->include_follow($_GET);
echo HtmlInput::title_box(_('Résultat'), 'include_follow_save_result');
echo '<ol>';
for ($i = 0; $i < count($a_result); $i++)
{
    echo '<li>' . $a_result[$i] . '</li>';
}
echo '</ol>';
echo HtmlInput::button_close('include_follow_save_result');
$response = ob_get_clean();
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
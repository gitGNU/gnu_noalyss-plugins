<?php
require_once 'include/class_rapav_listing_compute.php';

ob_start();
$listing=new RAPAV_Listing_Compute();
$listing->load($lc_id);
$a_result=$listing->send_mail($p_from,$p_subject,$p_message,$p_attach);
echo HtmlInput::title_box(_('Résultat'),'parameter_send_mail_result');
echo '<ol>';
for ($i=0;$i<count($a_result);$i++)
{
    echo '<li>'.$a_result[$i].'</li>';
}
echo '</ol>';
echo HtmlInput::button_close('parameter_send_mail_result');
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
<?php

require_once 'include/class_rapav_listing.php';
$obj=new Rapav_Listing($id);
ob_start();
if ( $id == -1 )
    echo HtmlInput::title_box("Ajout d'un listing", $cout);
else
    echo HtmlInput::title_box("Modification d'un listing", $cout);

echo '<form method="POST" enctype="multipart/form-data" id="listing_frm" onsubmit="return check_listing_add(\'listing_frm\')">';
echo HtmlInput::hidden('l_id',$id);
echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code'));
$obj->form_modify();
echo '<p>';
echo HtmlInput::submit("listing_add_sb", "Ajout");
echo '</p>';
echo '</form>';
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
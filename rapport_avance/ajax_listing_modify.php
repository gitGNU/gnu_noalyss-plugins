<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

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
echo '<ul class="aligned-block">';
echo '<li >';
echo HtmlInput::submit("listing_add_sb", "Valider");
echo '</li>';
echo '<li class="menu">';
echo HtmlInput::submit("listing_clone", "Cloner");
echo '</li>';
echo '</ul>';
echo '</p>';
echo '</form>';
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
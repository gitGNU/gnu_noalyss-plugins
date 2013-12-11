<?php
/**
 * @file
 * @brief display a declaration from history but you can't modify it
 */
require_once 'include/class_rapav_listing_compute.php';
global $cn;
echo HtmlInput::button_action("Retour","$('declaration_list_div').show(); $('declaration_display_div').hide();");
$decl = new RAPAV_Listing_Compute();
$decl->load($_GET['d_id']);
$decl->display(false);
//echo $decl->anchor_document();
echo '<hr>';
$decl->propose_CSV();
echo HtmlInput::button_action("Retour","$('declaration_list_div').show(); $('declaration_display_div').hide();");
// require_once '.php';

?>        
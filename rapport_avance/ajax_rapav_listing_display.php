<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

/**
 * @file
 * @brief display a declaration from history but you can't modify it
 */
require_once 'rapav_constant.php';
require_once 'include/class_rapav_listing_compute.php';
global $cn;
echo '<div class="content">';
echo '<p>';
echo HtmlInput::button_action("Retour","$('declaration_list_div').show(); $('declaration_display_div').hide();",'rttop'.$_GET['d_id'],'smallbutton');
echo '</p>';
$listing = new RAPAV_Listing_Compute();
$listing->load($_GET['d_id']);
$listing->display(false);
//echo $listing->anchor_document();
echo '<hr>';
$listing->propose_CSV();
$listing->propose_send_mail();
$listing->propose_include_follow();
$listing->propose_download_all();

echo HtmlInput::button_action("Retour","$('declaration_list_div').show(); $('declaration_display_div').hide();",'rt'.$_GET['d_id'],'smallbutton');
echo '</div>';
echo create_script('sorttable.makeSortable($("_tb"))');
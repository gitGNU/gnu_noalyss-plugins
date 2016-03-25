<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<?php
global $g_user;


ob_start();

$titre = new IText('ag_title');

// Profile in charged of the action
$ag_dest = new ISelect();
$ag_dest->name = "ag_dest";
// select profile
$aAg_dest = $cn->make_array("select  p_id as value, " .
        "p_name as label " .
        " from profile  "
        . "where p_id in (select p_granted from "
            . " user_sec_action_profile where ua_right='W' and p_id=" . $g_user->get_profile() . ") order by 2");
$ag_dest->value = $aAg_dest;

// -- Date
$date = new IDate();
$date->name = "ag_timestamp";
$date->value = date('d.m.Y');

// -- remind date
$remind_date = new IDate();
$remind_date->name = "ag_remind_date";
$remind_date->value = "";

// -- document
$category = new ISelect('dt_id');
$category->value = $cn->make_array("select dt_id,dt_value from document_type order by dt_value");
$category->readOnly = false;

//-- description
$desc = new ITextArea();
$desc->style = ' class="itextarea" style="width:90%;margin-left:5%"';
$desc->name = "ag_comment";

require_once $g_listing_home.'/template/include_follow.php';

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
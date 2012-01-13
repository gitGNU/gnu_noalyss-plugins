<?php

/**
 * @file gestion des budgets
 */

require_once 'class_copro_budget.php';
$bud=new Copro_Budget();

if ( isset ($_POST['bud_update']))
{
	$bud->save($_POST);
}



echo '<div id="bud_list" class="content">';
$bud->to_list();
echo HtmlInput::button("bud_add_bt","Ajout Budget","onclick=\"budget_add('".$_REQUEST['gDossier']."','".$_REQUEST['plugin_code']."','".$_REQUEST['ac']."')\"");
echo '</div>';
?>
<div id="divbuddetail">

</div>
<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

//////////////////////////////////////////////////////////////
// Modifie une definition de formulaire
//////////////////////////////////////////////////////////////
require_once 'include/class_rapav_formulaire.php';
if (!isset($_GET['f_id']) && isNum($_GET['f_id']) == 0)
    exit();
require_once 'include/formulaire_definition_show.inc.php';
echo '<hr>';
echo HtmlInput::button_action("Paramètre", sprintf("rapav_form_param('%s','%s','%s','%s')",$plugin_code,$ac,$gDossier,$_REQUEST['f_id'] ));
exit();
?>
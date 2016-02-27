<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

/**
 * @file
 * @brief Montre le résultat et permet de changer les paramètrages d'un formulaire
 *  uniquement pour ceux ayant un champs de calcul (formule, code tva+poste comptable + totaux intermédiare
 */
require_once 'include/class_rapav_formulaire.php';
if (!isset($_GET['f_id']) && isNum($_GET['f_id']) == 0)
    exit();
echo '<h1>Paramètre </h1>';
$form = new RAPAV_formulaire($_REQUEST['f_id']);
$form->load_definition();
$form->echo_formulaire();
$form->input_parameter();
echo '<hr>';
echo HtmlInput::button_action("Définition", sprintf("rapav_form_def('%s','%s','%s','%s')",$plugin_code,$ac,$gDossier,$_REQUEST['f_id'] ));
?>
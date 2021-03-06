<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
require_once 'listing_constant.php';

extract($_GET, EXTR_SKIP);
if ( ! isset($act)) {
    die(_('act invalide'));
}
global $cn;
$a_action=explode(',',
        'mod_form,add_row_definition,mod_param,add_param_detail,'.
        'rapav_search_code,save_param_detail,rapav_declaration_display,'.
        'listing_modify,listing_remove_modele,listing_display_definition,'.
        'listing_detail_add,save_param_listing,listing_detail_remove,'.
        'listing_search_code,rapav_listing_display,parameter_send_mail,send_mail,'
        . 'include_follow,include_follow_save,modify_param_detail,listing_detail_modify,modify_listing_description'
        . ',modify_rapav_description,get_condition,listing_get_description,listing_condition_input,listing_condition_save,'
        . 'save_definition');
if ( in_array($act,$a_action ) == true )
{
    include $g_listing_home.'/ajax/ajax_'.$act.'.php';
    exit();
}
switch ($act)
{
    case 'listing_condition_remove':
        $cn->exec_sql(" delete from rapport_advanced.listing_condition where id=$1",
                array($lc_id));
        break;
    /////////////////////////////////////////////////////////////////////////
    // Delete un formulaire_param_detail
    /////////////////////////////////////////////////////////////////////////
    case 'delete_param_detail':
        $cn->exec_sql("delete from rapport_advanced.formulaire_param_detail "
                . " where fp_id=$1", array($fp_id));
        break;
    /////////////////////////////////////////////////////////////////////
    // Delete a saved declaration (from history)
    /////////////////////////////////////////////////////////////////////
    case 'rapav_declaration_delete':
        $cn->exec_sql("delete from rapport_advanced.declaration where d_id=$1",
                array($_GET['d_id']));
        break;
    /////////////////////////////////////////////////////////////////////
    // Delete a saved listing (from history)
    /////////////////////////////////////////////////////////////////////
    case 'rapav_listing_delete':
        $cn->exec_sql("delete from rapport_advanced.listing_compute where lc_id=$1",
                array($_GET['d_id']));
        break;
    /////////////////////////////////////////////////////////////////////
    // Remove a template
    /////////////////////////////////////////////////////////////////////
    case 'rapav_remove_doc_template':
        require_once 'include/class_rapav_formulaire.php';
        $rapav = new Rapav_Formulaire($_GET['f_id']);
        $rapav->remove_doc_template();
        break;
    default:
        if ( DEBUG) var_dump($_GET);
        die ("Aucune action demandée");
}
?>

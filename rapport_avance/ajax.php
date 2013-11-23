<?php
extract($_GET);
global $cn;
switch ($act)
{
    /*     * ******************************************************************************************************* */
    // Modifie une definition de formulaire
    /*     * ******************************************************************************************************* */

    case 'mod_form':
        require_once 'include/class_rapav_formulaire.php';
        if (!isset($_GET['f_id']) && isNum($_GET['f_id']) == 0)
            exit();
        require_once 'include/formulaire_definition_show.inc.php';
        break;
    /*     * **************************************************************************************************************
     * Ajoute une ligne dans la definition
     * *************************************************************************************************************** */
    case 'add_row_definition':
        $type_row = $cn->make_array("select p_type,p_description from rapport_advanced.type_row order by p_description");
        $type_periode = $cn->make_array("select t_id,t_description from rapport_advanced.periode_type order by t_description");
        ?>
        <td>
            Nouv.
        </td>
        <td>
            <?php echo HtmlInput::hidden('p_id[]', -1) ?>
            <?php
            $p_code = new IText('p_code[]');
            $p_code->size = "10";
            echo $p_code->input();
            ?>
        </td>
        <td>
            <?php
            $p_libelle = new IText('p_libelle[]');
            $p_libelle->css_size = "100%";
            echo $p_libelle->input();
            ?>
        </td>
        <td>
            <?php
            $p_type = new ISelect('p_type[]');
            $p_type->value = $type_row;
            echo $p_type->input();
            ?>
        </td>
        <td>
            <?php
            $p_type_periode = new ISelect('t_id[]');
            $p_type_periode->value = $type_periode;
            echo $p_type_periode->input();
            ?>
        </td>
        <td>
            <?php
            $p_order = new INum('p_order[]');
            $p_order->prec = 0;
            $p_order->size = 4;
            echo $p_order->input();
            ?>
        </td>
        <?php
        break;
    /*     * **************************************************************************************************************
     * Montre le résultat et permet de changer les paramètrages d'un formulaire
     * uniquement pour ceux ayant un champs de calcul (formule, code tva+poste comptable + totaux intermédiare
     * *************************************************************************************************************** */
    case 'mod_param':
        require_once 'include/class_rapav_formulaire.php';
        if (!isset($_GET['f_id']) && isNum($_GET['f_id']) == 0)
            exit();
        echo '<h1>Paramètre </h1>';
        $form = new RAPAV_formulaire($_REQUEST['f_id']);
        $form->load_definition();
        $form->echo_formulaire();
        $form->input_parameter();
        break;

    /*     * ****************************************************************************************************************
     * Montre un écran pour ajouter une ligne de formulaire dans les paramètre de formulaires
     */
    case 'add_param_detail':
        include 'ajax_add_param_detail.php';
        break;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //rapav_search_code cherche les codes du formulaires courants
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'rapav_search_code':
        include 'ajax_search_code.php';
        break;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Sauve résultat et renvoie un xml
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'save_param_detail':
        include 'ajax_save_param_detail.php';
        break;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Delete un formulaire_param_detail
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'delete_param_detail':
        $cn->exec_sql("delete from rapport_advanced.formulaire_param_detail where fp_id=$1", array($fp_id));
        break;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Display a saved declaration from history
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'rapav_declaration_display':
        include 'ajax_declaration_display.php';
        break;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Delete a saved declaration (from history)
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'rapav_declaration_delete':
        $cn->exec_sql("delete from rapport_advanced.declaration where d_id=$1", array($_GET['d_id']));
        break;
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Remove a template
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'rapav_remove_doc_template':
        require_once 'include/class_rapav_formulaire.php';
        $rapav = new Rapav_Formulaire($_GET['f_id']);
        $rapav->remove_doc_template();
        break;
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Show a div to enter new listing
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'listing_modify':
       include 'ajax_listing_modify.php';
        exit();
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Remove a document modele
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'listing_remove_modele':
       include 'ajax_listing_remove_modele.php';
        exit();
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Display the definition of a listing
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'listing_display_definition':
        include 'ajax_listing_display_definition.php';
        exit();
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Add a param to a listing
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'listing_param_add':
        include 'ajax_listing_param_add.php';
        exit();
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // save a param to a listing
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    case 'save_param_listing':
        include 'ajax_save_param_listing.php';
        exit();
    default:
        if ( DEBUG) var_dump($_GET);
        die ("Aucune action demandée");
}
?>

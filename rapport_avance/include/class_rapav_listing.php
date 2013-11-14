<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rapav_listing
 *
 * @author dany
 */
require_once 'class_rapport_avance_sql.php';
require_once 'class_rapav_listing_param.php';
class Rapav_Listing {
    private $Data; /*!< RAPAV_Listing_SQL */
    private $a_detail; /*!< array of RAPAV_Listing_Param corresponding to the listing*/
    function __construct($p_id = -1) {
        $this->Data = new RAPAV_Listing_SQL($p_id);
        $this->a_detail=array();
    }

    /**
     * display a list of the existing list
     */
    function to_list() {
        $res = $this->Data->seek('join fiche_def using (fd_id) order by l_name');
        require 'template/rapav_listing_to_list.php';
    }

    /**
     * Display a button for adding a new listing
     */
    static function Button_Add_Listing() {
        $arg = array(
            'gDossier' => Dossier::id(),
            'ac' => $_REQUEST['ac'],
            'pc' => $_REQUEST['plugin_code'],
            'id' => -1,
            'cin' => 'listing_tb_id',
            'cout' => 'listing_add_div');
        $json = 'listing_modify(' . str_replace('"', "'", json_encode($arg)) . ')';
        echo HtmlInput::button_action("Ajout", $json);
    }

    /**
     * @brief display a form to save a new list
     * 
     */
    function form_modify() {
        global $cn;
        $name = new IText('name');
        $description = new ITextArea('description');
        $description->style=' style="margin:0px;width:100%" class="itextarea"';
        $file = new IFile('listing_mod');
        $fichedef = new ISelect('fiche_def');
        $fichedef->value = $cn->make_array('select fd_id,fd_label from fiche_def order by fd_label');
        $str_remove = "";
        /*
         * if $this->l_id <> -1 then modification otherwise add
         */
        if ($this->Data->l_id <> -1) {
            $name->value = $this->Data->l_name;
            $description->value = $this->Data->l_description;
            $fichedef->selected = $this->Data->fd_id;
            $ck = new ICheckBox('remove');
            $str_remove = " Cochez pour effacer " . $ck->input();
            // If there is a file
            if ($this->Data->l_filename != "") {
                $file = new ISpan('listing_mod_id');
                // Add js for removing 
                $arg = array(
                    'gDossier' => Dossier::id(),
                    'ac' => $_REQUEST['ac'],
                    'pc' => $_REQUEST['plugin_code'],
                    'id' => $this->Data->l_id,
                    'cin' => '',
                    'cout' => 'listing_mod_id');
                $json = 'listing_remove_modele(' . str_replace('"', "'", json_encode($arg)) . ')';
                $file->value = $this->Data->l_filename . HtmlInput::anchor(' <span style="background-color:red">X </span>', "", ' onclick="' . $json . '"');
            }
        }
        require 'template/rapav_listing_form_modify.php';
    }

    /**
     * Insert or update a listing into rapport_advanced.listing, load also the
     * file
     * @global type $cn
     * @param type $p_array
     * @throws Exception
     */
    function save($p_array) {
        global $cn;
        try {
            $cn->start();
            if (strlen(trim($p_array['name'])) == 0) {
                throw new Exception('Le nom ne peut pas être vide');
            }

            $this->Data->setp('name', $p_array['name']);
            $this->Data->setp('description', $p_array['description']);
            $this->Data->setp('fiche_def_id', $p_array['fiche_def']);
            $this->Data->save();
            $this->load_file();
            $cn->commit();
        } catch (Exception $ex) {
            $cn->rollback();
        }
    }

    /**
     * @brief 
     * @global type $cn
     * @return int
     */
    function load_file() {
        global $cn;
        // nothing to save
        if (sizeof($_FILES) == 0)
            return;
        try {
            $name = $_FILES['listing_mod']['name'];
            $new_name = tempnam($_ENV['TMP'], 'fiche_def');
            // check if a file is submitted
            if (strlen($_FILES['listing_mod']['tmp_name']) != 0) {
                // upload the file and move it to temp directory
                if (move_uploaded_file($_FILES['listing_mod']['tmp_name'], $new_name)) {
                    $oid = $cn->lo_import($new_name);
                    // check if the lob is in the database
                    if ($oid == false) {
                        $cn->rollback();
                        return 1;
                    }
                }
                // the upload in the database is successfull
                $this->Data->l_lob = $oid;
                $this->Data->l_filename = $_FILES['listing_mod']['name'];
                $this->Data->l_mimetype = $_FILES['listing_mod']['type'];
                $this->Data->l_size = $_FILES['listing_mod']['size'];

                // update rapav
                $this->Data->update();
            }
        } catch (Exception $ex) {
            $cn->rollback();
            throw $ex;
        }
    }
/**
 * @brief remove a document template
 * @global type $cn database connection
 * @return type
 */
    function remove_modele() {
        global $cn;
        if ($this->Data->l_lob == null)
            return;
        try {
            $cn->start();
            $this->Data->cn->lo_unlink($this->Data->l_lob);
            $this->Data->l_filename = null;
            $this->Data->l_lob = null;
            $this->Data->l_size = null;
            $this->Data->l_mimetype = null;
            $this->Data->update();
            $cn->commit();
        } catch (Exception $e) {
            $cn->rollback;
            throw $ex;

        }
    }
    /**
     * @brief delete a listing + lobs
     * @throws type
     */
    function delete()
    {
        try {
            $this->remove_modele();
            $this->Data->delete(); 
        } catch (Exception $e) {
            $cn->rollback;
            throw $ex;

        }
    }
    /**
     * @brief display the parameter of a listing
     * let add or remove detail
     */
    function display()
    {
        // Load all listing_parameter
        $this->load_detail();
        
        // Display them avec an anchor to update / delete (javascript)
        include_once 'template/rapav_listing_definition.php';
    }
    /**
     * @brief Load the detail of a listing
     * @throws Exception Undefined object
     */
    function load_detail()
    {
        if ( $this->Data->getp('id') == -1) 
            throw new Exception ("Undefined objet ".__FILE__.':'.__LINE__);
        $this->a_detail=  RAPAV_Listing_Param::get_listing_detail($this->Data->getp('id'));
        
    }
    /**
     * @brief Display button for adding detail to a list definition, it means
     */
    function button_add_param() 
    {
        $button=new IButton('detail_add_bt','Ajout','detail_add_bt');
        $arg=  json_encode(array(
            'cin'=>'listing_definition_div_id',
            'gDossier'=>Dossier::id(),
            'id'=>$this->Data->getp('id'),
            'tb_id'=>'definition_tb_id',
            'ac'=>$_REQUEST['ac'])
            );
        $arg=str_replace('"',"'",$arg);
        $button->javascript='listing_param_add('.$arg.')';
        echo $button->input();
    }
}

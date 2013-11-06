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

class Rapav_Listing
{

    function __construct($p_id = -1)
    {
        $this->Data = new RAPAV_Listing_SQL($p_id);
    }

    /**
     * display a list of the existing list
     */
    function to_list()
    {
        $res = $this->Data->seek('join fiche_def using (fd_id) order by l_name');
        require 'template/rapav_listing_to_list.php';
    }

    /**
     * Display a button for adding a new listing
     */
    static function Button_Add_Listing()
    {
        $arg = array(
            'gDossier' => Dossier::id(),
            'ac' => $_REQUEST['ac'],
            'pc' => $_REQUEST['plugin_code'],
            'id' => 0,
            'cin' => 'listing_tb_id',
            'cout' => 'listing_add_div');
        $json = 'listing_add(' . str_replace('"', "'", json_encode($arg)) . ')';
        echo HtmlInput::button_action("Ajout", $json);
    }

    /**
     * @brief display a form to save a new list
     * 
     */
    static function form_add()
    {
        global $cn;
        $name = new IText('name');
        $description = new ITextArea('description');
        $file = new IFile('listing_mod');
        $fichedef = new ISelect('fiche_def');
        $fichedef->value = $cn->make_array('select fd_id,fd_label from fiche_def order by fd_label');
        require 'template/rapav_listing_form_add.php';
    }

    /**
     * Insert a new list into rapport_advanced.listing
     * @global type $cn
     * @param type $p_array
     * @throws Exception
     */
    function insert($p_array)
    {
        global $cn;
        try
        {
            $cn->start();
            if (strlen(trim($p_array['name'])) == 0)
            {
                throw new Exception('Le nom ne peut pas être vide');
            }

            $this->Data->setp('name', $p_array['name']);
            $this->Data->setp('description', $p_array['description']);
            $this->Data->setp('fiche_def_id', $p_array['fiche_def']);
            $this->Data->insert();
            $this->load_file();
            $cn->commit();
        } catch (Exception $ex)
        {
            $cn->rollback();
        }
    }

    /**
     * @brief 
     * @global type $cn
     * @return int
     */
    function load_file()
    {
        global $cn;
        // nothing to save
        if (sizeof($_FILES) == 0)
            return;
        try
        {
            $name = $_FILES['listing_mod']['name'];
            $new_name = tempnam($_ENV['TMP'], 'fiche_def');
            // check if a file is submitted
            if (strlen($_FILES['listing_mod']['tmp_name']) != 0)
            {
                // upload the file and move it to temp directory
                if (move_uploaded_file($_FILES['listing_mod']['tmp_name'], $new_name))
                {
                    $oid = $cn->lo_import($new_name);
                    // check if the lob is in the database
                    if ($oid == false)
                    {
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
        } catch (Exception $ex)
        {
            $cn->rollback();
            throw $ex;
        }
    }

}

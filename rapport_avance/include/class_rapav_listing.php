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
    
    function __construct($p_id=-1)
    {
        $this->Data=new RAPAV_Listing_SQL($p_id);
    }
    /**
     * display a list of the existing list
     */
    function to_list()
    {
       $res= $this->Data->seek(' order by l_name');
       require 'template/rapav_listing_to_list.php';
    }
    /**
     * Display a button for adding a new listing
     */
    static function Button_Add_Listing()
    {
        $arg=array(
            'gDossier'=>Dossier::id(),
            'ac'=>$_REQUEST['ac'],
            'pc'=>$_REQUEST['plugin_code'],
            'id'=>0,
            'cin'=>'listing_tb_id',
            'cout'=>'listing_add_div');
        $json='listing_add('.str_replace('"',"'",json_encode($arg)).')';
        echo HtmlInput::button_action("Ajout", $json);
    }
    /**
     * @brief display a form to save a new list
     * 
     */
    static function form_add()
    {
        global $cn;
        $name=new IText('name');
        $description=new ITextArea('description');
        $file=new IFile('listing_mod');
        $fichedef=new ISelect('fiche_def');
        $fichedef->value=$cn->make_array('select fd_id,fd_label from fiche_def order by fd_label');
        require 'template/rapav_listing_form_add.php';
    }
    function insert($p_array)
    {
        if (strlen(trim($p_array['name']))==0) {
            die ('Le nom ne peut pas être vide');
        }
        $this->Data->setp('name',$p_array['name']);
        $this->Data->setp('description',$p_array['description']);
        $this->Data->setp('fiche_def_id',$p_array['fiche_def']);
        $this->Data->insert();
    }
    
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @brief Manage the tables rapport_advanced.listing_param_detail and
 * rapport_advanced.listing_param. The data are in Param and Detail
 *
 * @author danydb
 */
require_once 'class_rapport_avance_sql.php';

class RAPAV_Listing_Param {
    private  $Param;  /*!< RAPAV_Listing_Param_SQL */
    private  $Detail;  /*!< RAPAV_Listing_Param_Detail_SQL */
    
    /**
     * constructor, initialize Data with a RAPAV_Listing_Param_SQL
     * @param p_id is the primary key of Listing_Param : which is the FK for the detail
     * 
     */
    function __construct($p_id=-1) {
        global $cn;
        $this->Param = new RAPAV_Listing_Param_SQL($p_id);
        $this->Detail=new RAPAV_Listing_Param_Detail_SQL();
        if ( $p_id !=-1)
        {
            $this->load_detail();
        }
    }
    /**
     * Set the id of Param and load from database 
     * @param type $p_id
     */
    function set_id ($p_id)
    {
        $this->Param->setp('id',$p_id);
        $this->load();
       
    }
    /**
     * Retrieve data from listing_param_detail
     * and set Param and Detail
     * @see load_detail
     */
    function load()
    {
        $this->Param->load();
        $this->load_detail();
        
    }
    /**
     * Retrieve data from listing_param_detail
     */
    function load_detail()
    {
       $id=$this->Param->getp('id');
       
       if ( $id !=-1)
        {
            $pk_detail=$cn->get_value(' select fp_id from 
                    rapport_advanced.listing_param_detail
                    where
                    lp_id = $1',array($id));
            
            if ($cn->count() != 0)    {
                $this->Detail->setp('id',-1);
                return;
            }
            
            $this->Detail->setp('id',$pk_detail);
            
            $this->Detail->load();
            
        }
    }
    /**
     * Get all data from listing_param and listing_param_detail 
     * for a listing
     * @param $p_id Listing::l_id
     * @return  array of object RAPAV_Listing_Param
     */
    static function get_listing_detail ($p_id)
    {
       global $cn;
       $a_listing_param=array();
       
       $a_param_id=$cn->get_array('select lp_id 
               from rapport_advanced.listing_param 
               where l_id=$1 order by l_order'.array($p_id));
       
       for ($i=0;$i<count($a_param_id);$i++)
       {
           $a_listing_param[]=new RAPAV_Listing_Param($a_param_id['lp_id'][$i]);
       }
       return $a_listing_param;
    }
}

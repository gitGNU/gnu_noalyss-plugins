<?php

/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * @brief Manage the tables rapport_advanced.listing_param_detail and
 * rapport_advanced.listing_param. The data are in Param and Detail
 *
 * @author danydb
 */
require_once 'class_rapport_avance_sql.php';
require_once 'class_rapav_listing_formula.php';

class RAPAV_Listing_Param {
    var  $Param;  /*!< RAPAV_Listing_Param_SQL */
    
    /**
     * constructor, initialize Data with a RAPAV_Listing_Param_SQL
     * @param p_id is the primary key of Listing_Param : which is the FK for the detail
     * 
     */
    function __construct($p_id=-1) {
        global $cn;
        $this->Param = new RAPAV_Listing_Param_SQL($p_id);
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
        // $this->load_detail();
        
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
               where l_id=$1 order by l_order',array($p_id));
       
       for ($i=0;$i<count($a_param_id);$i++)
       {
           $a_listing_param[]=new RAPAV_Listing_Param($a_param_id[$i]['lp_id']);
       }
       return $a_listing_param;
    }
    /**
     * @brief display a div for adding or modifing a parameter
     * @param $p_id is the listing id
     */
    function input($p_id)
    {
        global $cn;
        $code=new IText('code_id');
        $comment=new IText('comment');
        $order=new INum('order');
        $order->value=$cn->get_value("select coalesce(max(l_order),0)+10 from 
                    rapport_advanced.listing_param 
                    where 
                    l_id=$1
                    ",array($this->Param->l_id));
        $attribute=new RAPAV_Formula_Attribute($this->Param);
        $formula=new RAPAV_Formula_Formula($this->Param);
        $compute=new RAPAV_Formula_Compute($this->Param);
        $account=new RAPAV_Formula_Account($this->Param);
        $account->set_listing($p_id);
        $attribute->set_listing($p_id);
        require 'template/listing_param_input.php';
    }
    function button_delete()
    {
         $json = sprintf(" onclick=\"listing_detail_remove('%s','%s','%s','%s')\"", 
            Dossier::id(), 
            $_REQUEST['plugin_code'], 
            $_REQUEST['ac'], 
            $this->Param->getp('lp_id') );
         echo HtmlInput::anchor("Effacer", "", $json);
    }
    function button_modify()
    {
        $json=json_encode(array('pc'=>$_REQUEST['plugin_code'],
            'ac'=>$_REQUEST['ac'],
            'gDossier'=>Dossier::id(),
            'id'=>$this->Param->getp('lp_id'),
            'cin'=>'listing_param_input_div_id'));
        $json=  str_replace('"', "'", $json);
         $js = sprintf(" onclick=\"listing_detail_modify(%s)\"", 
                 $json             );
         echo HtmlInput::anchor("Modifier", "", $js);
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rapav_listing_formula
 *
 * @author dany
 */
abstract class RAPAV_Listing_Formula
{
    abstract public function display();
    abstract public function compute();
    abstract public function input();
    /**
     * 
     * @param $p_obj RAPAV_Listing_Param_SQL
     */
    public function make_object($p_obj)
    {
        
    }
}

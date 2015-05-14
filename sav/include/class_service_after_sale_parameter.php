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
/* $Revision$ */

// Copyright (c) 2015 Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief class for the table service_after_sale.sav_parameter
 *
 */
class Service_After_Sale_Parameter
{
    private $good;  //!< Parameter for returned material : list separated by comma of fd_id
    private $spare; //!<  parameter for spare part : : list separated by comma of fd_id
    private $ledger ; //!<  ledger id (jrn_def.jrn_def_id)
    private $workhour; //!< card id for the workhour 
    
    
    //
    // constructor
    function __construct($p_cn) {
        $this->db=$p_cn;
        $Res=$p_cn->exec_sql("select * from service_after_sale.sav_parameter ");
        for ($i = 0;$i < Database::num_row($Res);$i++)
        {
            $row=Database::fetch_array($Res,$i);
            $key=$row['code'];
            $elt=$row['value'];
            
            // store value here
            $this->{"$key"}=$elt;
        }

    }
    /*!
     * \brief  save the parameter into the database by inserting or updating
     *
     *
     * \param $p_attr give the attribut name
     *
     */
    function save($p_option,$p_value)
    {
		global $cn;
        // check if the parameter does exist
        if ( $cn->get_value('select count(*) from service_after_sale.sav_parameter where code=$1',array($p_option)) != 0 )
        {
            $Res=$cn->exec_sql("update service_after_sale.sav_parameter set value=$1 where code=$2",
                                     array($p_value,$p_option));
        }
        else
        {

            $Res=$cn->exec_sql("insert into service_after_sale.sav_parameter (code,value) values( $1,$2)",
                                     array($p_option,$p_value));

        }

    }
    /**
     * return list of category of card for materials, separated by comma, value empty or null is an error
     * @return string
     */
    function get_material()
    {
        return $this->good;
    }
    /**
     * Set the string as material and verify the string is valid, the string
     * must be not empty, must contains existing fd_id and separated by comma, not other
     * characters than comma and number.
     * @param $p_material string
     * @exception  : no material
     */
    function set_material($p_material)
    {
       if (trim($p_material)=="")                throw new Exception (_('Aucun matériel choisi'),NOMATERIAL);
            
        $this->good=$p_material;
    }
    /**
     * return list of category of card for materials, , separated by comma, value empty or null is an error
     * @return string
     */
    function get_spare_part()
    {
        
        return $this->spare;
        
    }
     /**
     * Set the string for spare_part and verify the string is valid, the string
     * must be not empty, must contains existing fd_id and separated by comma, not other
     * characters than comma and number.
     * @param $p_spare string
     * @exception  : no material
      * 
      * @return string
      */
    function set_spare_part($p_spare)
    {
        if (trim($p_spare)=="")                throw new Exception (_('Aucun matériel choisi'),NOMATERIAL);
            
        $this->spare=$p_spare;
    }
    function get_workhour()
    {
        return $this->workhour;
    }
    function set_workhour($p_workhour)
    {
        $this->workhour=$p_workhour;
    }
    /**
     * returns ledger id
     */
    function get_ledger()
    {
        return $this->ledger;
    }
    function set_ledger($p_ledger)
    {
        $this->ledger=$p_ledger;
    }
    /**
     * Display list of available ledgers of sales
     * @return  HTML select string 
     */
    function input_ledger()
    {
        $ledger=new Acc_Ledger($this->db,$this->ledger);
        $select= $ledger->select_ledger('VEN',3);
        $select->name='ledger';
        return $select->input();
    }
    
    /**
     * Display current card id and propose to update it
     * @return \ICard
     */
    function input_workhour()
    {
        global $cn;
      $select=new ISelect('workhour_select');
      $select->value=$cn->make_array('select fd_id, fd_label from fiche_def order by fd_label');
      $select->selected=$this->get_workhour();
      return $select->input();
        
    }
    /**
     * Display the card category used for the spare part
     * @return \ICard
     */

    function input_spare_part()
    {
           global $cn;
      $select=new ISelect('spare_part_select');
      $select->value=$cn->make_array('select fd_id, fd_label from fiche_def order by fd_label');
      $select->selected=$this->get_spare_part();
      return $select->input();
    }
    /**
     * Display and input the card category used for the good
     * @return \ICard
     */

    function input_good()
    {
           global $cn;
      $select=new ISelect('good_select');
      $select->value=$cn->make_array('select fd_id, fd_label from fiche_def order by fd_label');
      $select->selected=$this->get_material();
      return $select->input();
    }
}   
?>

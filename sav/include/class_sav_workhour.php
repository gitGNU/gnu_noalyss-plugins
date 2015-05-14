<?php
/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// Copyright (2015) Author Dany De Bontridder <dany@alchimerys.be>

require_once 'class_sav_workhour_sql.php';
require_once 'class_sav_repair_card_sql.php';
/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
class Sav_WorkHour
{
    private $workhour_sql;
    
    function __construct($p_id=-1)
    {
        $this->workhour_sql=new Sav_Workhour_SQL($p_id);
    }
    function set_repair_card($p_id)
    {
        $this->workhour_sql->repair_card_id=$p_id;
    }
    function set_description ($p_description)
    {
        if ( trim($p_description) == '') $p_description=$this->get_name();
        $this->workhour_sql->work_description=$p_description;
    }
    function set_id($p_id)
    {
        $this->workhour_sql->id=$p_id;
    }
    function set_workhour($p_amount)
    {
        $this->workhour_sql->total_workhour=$p_amount;
    }
    function get_repair_card()
    {
        return $this->workhour_sql->repair_card_id;
    }
    function get_description ()
    {
        $this->workhour_sql->work_description  ;
    }
    function get_id()
    {
        return $this->workhour_sql->id;
    }
    function get_workhour()
    {
        return $this->workhour_sql->total_workhour;
    }
    function get_qcode()
    {
        global $cn;
        $fiche = new Fiche($cn,$this->workhour_sql->f_id_workhour);
        return $fiche->strAttribut(ATTR_DEF_QUICKCODE);
    }
    function get_name()
    {
        global $cn;
        $fiche = new Fiche($cn,$this->workhour_sql->f_id_workhour);
        return $fiche->strAttribut(ATTR_DEF_NAME);
    }
    function get_workhour_id()
    {
        return $this->workhour_sql->f_id_workhour;
    }
    function set_workhour_id()
    {
        return $this->workhour_sql->f_id_workhour;
    }
    function print_row()
    {
        global $cn,$gDossier,$ac,$plugin_code;
        
        // Workhour
        $hours=$this->get_workhour();
        // Material
        $qcode=$this->get_qcode();
        $name=$this->get_name();
        $description=$this->get_description();
        
        // Javascript
        $js=sprintf('workhour_remove(\'%s\',\'%s\',\'%s\',\'%s\')',
                       $gDossier,$ac,$plugin_code,$this->workhour_sql->id);
        
        // template
        ob_start();
        require 'template/sas_workhour_print_row.php';
        $result=ob_get_clean();
        return $result;
    }
    function display_list($p_repair_id)
    {
        global $cn;
        bcscale(2);
        $sql='select id
                from service_after_sale.sav_workhour as sp1
                where 
                sp1.repair_card_id=$1
                ';
        $a_workhour=$cn->get_array($sql,array($p_repair_id));
        $count_workhour=count($a_workhour);
        require 'template/workhour_display_list.php';
    }
    function add($p_repair,$p_qcode,$p_hour,$p_description)
    {
       global $cn;
        $repair=new Sav_Repair_Card_SQL($p_repair);
        if ( $repair->id == -1 ) throw new Exception('Inexistent repair card',NOMATERIAL);
        
        $fiche=new Fiche($cn);
        $fiche->get_by_qcode($p_qcode, FALSE);
        $workhour_id=$fiche->id;
         /**
         * @todo vérifier que la carte demandée appartient bien  à la catégorie
         * de fiche
         */
        if ( $workhour_id == 0) throw new Exception (_('Inexistant spare part'),NOSPAREPART);
        
        $this->workhour_sql->id=-1;
        $this->workhour_sql->repair_card_id=$p_repair;
        $this->workhour_sql->total_workhour=$p_hour;
        $this->workhour_sql->work_description=(trim($p_description)=='')?$fiche->getName():strip_tags($p_description);
        $this->workhour_sql->f_id_workhour=$workhour_id;
        $this->workhour_sql->save(); 
    }
    function remove()
    {
        $this->workhour_sql->delete();
    }
}
?>

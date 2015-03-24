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
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

require_once 'class_sav_repair_card_sql.php';
require_once 'class_sav_spare_part_sql.php';

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
class Sav_Spare_Part
{
    private $spare_part;
    function __construct()
    {
        $this->spare_part=new Sav_Spare_Part_SQL;
    }
    function display_list(Sav_Repair_Card_Sql $p_repair_card)
    {
        global $cn;
        bcscale(2);
        $sql='select id,f_id 
                from service_after_sale.sav_spare_part  as sp1
                join fiche_detail as fd1 on (sp1.f_id_material=fd1.f_id)
                where 
                repair_card_id=$1
                and ad_id=23';
        $a_spare=$cn->get_array($sql,array($p_repair_card->id));
        $count_spare=count($a_spare);
        require 'template/spare_part_display_list.php';
    }
    function repair_card_add($p_repair,$p_qcode,$p_quant)
    {
        global $cn;
        $repair=new Sav_Repair_Card_SQL($p_repair);
        if ( $repair->id == -1 ) throw new Exception('Inexistent repair card',NOMATERIAL);
       
        $fiche=new Fiche($cn);
        $fiche->get_by_qcode($p_qcode, FALSE);
        $material_id=$fiche->id;
        
        if ( $material_id == 0) throw new Exception (_('Inexistant spare part'),NOSPAREPART);
        
        $this->spare_part->repair_card_id=$p_repair;
        $this->spare_part->id=-1;
        $this->spare_part->quantity=$p_quant;
        $this->spare_part->f_id_materiel=$material_id;
        $this->spare_part->save();
        
    }
    function get_id()
    {
        return $this->spare_part->id;
    }
    function get_name()
    {
        global $cn;
        $fiche = new Fiche($cn,$this->spare_part->f_id_materiel);
        return $fiche->strAttribut(ATTR_DEF_NAME);
        
    }
    function get_qcode()
    {
        global $cn;
        $fiche = new Fiche($cn,$this->spare_part->f_id_materiel);
        return $fiche->strAttribut(ATTR_DEF_QUICKCODE);
    }
}
?>

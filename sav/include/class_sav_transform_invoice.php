<?php
/* 
 * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/***
 * @file 
 * @brief
 *
 */
require_once 'class_service_after_sale.php';

class Sav_Transform_Invoice {
    //put your code here
    function __construct(Service_After_Sale $sav) {
        $this->sav=$sav;
    }
    private function echo_access ()
    {
            echo HtmlInput::hidden('ac', 'VEN');
    }
    private function date_sale()
    {
        echo HtmlInput::hidden('e_date', date('d.M.y'));
    }
    private function customer()
    {
        echo HtmlInput::hidden('e_client', $this->sav->get_customer_qcode());
    }
    
    private function number_item()
    {
        global $cn;
        $repair_card=$this->sav->get_card_id();
        $count_spare=$cn->get_value("select count(*) from service_after_sale.sav_spare_part where repair_card_id=$1",array($repair_card));
        $count_workhour=$cn->get_value("select count(*) from service_after_sale.sav_workhour where repair_card_id=$1",array($repair_card));
        return ($count_spare+$count_workhour);
    }
    private function display_workhour($p_start)
    {
        global $cn,$g_sav_parameter;
        $repair_card=$this->sav->get_card_id();
        $a_workhour=$cn->get_array('
            select total_workhour,work_description from service_after_sale.sav_workhour where repair_card_id=$1
            ',array($repair_card));
        $nb=count($a_workhour);
        for ($i=0;$i<$nb;$i++)
        {
            $idx=$i+$p_start;
            $fiche_workhour=new Fiche($cn,$g_sav_parameter->get_workhour_qcode());
            echo HtmlInput::hidden('e_march'.$idx,$fiche_workhour->get_quick_code());
            echo HtmlInput::hidden('e_march'.$idx.'_label',h($a_workhour[$i]['work_description']));
            echo HtmlInput::hidden('e_quant'.$idx,h($a_workhour[$i]['total_workhour']));
            echo HtmlInput::hidden('e_march'.$idx.'_price',$fiche_workhour->strAttribut(ATTR_DEF_PRIX_VENTE, 0));
        }
        return $i;
                
    }
    private function display_spare_part($p_start)
    {
        global $cn,$g_sav_parameter;
        $repair_card=$this->sav->get_card_id();
        $a_spare_part=$cn->get_array('
            select f_id_material,quantity from service_after_sale.sav_spare_part where repair_card_id=$1
            ',array($repair_card));
        $nb=count($a_spare_part);
        for ($i=0;$i<$nb;$i++)
        {
            $idx=$i+$p_start;
            $fiche_spare_part=new Fiche($cn,$a_spare_part[$i]['f_id_material']);
            echo HtmlInput::hidden('e_march'.$idx,$fiche_spare_part->get_quick_code());
            echo HtmlInput::hidden('e_march'.$idx.'_label','');
            echo HtmlInput::hidden('e_quant'.$idx,h($a_spare_part[$i]['quantity']));
            echo HtmlInput::hidden('e_march'.$idx.'_price',$fiche_spare_part->strAttribut(ATTR_DEF_PRIX_VENTE, 0));
        }
        return $i;
                
    }
    public function form()
    {
        global $g_sav_parameter;
        $this->echo_access();
        $this->date_sale();
        $this->customer();
        echo HtmlInput::hidden('p_jrn',$g_sav_parameter->get_ledger());
        echo HtmlInput::hidden('nb_item',$this->number_item());
        echo HtmlInput::hidden('correct',1);
        $max_spare_part=$this->display_spare_part(0);
        $this->display_workhour($max_spare_part);
        
    }
}

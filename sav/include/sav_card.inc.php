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

$action=HtmlInput::default_value_request("sb", "list");
require_once 'class_service_after_sale.php';

switch ($action) {
    case "list" :
        $service=new Service_After_Sale();
        $service->button_add();
        $service->display_list(" where card_status='E' ");
        $service->button_add();
        return;
    case "detail" :
        $card=HtmlInput::default_value_request("repair_card_id", -1);
        /* We can save it here if needed*/
        $save=HtmlInput::default_value_post('save_repair_card','none');
        $service=new Service_After_Sale();
        try 
        {
            if ($save != 'none') 
            {
                // save from post
                if ( $card == -1 ) 
                {
                    /*
                     * Add a new one
                     */
                    $service->set_status('E');
                    $service->set_date_reception(HtmlInput::default_value_post('date_reception', date('d.m.Y')));
                    $service->set_garantie(HtmlInput::default_value_post('garantie',''));
                    $service->set_material(HtmlInput::default_value_post('good_id',-1),'qcode');
                    $service->set_customer(HtmlInput::default_value_post('cust_id',-1),'qcode');
                    $service->set_description(HtmlInput::default_value_post('description',-1));
                    $service->save();
                    $card=$service->get_card_id();
                } else 
                {
                    /*
                     * Update card
                     */
                    $service->set_card_id($card);
                    $service->set_status(HtmlInput::default_value_post('status_repair','E'));
                    $service->set_date_start(HtmlInput::default_value_post('date_start', date('d.m.Y')));
                    $service->set_date_end(HtmlInput::default_value_post('date_end', date('d.m.Y')));
                    $service->set_garantie(HtmlInput::default_value_post('garantie',''));
                    $service->set_description(HtmlInput::default_value_post('description',-1));
                    $service->save();
                }
            
            }
            
        }        catch (Exception $e) 
        {
            echo h2info(_('Paramètre invalide'));
        }
        $service->set_card_id($card);
        $service->display_detail();
        return;
      
    
}
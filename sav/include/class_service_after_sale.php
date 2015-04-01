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


/* * *
 * @file 
 * @brief main class for the Service After Sale plugin
 *
 */
require_once 'class_sav_repair_card_sql.php';
require_once 'class_service_after_sale_parameter.php';
require_once 'class_sav_workhour.php';
require_once 'class_sav_spare_part.php';

class Service_After_Sale
{

    private $param; // Parameters of the application
    private $repair_card;

    public function __construct()
    {
       
        $this->repair_card=new Sav_Repair_Card_SQL();
        
        
    }

    private function delete_draft()
    {
        /*  global $cn;
          $cn->exec('delete from service_after_sale.sav_repair_card where repair_number is null
          and tech_creation_date < now + interval "4 hours"'); */
    }

    public function display_list($p_where)
    {
        global $cn;
        
        $listing=$cn->get_array("SELECT id, 
             f_id_customer, 
             fd1.ad_value as name,
             fd2.ad_value as customer_qcode,
             to_char(date_reception,'DD.MM.YY') as str_date_reception, 
             date_start, 
             date_end, garantie, 
             substr(description_failure,1,40) as short_description, 
             jr_id, 
             tech_creation_date, 
             repair_number,
             garantie,
             card_status
             FROM service_after_sale.sav_repair_card as src
             join fiche_detail as fd1 on (fd1.f_id=src.f_id_customer and fd1.ad_id=1)
             join fiche_detail as fd2 on (fd2.f_id=src.f_id_customer and fd2.ad_id=23)
             
             $p_where
             
            order by date_reception desc,id desc
        ");
        include 'template/sas_display_list.php';
    }

    public function button_add()
    {
        $url="do.php".HtmlInput::request_to_string(array('ac', 'gDossier', 'sa', 'sb', 'plugin_code'));
        $url.="&amp;".http_build_query(array('sb'=>'detail'));
        $button=HtmlInput::button_anchor(_("Ajout"), $url, "", "", "smallbutton");
        echo $button;
    }
    public function button_search()
    {
        
    }
    public function display_detail()
    {
        global $cn;

        $plugin_code=HtmlInput::default_value_request('plugin_code', '');
        $ac=HtmlInput::default_value_request('ac', '');
        $gDossier=Dossier::id();
        
        /*
         * to be able to add item, we create an empty row as draft
         */
        if ($this->repair_card->id==-1)
        {
            $this->repair_card->card_status='D';
            $this->repair_card->date_reception=date('d.m.Y');
        }  
        /*  Customer          */
        $fiche_customer=new Fiche($cn, $this->repair_card->f_id_customer);

        /* crow received */
        $fiche_received=new Fiche($cn, $this->repair_card->f_id_personnel_received);

        /* crow done */
        $fiche_done=new Fiche($cn, $this->repair_card->f_id_personnel_done);

        /* goods */
        $fiche_good=new Fiche($cn, $this->repair_card->f_id_personnel_done);

        /* Date received */
        $date_received=new IDate('date_reception', $this->repair_card->date_reception);

        /* Date start */
        $date_start=new IDate('date_start', $this->repair_card->date_start);

        /* Date received */
        $date_end=new IDate('date_end', $this->repair_card->date_end);

        /* Status */
        $status=new ISelect('status_repair');
        $status->value=Sav_Repair_Card_SQL::$card_status_value;
        $status->selected=$this->repair_card->card_status;
        if ($this->repair_card->id==-1)
        {
              $status->disabled=1;
        } else {
            /* Show only others status */
            $a_status=Sav_Repair_Card_SQL::$card_status_value;
            $status->value=array_slice($a_status,1);
        }

        /* repair number -> only for update readonly !!!! */
        $repair_num=new IText('repair_number', $this->repair_card->repair_number);
        
        /* returned material */
        $material=new Fiche($cn,$this->repair_card->f_id_good);

        /* Spare parts */
        $spare=new Sav_Spare_Part();
        
        /* Workhour */
        $workhour=new Sav_Workhour();
        
        require 'template/sas_display_detail.php';
    }
    function set_status($p_status)
    {
        $this->repair_card->card_status=$p_status;
    }
    function set_date_reception($p_date)
    {
        if (isDate($p_date) == null )            throw new Exception(_('Date invalide'),DATEINVALIDE);
        $this->repair_card->date_reception=$p_date;
    }
    function set_date_start($p_date)
    {
        if ( $p_date=="") return;
        if (isDate($p_date) == null )            throw new Exception(_('Date invalide'),DATEINVALIDE);
        $this->repair_card->date_start=$p_date;
    }
    function set_date_end($p_date)
    {
        if ( $p_date=="") return;
        if (isDate($p_date) == null )            throw new Exception(_('Date invalide'),DATEINVALIDE);
        $this->repair_card->date_end=$p_date;
    }
    function set_garantie($p_string)
    {
        $this->repair_card->garantie=h($p_string);
    }
    function set_customer($p_cust_id,$p_via)
    {
        global $cn;
        $fiche = new Fiche($cn);
        if ( $p_via == 'qcode') 
        {
            $fiche->get_by_qcode($p_cust_id,FALSE);
        } 
        else if ($p_via == 'id')
        {
            $fiche->id=$p_cust_id;
        }
        else 
        {
            throw new Exception(_('Appel invalide Service_After_Sale '.__LINE__." via = [$p_via]"), APPEL_INVALIDE);
        }
            
        /* retrieve f_id thanks quick_code */
        $this->repair_card->f_id_customer=$fiche->id;
    }
    function set_material($p_good_id,$p_via)
    {
        global $cn;
        $fiche = new Fiche($cn);
        if ( $p_via == 'qcode') 
        {
            $fiche->get_by_qcode($p_good_id,FALSE);
        } 
        else if ($p_via == 'id')
        {
            $fiche->id=$p_good_id;
        }
        else 
        {
            throw new Exception(_('Appel invalide Service_After_Sale '.__LINE__." via = [$p_via]"), APPEL_INVALIDE);
        }
          
            
        /* retrieve f_id thanks quick_code */
        $this->repair_card->f_id_good=$fiche->id;
    }
    function set_description($p_string)
    {
        $this->repair_card->description_failure=h($p_string);
        
    }
    function set_card_id($p_integer)
    {
        $this->repair_card->id=$p_integer;
        $this->repair_card->load();
    }
    function save()
    {
        if ( $this->repair_card->id == -1 ) 
        {
            $this->repair_card->insert();
        } else  {
            $this->repair_card->update();
        }
    }
    function get_card_id()
    {
        return $this->repair_card->id;
    }
                

}

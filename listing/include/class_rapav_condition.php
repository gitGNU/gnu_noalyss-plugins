<?php

/*
 * Copyright (C) 2016 Dany De Bontridder <dany@alchimerys.be>
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
 * @brief
 *
 */
require_once NOALYSS_INCLUDE.'/class/class_fiche_attr.php';
require_once 'class_rapport_avance_sql.php';

class RAPAV_Condition
{
    private $data;
    static $a_operator = array(
        array("value"=>0,"label"=>'='),
        array("value"=>1,"label"=>'>='),
        array("value"=>2,"label"=>'<='),
        array("value"=>3,"label"=>'<>')
    );
    var $formula_type;
    function __construct($p_id=-1)
    {
        $this->data = new RAPAV_Condition_SQL($p_id);
    }
    function get_id()
    {
        return $this->data->id;
    }
    function load_by_listing_param_id($p_listing_param_id)
    {
        global $cn;
        
        $id=$cn->get_array('select id from rapport_advanced.listing_condition where lp_id=$1',
                array($p_listing_param_id));
        if ( $id == false ) return array();
        $a_condition=array();
        $nb_condition=count($id);
        
        for ($i=0;$i<$nb_condition;$i++)
        {
            $cond=new RAPAV_Condition($id[$i]['id']);
            $a_condition[$i]=clone $cond;
        }
        return $a_condition;
    }
    function set_listing_param($p_id)
    {
        $this->data->lp_id=$p_id;
    }
    function get_listing_param()
    {
        return $this->data->lp_id;
    }
    function set_operator($p_operator)
    {
        $this->data->c_operator=$p_operator;
    }
    function set_value($p_value)
    {
        $this->data->c_value=$p_value;
    }
    function get_value()
    {
        return $this->data->c_value;
    }
    function get_operator()
    {
        return $this->data->c_operator;
    }
    function get_formula_type()
    {
        global $cn;
        $type=$cn->get_value('select type_detail from rapport_advanced.listing_param where lp_id=$1 ',array($this->data->lp_id));
        return $type;
    }
    function verify()
    {
        global $cn;
        $type = $this->get_formula_type();
        if ($type=='ATTR')
        {
            $attribute_id=$cn->get_value("select ad_id from rapport_advanced.listing_param where lp_id=$1",
                    array($this->data->lp_id));
            $fiche=new Fiche_Attr($cn, $attribute_id);
            switch ($fiche->ad_type)
            {
                case "numeric":
                    if (isNumber($this->data->c_value)==0) return false;
                    break;
                case "date":
                    if (isDate($this->data->c_value) == null) return false;
                    break;
                default:
                    break;
            }
        } 
        return true;
    }

    function save()
    {
        global $cn;
        if ($this->verify() )         
        {
            $this->data->save();
            return true;
        }
        return false;
    }
    function html_select(){
        $select=new ISelect('c_operator');
        $select->value=self::$a_operator;
        $select->selected=$this->data->c_operator;
        echo $select->input();
    }
    function get_condition()
    {
        if ( $this->data->c_value != "") {
            $array=self::$a_operator;
            return h($array[$this->data->c_operator]['label']." ".$this->data->c_value);
        }
        return "";
    }
    static function display_condition($p_listing_id)
    {
        global $cn,$g_listing_home;
        $a_condition = $cn->get_array(
                "
                    select lc.id,lp_code , c_operator,c_value 
                    from
                    rapport_advanced.listing_condition as lc
                    join rapport_advanced.listing_param as lp on (lp.lp_id=lc.lp_id)
                    where 
                    l_id=$1
                    and 
                    coalesce(c_value,'') <> ''
                    "
                , array ($p_listing_id));
       if ( $a_condition == false ) return ;
       $nb_condition = count($a_condition);
       require $g_listing_home.'/template/display_condition.php';
    }
    /**
     * Delete the row which do not match the given condition. So we 
     * @global type $cn
     * @param type $p_listing_compute_id
     * @return type
     */
    function delete_by_filter($p_listing_compute_id){
        global $cn;
        $cn->start();
       // echo $p_listing_compute_id; return;
        $operator = self::$a_operator;
        for ($i=0;$i<count($operator);$i++)
        {
            // Keep first the text
            $cn->exec_sql(" delete 
                from rapport_advanced.listing_compute_fiche 
                where
                lc_id = $1 
                and lf_id in ( 
            select lf_id
            from rapport_advanced.listing_compute_detail as lcd
            join rapport_advanced.listing_condition as lc on (lcd.lp_id=lc.lp_id)
            where
            ld_value_text is not null
            and not ( upper(coalesce(ld_value_text,'')) ".$operator[$i]['label']." upper(c_value) )
            and c_operator = $2
            and lcd.lc_id = $1 
            )",array($p_listing_compute_id,$operator[$i]['value']));
            // Keep the amount
            $cn->exec_sql(" delete 
                from rapport_advanced.listing_compute_fiche 
                where
                lc_id = $1 
                and lf_id in ( 
            select lf_id
            from rapport_advanced.listing_compute_detail as lcd
            join rapport_advanced.listing_condition as lc on (lcd.lp_id=lc.lp_id)
            where
            ld_value_numeric is not null
            and not ( coalesce(ld_value_numeric,0) ".$operator[$i]['label']." c_value::float )
            and c_operator = $2
            and lcd.lc_id = $1 
            )",array($p_listing_compute_id,$operator[$i]['value']));
            // Keep the date
            $cn->exec_sql(" delete 
                from rapport_advanced.listing_compute_fiche 
                where
                lc_id = $1 
                and lf_id in ( 
            select lf_id
            from rapport_advanced.listing_compute_detail as lcd
            join rapport_advanced.listing_condition as lc on (lcd.lp_id=lc.lp_id)
            where
            ld_value_date is not null
            and c_value ~ E'^\\\\d{2}.\\\\d{2}.\\\\d{4}'
            and not ( ld_value_date ".$operator[$i]['label']." to_date(c_value,'DD.MM.YYYY') )
            and c_operator = $2
            and lcd.lc_id = $1 
            )",array($p_listing_compute_id,$operator[$i]['value']));
        }
        $cn->commit();
    }
}

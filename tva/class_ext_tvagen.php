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

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief
 */

require_once('tva_constant.php');
require_once('class_own.php');
/**
 *@brief transform a string into an arrau without empty element and  duplicate
 * the array is sorted
 *@param $p_string string containing a comma a separator
 *@return array
 *
 */
function get_array_nodup($p_string) {
  $array=explode(',',$p_string);
  sort($array);
  $array=array_unique($array);
  $result=array();
  foreach ($array as $val) {
    if ( $val == '') continue;
    $result[]=$val;
  }
  $r=join(',',$result);
  return $result;
}

class Ext_Tva_Gen
{
  function __construct($p_cn) {
    $this->db=$p_cn;
  }
  public function get_parameter($p_string) {
    if ( array_key_exists($p_string,$this->variable) ) {
      $idx=$this->variable[$p_string];
      return $this->$idx;
    }
    else
      throw new Exception (__FILE__.":".__LINE__.'Erreur attribut inexistant');
  }
  public function set_parameter($p_string,$p_value) {
    if ( array_key_exists($p_string,$this->variable) ) {
      $idx=$this->variable[$p_string];
      $this->$idx=$p_value;
    }
    else
      throw new Exception (__FILE__.":".__LINE__.'Erreur attribut inexistant');


  }
  public function get_info() {    return var_export(self::$variable,true);  }

  public function save() {
  /* please adapt */
    if (  $this->get_parameter("id") == 0 )
      $this->insert();
    else
      $this->update();
  }

  static function choose_periode($by_year=false) {
    require_once('class_iradio.php');
    $monthly=new IRadio('periodic');
    $monthly->value=1;

    // month
    $month=new ISelect('bymonth');
    $array=array ();
    for ($i=0;$i<12;$i++) {
      $array[$i]['value']=$i+1; $array[$i]['label']=sprintf('%02d',($i+1));
    }
    $month->value=$array;
    $monthly->selected=true;
    $str_monthly=$monthly->input();
    $str_month=$month->input();
    // year
    $year = new IText('year');
    $year->size=4;
    $str_year=$year->input();

    // Tri
    $quater=new ISelect('byquaterly');
    $array=array();
    for ($i=0;$i<4;$i++) {
      $array[$i]['value']=$i+1; $array[$i]['label']=sprintf('%02d',($i+1));
    }
    $quater->value=$array;
    $quaterly=new IRadio('periodic');
    $quaterly->value=2;
    $str_quaterly=$quaterly->input();
    $str_quater=$quater->input();

    $str_submit=HtmlInput::submit('decl',_('Afficher'));
    $str_hidden=HtmlInput::extension().dossier::hidden();
    if (isset($_REQUEST['sa']))
      $str_hidden.=HtmlInput::hidden('sa',$_REQUEST['sa']);
	$str_hidden.=HtmlInput::request_to_hidden(array('ac'));
    $str_byyear='';
    if ( $by_year == true ) {
      $yearly=new IRadio('periodic');
      $yearly->value=3;
      $str_byyear=$yearly->input();
    }
    ob_start();
    require_once('form_periode.php');
    $r=ob_get_contents();
    ob_end_clean();
    return $r;
  }

  function blank($p_year,$p_periode,$p_flag_quaterly) {
    // load parameter from myown
    $own=new Own($this->db);
    $this->set_parameter("name",$own->MY_NAME);
    $this->set_parameter("num_tva",$own->MY_TVA);
    $this->set_parameter('adress',$own->MY_STREET.",".$own->MY_NUMBER);
    $this->set_parameter('country',$own->MY_COUNTRY." ".$own->MY_CP." ".$own->MY_COMMUNE);
    $this->set_parameter('flag_periode',$p_flag_quaterly);
    $this->set_parameter('periode_dec',$p_periode);
    $this->set_parameter('exercice',$p_year);

    try {
      $this->verify() ;
    } catch ( Exception $e) {
      echo $e->getMessage();
      throw $e;
    }
    // by month
    if ( $p_flag_quaterly == 1) {
      // start periode = 01 to 31, $p_periode contains the month
      $per_start="01.".$p_periode.".".$p_year;
      $day=31;
      $per_end="31".".".$p_periode.".".$p_year;
      while ( checkdate($p_periode,$day,$p_year) == false && $day > 25) {
	$day--;
	$per_end=$day.".".$p_periode.".".$p_year;
      }
      if ($day < 28 ) { echo __FILE__.__LINE__." Erreur de date $day"; exit;}
    }

    if ( $p_flag_quaterly == 2 ) {
      // compute start periode
      $per_start=$GLOBALS['quaterly_limit'][$p_periode][0].".".$p_year;
      $per_end=$GLOBALS['quaterly_limit'][$p_periode][1].".".$p_year;

    }
    if ( $p_flag_quaterly == 3 ) {
      // compute start periode
      $per_start='01.01.'.$p_year;
      $per_end='31.12.'.$p_year;
    }

    $this->set_parameter('start_periode',$per_start);
    $this->set_parameter('end_periode',$per_end);

  }

  /**
   *@brief display the information about the company
   */
  function display_info(){
    $itva=new IText('num_tva',$this->num_tva);$str_tva=$itva->input();
    $iname=new IText('name',$this->tva_name); $str_name=$iname->input();
    $iadress=new IText('adress',$this->adress);$str_adress=$iadress->input();
    $icountry=new IText('country',$this->country);$str_country=$icountry->input();

    /* date */

    if (isset($this->date_decl)) { $idate=new IText('date',format_date($this->date_decl));$str_date=$idate->input();}

    /* periode */

    if ( isset($this->start_date) ){
      $str_start=format_date($this->start_date);
      $str_end=format_date($this->end_date);

    }
    $ianne=$this->exercice;
    ob_start();
    require_once('form_decl_info.php');
    $r=ob_get_contents();
    ob_end_clean();
    return $r;
  }

}

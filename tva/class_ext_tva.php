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
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief
 */
require_once('class_ext_tvagen.php');
require_once('class_tva_parameter.php');

class Ext_Tva extends Ext_Tva_Gen
{
  /* example private $variable=array("val1"=>1,"val2"=>"Seconde valeur","val3"=>0); */
  protected   $variable=array(
			   "d00"=>"d00",
			   "d01"=>"d01",
			   "d02"=>"d02",
			   "d03"=>"d03",
			   "d44"=>"d44",
			   "d45"=>"d45",
			   "d46"=>"d46",
			   "d47"=>"d47",
			   "d48"=>"d48",
			   "d49"=>"d49",
			   "d81"=>"d81",
			   "d82"=>"d82",
			   "d83"=>"d83",
			   "d84"=>"d84",
			   "d85"=>"d85",
			   "d86"=>"d86",
			   "d54"=>"d54",
			   "d55"=>"d55",
			   "d56"=>"d56",
			   "d57"=>"d57",
			   "d63"=>"d63",
			   "dxx"=>"dxx",
			   "d59"=>"d59",
			   "d62"=>"d62",
			   "d64"=>"d64",
			   "dyy"=>"dyy",
			   "d71"=>"d71",
			   "d82"=>"d82",
			   "d87"=>"d87",
			   "d88"=>"d88",
			   "d91"=>"d91",
			   "id"=>"tva_id",
			   "date_decl"=>"date_decl",
			   "start_periode"=>"start_periode",
			   "end_periode"=>"end_periode",
			   "xml_file"=>"xml_file",
			   "num_tva"=>"num_tva",
			   "name"=>"tva_name",
			   "adress"=>"adress",
			   "country"=>"country",
			   "flag_periode"=>"flag_periode",
			   "exercice"=>"exercice"
			   );

  /**
   *@brief retrieve * row thanks a condition
   */
   public function seek($cond,$p_array=null) 
   {
   /*
     $sql="select * from * where $cond";
     return $this->cn->get_array($cond,$p_array)
  */
   }
  public function insert() {
    if ( $this->verify() != 0 ) return;
    /*  please adapt
    $sql="insert into tva_rate (tva_label,tva_rate,tva_comment,tva_poste) ".
      " values ($1,$2,$3,$4)  returning tva_id";
    $this->tva_id=$this->cn->get_value(
		 $sql,
		 array($this->tva_label,
		       $this->tva_rate,
		       $this->tva_comment,
		       $this->tva_poste)
		 );
    */
  }

  public function update() {
    if ( $this->verify() != 0 ) return;
    /*   please adapt
    $sql="update tva_rate set tva_label=$1,tva_rate=$2,tva_comment=$3,tva_poste=$4 ".
      " where tva_id = $5";
    $res=$this->cn->exec_sql(
		 $sql,
		 array($this->tva_label,
		       $this->tva_rate,
		       $this->tva_comment,
		       $this->tva_poste,
		       $this->tva_id)
		 );
		 */
  }

  public function load() {

   $sql="select tva_label,tva_rate, tva_comment,tva_poste from tva_rate where tva_id=$1"; 
    /* please adapt
    $res=$this->cn->get_array(
		 $sql,
		 array($this->tva_id)
		 );
		 */
    if ( Database::num_row($res) == 0 ) return;
    foreach ($res as $idx=>$value) { $this->$idx=$value; }
  }
  public function delete() {
/*    $sql="delete from tva_rate where tva_id=$1"; 
    $res=$this->cn->exec_sql($sql,array($this->tva_id));
*/
  }

  function verify() {
    /**
     *@todo
     * MY_NAME can not be empty
     * MY_TVA contains BE and has 10 digits
     * this->adress and $this->country can not be empty
     */
  }
  function blank($p_year,$p_periode,$p_flag_quaterly) {
    // load parameter from myown
    $own=new Own($this->db);
    $this->set_parameter("name",$own->MY_NAME);
    $this->set_parameter("num_tva",$own->MY_TVA);
    $this->set_parameter('adress',$own->MY_STREET.",".$own->MY_NUMBER);
    $this->set_parameter('country',$own->MY_COUNTRY." ".$own->MY_CP." ".$own->MY_COMMUNE);
    $this->set_parameter('flag_periode',$p_flag_quaterly);
    $this->set_parameter('exercice',$p_year);
    try {
      $this->verify() ;
    } catch (Exception $e) {
      echo $e->getMessage();
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
    $this->set_parameter('start_periode',$per_start);
    $this->set_parameter('end_periode',$per_end);
    // compute amount from periode
    var_dump($per_start." ".$per_end);

    
  }
  function display_info(){
    $itva=new IText('num_tva',$this->num_tva);$str_tva=$itva->input();
    $iname=new IText('name',$this->tva_name); $str_name=$iname->input();
    $iadress=new IText('adress',$this->adress);$str_adress=$iadress->input();
    $icountry=new IText('country',$this->country);$str_country=$icountry->input();
    ob_start();
    require_once('form_decl_info.php');
    $r=ob_get_contents();
    ob_clean();
    return $r;
  }
  function compute() {
    // check that this exercice exist
    $exist=$this->db->get_value('select count(*) from jrn join parm_periode on (p_id=jr_tech_per) where p_exercice=$1',array($this->exercice));
    if ( $exist==0 ) { alert(_("Cette exercice comptable n'est pas dans ce dossier")); exit;}

    // set 0 for all
    $keys=array_keys($this->variable);
    for ($i = 0;$i < count($this->variable);$i++) { 
      $idx=$keys[$i];
      $this->$idx=0;
      if ( $idx=='d91') break;
    }

    $ctva=new Tva_Parameter($this->db);
    // Compute all the values now
    //d00 ?????

    //d01
    $ctva->set_parameter('code','GRIL01');
    $ctva->load();
    $poste=$ctva->get_parameter('value');
    //!!! From the first day of the exercice until end_date
    /**
     *@todo create new function 
     * - get_amount_account
     * - get_amount_account_involved
     */
    $this->set_parameter('d01',$solde['solde']);

    //d02
    $ctva->set_parameter('code','GRIL02');
    $ctva->load();
    $poste=$ctva->get_parameter('value');
    $oAccount=new Acc_Account_Ledger($this->db,$poste);
    $solde=$oAccount->get_solde_detail($cond);
    $this->set_parameter('d02',$solde['solde']);

    //d03
    $ctva->set_parameter('code','GRIL03');
    $ctva->load();
    $poste=$ctva->get_parameter('value');
    $oAccount=new Acc_Account_Ledger($this->db,$poste);
    $solde=$oAccount->get_solde_detail($cond);
    $this->set_parameter('d03',$solde['solde']);

  }
  function display_declaration_amount() {
    $itext_00=new INum('val[]',$this->get_parameter('d00')); $str_00=$itext_00->input().HtmlInput::hidden('code[]','d00');
    $itext_01=new INum('val[]',$this->get_parameter('d01')); $str_01=$itext_01->input().HtmlInput::hidden('code[]','d01');
    $itext_02=new INum('val[]',$this->get_parameter('d02')); $str_02=$itext_02->input().HtmlInput::hidden('code[]','d02');
    $itext_03=new INum('val[]',$this->get_parameter('d03')); $str_03=$itext_03->input().HtmlInput::hidden('code[]','d03');
    $itext_44=new INum('val[]',$this->get_parameter('d44')); $str_44=$itext_44->input().HtmlInput::hidden('code[]','d44');
    $itext_45=new INum('val[]',$this->get_parameter('d45')); $str_45=$itext_45->input().HtmlInput::hidden('code[]','d45');
    $itext_46=new INum('val[]',$this->get_parameter('d46')); $str_46=$itext_46->input().HtmlInput::hidden('code[]','d46');
    $itext_47=new INum('val[]',$this->get_parameter('d47')); $str_47=$itext_47->input().HtmlInput::hidden('code[]','d47');
    $itext_48=new INum('val[]',$this->get_parameter('d48')); $str_48=$itext_48->input().HtmlInput::hidden('code[]','d48');
    $itext_49=new INum('val[]',$this->get_parameter('d49')); $str_49=$itext_49->input().HtmlInput::hidden('code[]','d49');
    $itext_81=new INum('val[]',$this->get_parameter('d81')); $str_81=$itext_81->input().HtmlInput::hidden('code[]','d81');
    $itext_82=new INum('val[]',$this->get_parameter('d82')); $str_82=$itext_82->input().HtmlInput::hidden('code[]','d82');
    $itext_83=new INum('val[]',$this->get_parameter('d83')); $str_83=$itext_83->input().HtmlInput::hidden('code[]','d83');
    $itext_84=new INum('val[]',$this->get_parameter('d84')); $str_84=$itext_84->input().HtmlInput::hidden('code[]','d84');
    $itext_85=new INum('val[]',$this->get_parameter('d85')); $str_85=$itext_85->input().HtmlInput::hidden('code[]','d85');
    $itext_86=new INum('val[]',$this->get_parameter('d86')); $str_86=$itext_86->input().HtmlInput::hidden('code[]','d86');
    $itext_87=new INum('val[]',$this->get_parameter('d87')); $str_87=$itext_87->input().HtmlInput::hidden('code[]','d87');
    $itext_88=new INum('val[]',$this->get_parameter('d88')); $str_88=$itext_88->input().HtmlInput::hidden('code[]','d88');

    $itext_54=new INum('val[]',$this->get_parameter('d54')); $str_54=$itext_54->input().HtmlInput::hidden('code[]','d54');
    $itext_55=new INum('val[]',$this->get_parameter('d55')); $str_55=$itext_55->input().HtmlInput::hidden('code[]','d55');
    $itext_56=new INum('val[]',$this->get_parameter('d56')); $str_56=$itext_56->input().HtmlInput::hidden('code[]','d56');
    $itext_57=new INum('val[]',$this->get_parameter('d57')); $str_57=$itext_57->input().HtmlInput::hidden('code[]','d57');
    $itext_63=new INum('val[]',$this->get_parameter('d63')); $str_63=$itext_63->input().HtmlInput::hidden('code[]','d63');
    $itext_xx=new INum('val[]',$this->get_parameter('dxx')); $str_xx=$itext_xx->input().HtmlInput::hidden('code[]','dxx');
    $itext_59=new INum('val[]',$this->get_parameter('d59')); $str_59=$itext_59->input().HtmlInput::hidden('code[]','d59');
    $itext_62=new INum('val[]',$this->get_parameter('d62')); $str_62=$itext_62->input().HtmlInput::hidden('code[]','d62');
    $itext_64=new INum('val[]',$this->get_parameter('d64')); $str_64=$itext_64->input().HtmlInput::hidden('code[]','d64');
    $itext_yy=new INum('val[]',$this->get_parameter('dyy')); $str_yy=$itext_yy->input().HtmlInput::hidden('code[]','dyy');
    $itext_71=new INum('val[]',$this->get_parameter('d71')); $str_71=$itext_71->input().HtmlInput::hidden('code[]','d71');
    $itext_91=new INum('val[]',$this->get_parameter('d91')); $str_91=$itext_91->input().HtmlInput::hidden('code[]','d91');


    ob_start();
    require_once('form_decl.php');
    $r=ob_get_contents();
    ob_clean();
    return $r;
    
  }
  }
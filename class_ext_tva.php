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
			   "d61"=>"d61",
			   "d62"=>"d62",
			   "d64"=>"d64",
			   "dyy"=>"dyy",
			   "d71"=>"d71",
			   "d72"=>"d72",
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
   }
  public function insert() {
    if ( $this->verify() != 0 ) return;

  }

  public function update() {
    if ( $this->verify() != 0 ) return;

  }

  public function load() {

   $sql="select tva_label,tva_rate, tva_comment,tva_poste from tva_rate where tva_id=$1"; 
   /* please adapt */
    $res=$this->cn->get_array(
		 $sql,
		 array($this->tva_id)
		 );
		 
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

    // set default value 0 for all
    $keys=array_keys($this->variable);
    for ($i = 0;$i < count($this->variable);$i++) { 
      $idx=$keys[$i];
      $this->$idx=0;
      if ( $idx=='d91') break;
    }

    $ctva=new Tva_Parameter($this->db);
    $array=array('00','01','02','03','44','45','46','47','48','49');
    for ($e=0;$e<count($array);$e++) {
      // Compute frame 2
      $amount=$this->get_amount('GRIL'.$array[$e],'out');
      $this->set_parameter('d'.$array[$e],$amount);
    }
    $array=array('81','82','83','84','85','86','87','88');
    for ($e=0;$e<count($array);$e++) {
      // Compute frame 3
      $amount=$this->get_amount('GRIL'.$array[$e],'in');
      $this->set_parameter('d'.$array[$e],$amount);
    }

    // Compute GRIL54
    $array=array('01','02','03');
    $rposte='';$rrelated='';
    for ($e=0;$e<count($array);$e++) {
      // Compute frame 3
      $ctva=new Tva_Parameter($this->db);
      $ctva->set_parameter('code','GRIL'.$array[$e]);
      $ctva->load();
      $poste=$ctva->get_parameter('value');
      $related=$ctva->get_parameter('account');
      $rposte.=$related;
      $rrelated.=$poste;
    }
    $amount=$this->get_poste($poste,$related,'out');
    $this->set_parameter('d54',$amount);

    // Compute GRIL55
    $array=array('86','88');
    $rposte='';$rrelated='';
    for ($e=0;$e<count($array);$e++) {
      // Compute frame 3
      $ctva=new Tva_Parameter($this->db);
      $ctva->set_parameter('code','GRIL'.$array[$e]);
      $ctva->load();
      $poste=$ctva->get_parameter('value');
      $related=$ctva->get_parameter('account');
      $rposte.=$related;
      $rrelated.=$poste;
    }
    $amount=$this->get_poste($poste,$related,'out');
    $this->set_parameter('d55',$amount);
    /**
     *@todo
     * GRIL57 - GRIL61 - GRIL63
     */

    //GRILXX
    $amount=$this->d54+$this->d55+$this->d56+$this->d57+$this->d61+$this->d63;
    $this->set_parameter('dxx',$amount);


    // Frame V 
    //gril59
    $array=array('81','82','83','84','85','86','87','88');
    $rposte='';$rrelated='';
    for ($e=0;$e<count($array);$e++) {
      // Compute frame 3
      $ctva=new Tva_Parameter($this->db);
      $ctva->set_parameter('code','GRIL'.$array[$e]);
      $ctva->load();
      $poste=$ctva->get_parameter('value');
      $related=$ctva->get_parameter('account');
      $rposte.=$related;
      $rrelated.=$poste;
    }
    $amount=$this->get_poste($poste,$related,'out');
    $this->set_parameter('d59',$amount);
    /**
     *@todo indiquez que GRIL62 n'est pas calculé automatiquement
     */
    //gril64
    $array=array('81','82','83','84','85','86','87','88');
    $rposte='';$rrelated='';
    for ($e=0;$e<count($array);$e++) {
      // Compute frame 3
      $ctva=new Tva_Parameter($this->db);
      $ctva->set_parameter('code','GRIL'.$array[$e]);
      $ctva->load();
      $poste=$ctva->get_parameter('value');
      $related=$ctva->get_parameter('account');
      $rposte.=$related;
      $rrelated.=$poste;
    }
    $amount=$this->get_poste($poste,$related,'out');
    $this->set_parameter('d64',$amount);

    // GRILYY
    $this->dyy=$this->d59+$this->d62+$this->d64;
    
    //Fram VI
    if ( $this->dxx > $this->dyy ) $this->d71=$this->dxx-$this->dyy;
    if ( $this->dxx < $this->dyy ) $this->d72=$this->dyy-$this->dxx;

   }
   /**
    *@brief get the amount of operations related to the accounting linked
    *       to $p_code, in the range of start_periode and end_periode
    *@param $p_code is the code is tva_belge.parameter.pcode
    *@param $p_dir direction of the operation (in for sales, out for purchases)
    *       
    *@return the amount
    */
   function get_amount($p_code,$p_dir) {
     $result=0;

     // load the code and find the related acccounting
     $ctva=new Tva_Parameter($this->db);
     $ctva->set_parameter('code',$p_code);

     // check parameters
     if ( $ctva->load() == -1 )
       throw new Exception (_("p_code $p_code non trouvé"));

     if ( $p_dir != 'in' && $p_dir != 'out') 
       throw new Exception (_("p_dir $p_dir est incorrect"));

     // find all the operation using the accounting and
     // compute the total of in of out (6 or 7) with this accounting
     $related=$ctva->get_parameter('value');
     $poste=$ctva->get_parameter('account');
     $result=$this->get_poste($poste,$related,$p_dir);
     return $result;
   }
   /**
    *@brief split poste and amount and call get_amount_account
    *@param $poste accounting for which the amount is asked
    *@param $related accounting involved 
    *@param $p_dir in or out
    *@return total amount for account $poste in the operations which involved $related
    *@see get_amount
    */
   function get_poste($poste,$related,$p_dir) {
     $result=0;
     if ( strpos($poste,',') != 0 ) {
       $aPoste=split(',',$poste) ;
	 for ($i=0;$i<count($aPoste);$i++){
	   if ( strpos($related,',') != 0 ) {
	     $aRelated=split(',',$related);
	     for($j=0;$j<count($aRelated);$j++) {
	       $result+=$this->get_amount_account($aPoste[$i],$aRelated[$j],$p_dir);
	     }
	   } else
	     $result+=$this->get_amount_account($aPoste[$i],$related,$p_dir);
	 }
     } else {
       if ( strpos($related,',') != 0 ) {
	 $aRelated=split(',',$related);
	 for($j=0;$j<count($aRelated);$j++) {
	   $result+=$this->get_amount_account($poste,$aRelated[$j],$p_dir);
	 } 
       }else {
	 $result=$this->get_amount_account($poste,$related,$p_dir);
       }
     }
     if ($result < 0 ) alert(_('Montant négatif détecté'));
     return $result;
   }
   /**
    *@brief return the amount for an account 
    *@see get_amount
    *@param $p_poste accounting
    *@param $related is '6%' or '
    *@param $p_dir is out or in for purchases or sales
    *@return amount of this accounting
    */
   function get_amount_account($p_poste,$related,$p_dir) {

     $sql="
select coalesce(sum(amount_deb),0) as sum_deb, 
coalesce (sum(amount_cred),0) as sum_cred from (
select 
case when j_debit is true  then j_montant else 0 end  as amount_deb,
case when j_debit is false then j_montant else 0 end  as amount_cred
from jrnx 
where
j_grpt in ( select j_grpt from jrnx 
where 
j_poste::text like $1
)
and ( 
j_poste::text like $2
 and (
j_date >= to_date($3,'DD.MM.YYYY') and j_date <= to_date($4,'DD.MM.YYYY')))
) as compute_amount_side
";
     $res=$this->db->get_array($sql,array($related,
					  $p_poste,
					  $this->start_periode,
					  $this->end_periode
					  )
			       );
     $result=$res[0]['sum_deb']-$res[0]['sum_cred'];
     if ( $p_dir == 'out') 
       $result=(-1)*$result;
     return $result;
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
     $itext_72=new INum('val[]',$this->get_parameter('d72')); $str_72=$itext_72->input().HtmlInput::hidden('code[]','d72');
     $itext_91=new INum('val[]',$this->get_parameter('d91')); $str_91=$itext_91->input().HtmlInput::hidden('code[]','d91');


     ob_start();
     require_once('form_decl.php');
     $r=ob_get_contents();
     ob_clean();
     return $r;

   }

}

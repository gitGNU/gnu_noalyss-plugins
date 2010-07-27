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
require_once('class_tva_amount.php');
require_once('class_html_input.php');
class Ext_Tva extends Ext_Tva_Gen
{
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
			   "id"=>"da_id",
			   "date_decl"=>"date_decl",
			   "start_periode"=>"start_periode",
			   "end_periode"=>"end_periode",
			   "xml_file"=>"xml_file",
			   "num_tva"=>"num_tva",
			   "name"=>"tva_name",
			   "adress"=>"adress",
			   "country"=>"country",
			   "flag_periode"=>"flag_periode",
			   "exercice"=>"exercice",
			   "periode_dec"=>"periode_dec"
			   );

  /**
   *@brief retrieve * row thanks a condition
   */
   public function seek($cond,$p_array=null) 
   {
   }
   public function from_array($p_array) {
     $val=$p_array['val'];
     $code=$p_array['tvacode'];
     for ($i=0;$i<count($val);$i++) {
       $this->$code[$i]=$val[$i];
     }
     $this->start_periode=$p_array['start_periode'];
     $this->end_periode=$p_array['end_periode'];
     $this->flag_periode=$p_array['flag_periode'];
     $this->tva_name=$p_array['name'];
     $this->num_tva=$p_array['num_tva'];
     $this->adress=$p_array['adress'];
     $this->country=$p_array['country'];
     $this->periode_dec=$p_array['periode_dec'];
     $this->exercice=$p_array['exercice'];

   }
  public function insert() {

    if ( $this->verify() != 0 ) return;
    $sql="INSERT INTO tva_belge.declaration_amount(
             d00, d01, d02, d03, d44, d45, d46, d47, d48, d49, d81, 
            d82, d83, d84, d85, d86, d87, d88, d54, d55, d56, d57, d61, d63, 
            dxx, d59, d62, d64, dyy, d71, d72, d91, start_date, end_date, 
             periodicity,tva_name,num_tva,adress,country,periode_dec,exercice)
    VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, 
            $13, $14, $15, $16, $17, $18, $19, $20, $21, $22, $23, $24, $25, 
            $26, $27, $28, $29, $30, $31, $32, to_date($33,'DD.MM.YYYY'), to_date($34,'DD.MM.YYYY'), $35,$36,
            $37,$38,$39,$40,$41) 
             returning da_id;";
      $this->da_id=$this->db->get_value($sql,
				     array($this->d00, /* 1 */
					   $this->d01, /* 2 */
					   $this->d02, /* 3 */
					   $this->d03, /* 4 */
					   $this->d44, /* 5 */
					   $this->d45, /* 6 */
					   $this->d46, /* 7 */
					   $this->d47, /* 8 */
					   $this->d48, /* 9 */
					   $this->d49, /* 10 */
					   $this->d81, /* 11 */
					   $this->d82, /* 12 */
					   $this->d83, /* 13 */
					   $this->d84, /* 14 */
					   $this->d85, /* 15 */
					   $this->d86, /* 16 */
					   $this->d87, /* 17 */
					   $this->d88, /* 18 */
					   $this->d54, /* 19 */
					   $this->d55, /* 20 */
					   $this->d56, /* 21 */
					   $this->d57, /* 22 */
					   $this->d61, /* 23 */
					   $this->d63, /* 24 */
					   $this->dxx, /* 25 */
					   $this->d59, /* 26 */
					   $this->d62, /* 27 */
					   $this->d64, /* 28 */
					   $this->dyy, /* 29 */
					   $this->d71, /* 30 */
					   $this->d72, /* 31 */
					   $this->d91, /* 32 */
					   $this->start_periode, /* 33 */
					   $this->end_periode, /* 34 */
					   $this->flag_periode, /* 35 */
					   $this->tva_name,	/* 36 */
					   $this->num_tva,	/* 37 */
					   $this->adress,	/* 38 */
					   $this->country,	/* 39 */
					   $this->periode_dec,	/* 40 */
					   $this->exercice,	/* 41 */

					   ));
  }

  public function update() {
    if ( $this->verify() != 0 ) return;

  }

  public function load() {

   $sql="select * from tva_belge.declaration_amount where da_id=$1"; 

   $res=$this->db->get_array(
			    $sql,
			    array($this->da_id)
			    );
   if ( $this->db->count() == 0 ) return;
   foreach ($res[0] as $idx=>$value) { $this->$idx=$value; }
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
    return 0;
  }

  /**
   *@brief compute the amount
   */
  function compute() {
    // check that this exercice exist
    $exist=$this->db->get_value('select count(*) from jrn join parm_periode on (p_id=jr_tech_per) where p_exercice=$1',array($this->exercice));
    if ( $exist==0 ) { alert(_("Cette exercice comptable n'est pas dans ce dossier")); throw  new Exception('INVALYEAR',1 ) ;}

    // set default value 0 for all
    $keys=array_keys($this->variable);
    for ($i = 0;$i < count($this->variable);$i++) { 
      $idx=$keys[$i];
      $this->$idx=0;
      if ( $idx=='d91') break;
    }

    $ctva=new Tva_Parameter($this->db);
    /**
     *@todo on ne peut pas calculer de cette façon à cause des opérations qui comprennent plusieurs taux de tva diff.
     *il faut aller chercher les montants dans les tables quant_*
     */
    $array=array('00','01','02','03','44','45','46','47','48','49');
    for ($e=0;$e<count($array);$e++) {
      // Compute frame 2
      $oTva=new Tva_amount($this->db,'out',$this->start_periode,$this->end_periode);
      $oTva->set_parameter('grid','GRIL'.$array[$e]);
      $amount=$oTva->amount_operation();
      $this->set_parameter('d'.$array[$e],$amount);
    }

    $array=array('81','82','83','84','85','86','87','88');
    for ($e=0;$e<count($array);$e++) {
      // Compute frame 3
      $oTva=new Tva_amount($this->db,'in',$this->start_periode,$this->end_periode);
      $oTva->set_parameter('grid','GRIL'.$array[$e]);
      $amount=$oTva->amount_operation();
      $this->set_parameter('d'.$array[$e],$amount);
     
    }
    //Frame IV
    $array=array('54','55','56','57','61','63');
    for ($e=0;$e<count($array);$e++) {
      $oTva=new Tva_amount($this->db,'out',$this->start_periode,$this->end_periode);
      $oTva->set_parameter('grid','GRIL'.$array[$e]);
      $amount=$oTva->amount_vat();
      $this->set_parameter('d'.$array[$e],$amount);

    }

   
    $array=array('59','62','64');
    for ($e=0;$e<count($array);$e++) {
      $oTva=new Tva_amount($this->db,'in',$this->start_periode,$this->end_periode);
      $oTva->set_parameter('grid','GRIL'.$array[$e]);
      $amount=$oTva->amount_vat();
      $this->set_parameter('d'.$array[$e],$amount);

    }
    // for intracom, we compute a false VAT, have to paid it and deduce
    $this->d55=round($this->d86*0.21+$this->d88*0.21,2);
    $this->d59+=$this->d55;
    /**
     *@todo
     * GRIL56 - GRIL57 - GRIL61 - GRIL63
     */

    //GRILXX
    $amount=$this->d54+$this->d55+$this->d56+$this->d57+$this->d61+$this->d63;
    $this->set_parameter('dxx',$amount);


    // GRILYY
    $this->dyy=round($this->d59+$this->d62+$this->d64,2);

    
    //Fram VI
    if ( $this->dxx > $this->dyy ) $this->d71=$this->dxx-$this->dyy;
    if ( $this->dxx < $this->dyy ) $this->d72=$this->dyy-$this->dxx;

   }
  /**
   *@brief get into the table quant_purchase or quant_sold the amount
   * of VAT
   *@param
   *@param
   *@return
   *@see
   */

   function display_declaration_amount() {
     $itext_00=new INum('val[]',$this->get_parameter('d00')); $str_00=$itext_00->input().HtmlInput::hidden('tvacode[]','d00');
     $itext_01=new INum('val[]',$this->get_parameter('d01')); $str_01=$itext_01->input().HtmlInput::hidden('tvacode[]','d01');
     $itext_02=new INum('val[]',$this->get_parameter('d02')); $str_02=$itext_02->input().HtmlInput::hidden('tvacode[]','d02');
     $itext_03=new INum('val[]',$this->get_parameter('d03')); $str_03=$itext_03->input().HtmlInput::hidden('tvacode[]','d03');
     $itext_44=new INum('val[]',$this->get_parameter('d44')); $str_44=$itext_44->input().HtmlInput::hidden('tvacode[]','d44');
     $itext_45=new INum('val[]',$this->get_parameter('d45')); $str_45=$itext_45->input().HtmlInput::hidden('tvacode[]','d45');
     $itext_46=new INum('val[]',$this->get_parameter('d46')); $str_46=$itext_46->input().HtmlInput::hidden('tvacode[]','d46');
     $itext_47=new INum('val[]',$this->get_parameter('d47')); $str_47=$itext_47->input().HtmlInput::hidden('tvacode[]','d47');
     $itext_48=new INum('val[]',$this->get_parameter('d48')); $str_48=$itext_48->input().HtmlInput::hidden('tvacode[]','d48');
     $itext_49=new INum('val[]',$this->get_parameter('d49')); $str_49=$itext_49->input().HtmlInput::hidden('tvacode[]','d49');
     $itext_81=new INum('val[]',$this->get_parameter('d81')); $str_81=$itext_81->input().HtmlInput::hidden('tvacode[]','d81');
     $itext_82=new INum('val[]',$this->get_parameter('d82')); $str_82=$itext_82->input().HtmlInput::hidden('tvacode[]','d82');
     $itext_83=new INum('val[]',$this->get_parameter('d83')); $str_83=$itext_83->input().HtmlInput::hidden('tvacode[]','d83');
     $itext_84=new INum('val[]',$this->get_parameter('d84')); $str_84=$itext_84->input().HtmlInput::hidden('tvacode[]','d84');
     $itext_85=new INum('val[]',$this->get_parameter('d85')); $str_85=$itext_85->input().HtmlInput::hidden('tvacode[]','d85');
     $itext_86=new INum('val[]',$this->get_parameter('d86')); $str_86=$itext_86->input().HtmlInput::hidden('tvacode[]','d86');
     $itext_87=new INum('val[]',$this->get_parameter('d87')); $str_87=$itext_87->input().HtmlInput::hidden('tvacode[]','d87');
     $itext_88=new INum('val[]',$this->get_parameter('d88')); $str_88=$itext_88->input().HtmlInput::hidden('tvacode[]','d88');

     $itext_54=new INum('val[]',$this->get_parameter('d54')); $str_54=$itext_54->input().HtmlInput::hidden('tvacode[]','d54');
     $itext_55=new INum('val[]',$this->get_parameter('d55')); $str_55=$itext_55->input().HtmlInput::hidden('tvacode[]','d55');
     $itext_56=new INum('val[]',$this->get_parameter('d56')); $str_56=$itext_56->input().HtmlInput::hidden('tvacode[]','d56');
     $itext_57=new INum('val[]',$this->get_parameter('d57')); $str_57=$itext_57->input().HtmlInput::hidden('tvacode[]','d57');
     $itext_63=new INum('val[]',$this->get_parameter('d63')); $str_63=$itext_63->input().HtmlInput::hidden('tvacode[]','d63');
     $itext_61=new INum('val[]',$this->get_parameter('d61')); $str_61=$itext_61->input().HtmlInput::hidden('tvacode[]','d61');
     $itext_xx=new INum('val[]',$this->get_parameter('dxx')); $str_xx=$itext_xx->input().HtmlInput::hidden('tvacode[]','dxx');
     $itext_59=new INum('val[]',$this->get_parameter('d59')); $str_59=$itext_59->input().HtmlInput::hidden('tvacode[]','d59');
     $itext_62=new INum('val[]',$this->get_parameter('d62')); $str_62=$itext_62->input().HtmlInput::hidden('tvacode[]','d62');
     $itext_64=new INum('val[]',$this->get_parameter('d64')); $str_64=$itext_64->input().HtmlInput::hidden('tvacode[]','d64');
     $itext_yy=new INum('val[]',$this->get_parameter('dyy')); $str_yy=$itext_yy->input().HtmlInput::hidden('tvacode[]','dyy');
     $itext_71=new INum('val[]',$this->get_parameter('d71')); $str_71=$itext_71->input().HtmlInput::hidden('tvacode[]','d71');
     $itext_72=new INum('val[]',$this->get_parameter('d72')); $str_72=$itext_72->input().HtmlInput::hidden('tvacode[]','d72');
     $itext_91=new INum('val[]',$this->get_parameter('d91')); $str_91=$itext_91->input().HtmlInput::hidden('tvacode[]','d91');


     ob_start();
     require_once('form_decl.php');
     $r=ob_get_contents();
     ob_clean();
     $r.=HtmlInput::hidden('periode_dec',$this->periode_dec);
     return $r;

   }
   function menu() {
     $r='';
     $js_record=sprintf("onclick=\"record_writing('%s',%d,%d)\"",
			$_REQUEST['plugin_code'],
			dossier::id(),
			$this->da_id);
     $array=array (
		   array("javascript:void(0)",_("Ecriture comptable"),_("Création de l'écriture comptable"),1,$js_record),
		   array("javascript:void(0)",_("Générer fichier"),_("Création du fichier xml"),2)
		   );
     $r.=ShowItem($array,'V',"mtitle","mtitle");
     return $r;

   }
   function propose_form() {
     $r='';

     /* take all the vat code */
     $array=$this->db->get_array("select tva_poste from tva_rate");
     if ( empty($array)) return 'aucun compte pour la tva';
     $max=count($array);
     $periode=new Periode($this->db);
     $per=$periode->limit_year($this->exercice);
     $periode->p_id=$per['start'];

     $first_day=$periode->first_day();
     $idx=0;
     $amount_vat=0;
     $r.='<table class="result">';
     for ( $i=0;$i<$max;$i++){
       /* for each rate explode the accounting */
       list($deb,$cred)=explode(',',$array[$i]['tva_poste']);

       /* get saldo for DEBIT*/
       $saldo=new Acc_Account_Ledger($this->db,$deb);
       /* get label */
       $lib=$this->db->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',array($deb));
				
       $cond=sprintf(" j_date >=to_date('%s','DD.MM.YYYY') and j_date <= '%s'",
		     $first_day,
		     $this->end_date);
       $result=$saldo->get_solde_detail($cond);
       if ( $result['solde']==0) continue;

       $account=new IText('account['.$idx.']');
       $account->value=$deb;
       $amount=new INum('amount['.$idx.']');
       $amount->value=abs($result['solde']);

       $ICheckBox=new ICheckBox('deb['.$idx.']');
       if ( $result['debit'] < $result['credit'] ) {$amount_vat-=$result['solde'];	 $ICheckBox->selected=true;} 
       else {$amount_vat+=$result['solde'];	 $ICheckBox->selected=false;
}
       $idx++;
       /* display row */
       $r.=tr(td($account->input()).td($lib).td($amount->input()).td($ICheckBox->input()));
       
       /* get saldo for CREDIT*/
       $saldo=new Acc_Account_Ledger($this->db,$cred);
       /* get label */
       $lib=$this->db->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',array($cred));
				
       $cond=sprintf(" j_date >=to_date('%s','DD.MM.YYYY') and j_date <= '%s'",
		     $first_day,
		     $this->end_date);
       $result=$saldo->get_solde_detail($cond);
       if ( $result['solde']==0) continue;

       $account=new IText('account['.$idx.']');
       $account->value=$cred;
       $amount=new INum('amount['.$idx.']');
       $amount->value=abs($result['solde']);

       $ICheckBox=new ICheckBox('deb['.$idx.']');
       if ( $result['debit'] < $result['credit'] ) {$amount_vat-=$result['solde'];	 $ICheckBox->selected=true;}
       else {	 $ICheckBox->selected=false;
	 $amount_vat+=$result['solde'];}
       
       /* display row */
       $r.=tr(td($account->input()).td($lib).td($amount->input()).td($ICheckBox->input()));
       $idx++;
       
     }
     /* ATVA */
     $atva=$this->db->get_value("select paccount from tva_belge.parameter where pcode='ATVA'");
     if ( $atva != ''  ) {
       /* get saldo */
       $saldo=new Acc_Account_Ledger($this->db,$atva);
       /* get label */
       $lib=$this->db->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',array($atva));
				
       $cond=sprintf(" j_date >=to_date('%s','DD.MM.YYYY') and j_date <= '%s'",
		     $first_day,
		     $this->end_date);
       $result=$saldo->get_solde_detail($cond);
       $ICheckBox=new ICheckBox('atva_ic');
       $account=new IText('atva');
       $account->value=$atva;
       $amount=new INum('atva_amount');
       $amount->value=abs($result['solde']);

       if ( $result['debit'] < $result['credit'] ) {$amount_vat-=$result['solde'];	 $ICheckBox->selected=true;}
       else {       
	 $ICheckBox->selected=false;
	 $amount_vat+=$result['solde'];
       }
       /* display row */
       if ( $result['solde'] != 0)       $r.=tr(td($account->input()).td('Avance TVA').td($amount->input()).td($ICheckBox->input()));

     }
     /* creance sur tva*/
     /* CRTVA */
     $crtva=$this->db->get_value("select paccount from tva_belge.parameter where pcode='CRTVA'");
     if ( $crtva != ''  ) {
       /* get saldo */
       $saldo=new Acc_Account_Ledger($this->db,$crtva);
       /* get label */
       $lib=$this->db->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',array($crtva));
				
       $cond=sprintf(" j_date >=to_date('%s','DD.MM.YYYY') and j_date <= '%s'",
		     $first_day,
		     $this->end_date);
       $result=$saldo->get_solde_detail($cond);
       $ICheckBox=new ICheckBox('crtva_ic');
       $account=new IText('crtva');
       $account->value=$crtva;
       $amount=new INum('crtva_amount');
       $amount->value=abs($result['solde']);

       if ( $result['debit'] > $result['credit']) {
	 $amount_vat+=$result['solde'];
	 $ICheckBox->selected=false;
       } else {
	 $amount_vat-=$result['solde'];
	 $ICheckBox->selected=true;

       }
       /* display row */
       if ( $result['solde'] != 0)  $r.=tr(td($account->input()).td('Créance compte TVA').td($amount->input()).td($ICheckBox->input()));

     }
     /* dette tva */
     $dttva=$this->db->get_value("select paccount from tva_belge.parameter where pcode='DTTVA'");
     if ( $dttva != ''  ) {
       /* get saldo */
       $saldo=new Acc_Account_Ledger($this->db,$dttva);
       /* get label */
       $lib=$this->db->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',array($dttva));
				
       $cond=sprintf(" j_date >=to_date('%s','DD.MM.YYYY') and j_date <= '%s'",
		     $first_day,
		     $this->end_date);
       $result=$saldo->get_solde_detail($cond);
       $ICheckBox=new ICheckBox('dttva_ic');
       $account=new IText('dttva');
       $account->value=$dttva;
       $amount=new INum('dttva_amount');
       $amount->value=abs($result['solde']);

       if ( $result['credit'] > $result['debit'] ) {
	 $amount_vat-=$result['solde'];
	 $ICheckBox->selected=true;
       } else {
	 $ICheckBox->selected=false;
	 $amount_vat+=$result['solde'];
       }
       /* display row */
       if ( $result['solde'] != 0)       $r.=tr(td($account->input()).td('Dette Compte TVA').td($amount->input()).td($ICheckBox->input()));

     }
     /* if amount_vat > 0 then we have to pay */
     if ( $amount_vat < 0 ) {
       /* dette tva */
       if ( $dttva != ''  ) {
	 /* get label */
	 $lib=$this->db->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',array($dttva));
				
	 $ICheckBox=new ICheckBox('solde_ic');
	 $ICheckBox->selected=false;
	 $account=new IText('solde');
	 $account->value=$dttva;
	 $amount=new INum('solde_amount');
	 $amount->value=abs($amount_vat);

	 /* display row */
	 $r.=tr(td($account->input()).td('Dette Compte TVA').td($amount->input()).td($ICheckBox->input()));
       }
     }else {
       /* creance tva */
       if ( $crtva != ''  ) {
	 /* get label */
	 $lib=$this->db->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',array($crtva));
				
	 $ICheckBox=new ICheckBox('solde_ic');
	 $ICheckBox->selected=true;
	 $account=new IText('solde');
	 $account->value=$crtva;
	 $amount=new INum('solde_amount');
	 $amount->value=abs($amount_vat);

	 /* display row */
	 $r.=tr(td($account->input()).td('Créance Compte TVA').td($amount->input()).td($ICheckBox->input()));
       }

     }
     $r.='</table>';
     return $r;
   }
   function display() {
     $r= '<form id="readonly">';
     $r.='<div style="position:absolute;top:150;right:0;width:200;right-margin:3%">';
     $r.=$this->menu();
     $r.='</div>';
     $r.=$this->display_info();
     $r.=$this->display_declaration_amount();
     $r.='</form>';
     $r.= create_script("$('readonly').disable();");
     return $r;
   }

}

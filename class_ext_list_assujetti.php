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

require_once('class_listing.php');
require_once('class_fiche.php');
require_once('class_tva_parameter.php');
class Ext_List_Assujetti extends Listing {
  protected $variable = array(
			      "id"=>"a_id",
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
  private $aChild=array();
  static function choose_periode($by_year=false) {
   
    // year
    $year = new IText('year');
    $year->size=4;
    $str_year=$year->input();
    
   
    $str_submit=HtmlInput::submit('decl',_('Afficher'));
    $str_hidden=HtmlInput::extension().dossier::hidden();
    if (isset($_REQUEST['sa']))
      $str_hidden.=HtmlInput::hidden('sa',$_REQUEST['sa']);
    $r='<form method="get">';
    $r.="Année :".$str_year;
    $r.=$str_submit;
    $r.=$str_hidden;
    $r.='</form>';
    return $r;
  }

  function from_array($p_array){
    if ( isset($p_array['name_child'])) {
      $name=$p_array['name_child'];
      $qcode=$p_array['qcode'];
      $code=$p_array['plugin_code'];
      $tva_num=$p_array['tva_num_child'];
      $amount=$p_array['amount'];
      $amount_vat=$p_array['amount_vat'];

      // retrieve missing and compute an array
      for ($i=0;$i<count($name);$i++){
	$child=new Ext_List_Assujetti_Child($this->db);
	$child->set_parameter('amount',$amount[$i]);
	$child->set_parameter('amount_vat',$amount_vat[$i]);
	$child->set_parameter('qcode',$qcode[$i]);
	$child->set_parameter('name_child',$name[$i]);
	$child->set_parameter('tva_num',$tva_num[$i]);

	$array[]=$child;
      }//end for			    
      $this->aChild=$array;
    } else 
      $this->aChild=array();
    
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
  function display() {
    $r= '<form class="print" id="readonly">';
    $r.=$this->display_info();
    $r.=$this->display_declaration_amount();
    $r.='</form>';
    $r.= create_script("$('readonly').disable();");
    return $r;
  }
  function load() {
    $sql="select * from tva_belge.assujetti where a_id=$1";

    $res=$this->db->get_array(
			      $sql,
			      array($this->a_id)
			      );
    if ( $this->db->count() == 0 ) return 0;
    foreach ($res[0] as $idx=>$value) { $this->$idx=$value; }
    // load child
    $sql="select * from tva_belge.assujetti_chld where a_id=$1";
    $res=$this->db->get_array(
			      $sql,
			      array($this->a_id)
			      );
    $nb=$this->db->count();
    $array=array();
    // retrieve missing and compute an array
    for ($i=0;$i<$nb;$i++){
      $child=new Ext_List_Assujetti_Child($this->db);	
      foreach ($res[$i] as $idx=>$value){
	$child->$idx=$value;
      }	
      $array[]=$child;
    }//end for			    
    $this->aChild=$array;

    return 1;
  }
  function verify() {
    return 0;
  }
  function insert() {
    $this->db->start();

    /* insert into the first table */
    $sql=<<<EOF
      INSERT INTO tva_belge.assujetti(
				      start_date, end_date,  periodicity, tva_name, 
				      num_tva, adress, country,  periode_dec,exercice)
      VALUES (to_date($1,'DD.MM.YYYY'),to_date($2,'DD.MM.YYYY'),$3,$4,$5,$6,$7,$8,$9) returning a_id;
EOF;
    $this->a_id=$this->db->get_value($sql,
				     array(
					   $this->start_periode, /* 1 */
					   $this->end_periode,
					   $this->flag_periode, /* 3 */
					   $this->tva_name,
					   $this->num_tva, /* 5 */
					   $this->adress,
					   $this->country, /* 7 */
					   $this->periode_dec,
					   $this->exercice /* 9 */
					   )
				     );
    /* insert into the child table */
    for ($e=0;$e<count($this->aChild);$e++){
      $this->aChild[$e]->set_parameter('depend',$this->a_id);
      $this->aChild[$e]->insert();
    }
    $this->db->commit();

  }
  function update() {
  }

  function compute(){
    /* retrieve accounting customer */
    $code_customer=new Acc_Parm_Code($this->db);
    $code_customer->p_code='CUSTOMER';
    $code_customer->load();
    $a=$this->find_tva_code(array('GRIL01','GRIL02','GRIL03'));
    echo $a;
    if (trim($a)=='') $a=-1;
    $sql=<<<EOF
      select sum(j_montant) as amount,j_qcode
      from jrnx 
      where j_grpt in (select distinct j_grpt from quant_sold join jrnx using (j_id) where qs_vat_code in ($a) ) 
      and j_poste::text like $1||'%'
      and (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
      group by j_qcode
EOF;
    // get all of them
    $all=$this->db->get_array($sql,array($code_customer->p_value,
					 $this->start_periode,
					 $this->end_periode
					 )
			      );
    $array=array();

    // retrieve missing and compute an array
    for ($i=0;$i<count($all);$i++){
      $child=new Ext_List_Assujetti_Child($this->db);


      $child->set_parameter('qcode',$all[$i]['j_qcode']);
      $fiche=new Fiche($this->db);
      $fiche->get_by_qcode($all[$i]['j_qcode'],false);
      $num_tva=$fiche->strAttribut(ATTR_DEF_NUMTVA);
      $child->set_parameter('tva_num',$num_tva);
      $sq="select sum(qs_vat) from quant_sold 
where qs_client = $1 and j_id in (select distinct j_id from jrnx where  j_date >= to_date($2,'DD.MM.YYYY')
                                  and j_date <= to_date($3,'DD.MM.YYYY')
                                  )
     and qs_vat_code in ($a)
";
      // if in the same operation, we use 2 different tva code, the amount is incorrect if one of them is
      // excluded
      $exclude="select coalesce(sum(qs_price),0) from quant_sold
where qs_client = $1 and j_id in (select distinct j_id from jrnx where  j_date >= to_date($2,'DD.MM.YYYY')
                                  and j_date <= to_date($3,'DD.MM.YYYY')
                                  )
     and qs_vat_code not in ($a)
";
      $excl=$this->db->get_value($exclude,array($fiche->id,
						$this->start_periode,
						$this->end_periode));
      $amount_vat=$this->db->get_value($sq,array($fiche->id,
						 $this->start_periode,
						 $this->end_periode));
      $amount=$all[$i]['amount']-$amount_vat-$excl;
      $child->set_parameter('amount',$amount);
      $child->set_parameter('amount_vat',$amount_vat);

      $child->set_parameter('name_child',$fiche->strAttribut(ATTR_DEF_NAME));

      $array[]=$child;
    }//end for			    
    $this->aChild=$array;
  }
  /**
   *@todo finish it
   */
  function display_declaration_amount() {
    $res='<fieldset><legend>Listing</legend>';
    $res.= '<table id="tb_dsp" class="result" style="width:80%;margin-left:5%">';
    $clean=new IButton();
    $clean->label='Efface ligne';
    $clean->javascript="deleteRow('tb_dsp',this);";
	
    $r='';
    $r.=th('QuickCode');
    $r.=th('Name');
    $r.=th('Code Pays et numéro de TVA');
    $r.=th('montant CA');
    $r.=th('montant TVA');
    $r.=th('');
    $amount=0;$amount_vat=0;
    $res.=tr($r);
    for ($i=0;$i<count($this->aChild);$i++) {
      $a=new IText('qcode[]',$this->aChild[$i]->get_parameter('qcode'));
      $b=new IText('name_child[]',$this->aChild[$i]->get_parameter('name_child'));
      $c=new IText('tva_num_child[]',$this->aChild[$i]->get_parameter('tva_num'));
      $e=new INum('amount[]',$this->aChild[$i]->get_parameter('amount'));
      $d=new INum('amount_vat[]',$this->aChild[$i]->get_parameter('amount_vat'));

      $amount+=round($this->aChild[$i]->get_parameter('amount'),2);
      $amount_vat+=round($this->aChild[$i]->get_parameter('amount_vat'),2);
      $r=td($a->input());
      $r.=td($b->input());
      $r.=td($c->input());
      $r.=td($e->input());
      $r.=td($d->input());
      $r.=td($clean->input());
      $res.=tr($r);

    }
    $r=td('');
    $r.=td('');
    $r.=td(hb('Total'));
    $r.=td(hb(sprintf('%.02f',$amount)));
    $r.=td(hb(sprintf('%.02f',$amount_vat)));


    $res.=tr($r);
    $res.='</table>';
    $res.='</fieldset>';
    return $res;
  }
}

class Ext_List_Assujetti_Child extends Ext_List_Assujetti {
  protected $variable=array(
			    "id"=>"ac_id",
			    "tva_num"=>"ac_tvanum",
			    "amount"=>"ac_amount",
			    "amount_vat"=>"ac_vat",
			    "depend"=>"a_id",
			    "qcode"=>"ac_qcode",
			    "name_child"=>'ac_name'
			    );
  function insert() {

    $sql=<<<EOF
      INSERT INTO tva_belge.assujetti_chld(
					   a_id, ac_tvanum, ac_amount, ac_vat,  ac_qcode, 
					   ac_name)
      VALUES ($1, $2, $3, $4, $5, $6) returning ac_id; 
EOF;
    $this->ic_id=$this->db->get_value($sql,array(
						 $this->a_id,
						 $this->ac_tvanum,
						 $this->ac_amount,
						 $this->ac_vat,
						 $this->ac_qcode,
						 $this->ac_name
						 ));
  }

}

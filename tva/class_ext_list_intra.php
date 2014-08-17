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

require_once('class_listing.php');
require_once('class_fiche.php');
require_once('class_tva_parameter.php');
class Ext_List_Intra extends Listing {
  protected $variable = array(
			      "id"=>"i_id",
			      "date"=>"i_date",
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

  function from_array($p_array){
    if( isset($p_array['name_child'])) {
      $name=$p_array['name_child'];
     $qcode=$p_array['qcode'];
     $code=$p_array['tvacode'];
     $tva_num=$p_array['tva_num_child'];
     $amount=$p_array['amount'];
     $periode=$p_array['periode'];
     $array=array();
    // retrieve missing and compute an array
    for ($i=0;$i<count($name);$i++){
      $child=new Ext_List_Intra_Child($this->db);
      $child->set_parameter('amount',$amount[$i]);
      $child->set_parameter('periode',$periode[$i]);
      $child->set_parameter('qcode',$qcode[$i]);
      $child->set_parameter('code',$code[$i]);
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
	 $r.=HtmlInput::request_to_hidden(array('ac'));
     $r.=$this->display_declaration_amount();
     $r.='</form>';
     $js_remove=sprintf("onclick=\"if ( confirm('%s')){remove_form('%s',%d,%d,'li');}\"",
		       "Vous confirmez vouloir effacer ?",
			$_REQUEST['plugin_code'],
			dossier::id(),
			$this->i_id);

     $r.=HtmlInput::button('Effacer','Effacer',$js_remove);
     $r.= create_script("$('readonly').disable();");
     return $r;
  }
  function load() {
    $sql="select * from tva_belge.intracomm where i_id=$1";

   $res=$this->db->get_array(
			    $sql,
			    array($this->i_id)
			    );
   if ( $this->db->count() == 0 ) return 0;
   foreach ($res[0] as $idx=>$value) { $this->$idx=$value; }
   // load child
   $sql="select * from tva_belge.intracomm_chld where i_id=$1";
   $res=$this->db->get_array(
			    $sql,
			    array($this->i_id)
			    );
   $nb=$this->db->count();
   $array=array();
   // retrieve missing and compute an array
   for ($i=0;$i<$nb;$i++){
     $child=new Ext_List_Intra_Child($this->db);
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
INSERT INTO tva_belge.intracomm(
            start_date, end_date,  periodicity, tva_name,
            num_tva, adress, country,  periode_dec,exercice)
      VALUES (to_date($1,'DD.MM.YYYY'),to_date($2,'DD.MM.YYYY'),$3,$4,$5,$6,$7,$8,$9) returning i_id;
EOF;
$this->i_id=$this->db->get_value($sql,
		     array(
			   $this->start_periode,
			   $this->end_periode,
			   $this->flag_periode,
			   $this->tva_name,
			   $this->num_tva,
			   $this->adress,
			   $this->country,
			   $this->periode_dec,
			   $this->exercice
			   )
		     );
/* insert into the child table */
for ($e=0;$e<count($this->aChild);$e++){
  $this->aChild[$e]->set_parameter('depend',$this->i_id);
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
    $a=$this->find_tva_code('CLINTRA');

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
		$child=new Ext_List_Intra_Child($this->db);
		$child->set_parameter('amount',$all[$i]['amount']);
		switch ($this->flag_periode) {
		case 1:
		// by month
		$child->set_parameter('periode',sprintf('%02d%s',$this->periode_dec,$this->exercice));
		break;
		case 2:
		/* quaterly */
		$child->set_parameter('periode',sprintf('3%d%s',$this->periode_dec,$this->exercice));
		break;
		case 3:
		/* yearly */
		$child->set_parameter('periode',sprintf('00%s',$this->periode_dec,$this->exercice));
		break;
      } // end switch

      $child->set_parameter('qcode',$all[$i]['j_qcode']);
      $fiche=new Fiche($this->db);
      $fiche->get_by_qcode($all[$i]['j_qcode'],false);
      $num_tva=$fiche->strAttribut(ATTR_DEF_NUMTVA);

      if ( trim($num_tva) === "") {continue;}
      $child->set_parameter('tva_num',$num_tva);

      $child->set_parameter('name_child',$fiche->strAttribut(ATTR_DEF_NAME));
      $child->set_parameter('code','L');

      $array[]=$child;
    }//end for
    $this->aChild=$array;
  }
  /**
   *@todo finish it
   */
  function display_declaration_amount($p_readonly=false) {
    $res='<fieldset><legend>Listing</legend>';

    $res.= '<table id="tb_dsp" class="result" style="width:80%;">';
    $clean=new IButton();
    $clean->label='Efface ligne';
    $clean->javascript="deleteRow('tb_dsp',this);";

    $r='';
    $r.=th('QuickCode');
    $r.=th('Name');
    $r.=th('Code Pays et numéro de TVA');
    $r.=th('Code Pays et numéro de TVA');
    $r.=th('montant');
    $r.=th('periode');
    $r.=th('');
    $amount=0;
    $res.=tr($r);
    for ($i=0;$i<count($this->aChild);$i++) {
      $a=new IText('qcode[]',$this->aChild[$i]->get_parameter('qcode'));
      $b=new IText('name_child[]',$this->aChild[$i]->get_parameter('name_child'));
      $c=new IText('tva_num_child[]',$this->aChild[$i]->get_parameter('tva_num'));
      $d=new IText('tvacode[]',$this->aChild[$i]->get_parameter('code'));
      $e=new INum('amount[]',$this->aChild[$i]->get_parameter('amount'));
      $f=new IText('periode[]',$this->aChild[$i]->get_parameter('periode'));

      $amount+=round($this->aChild[$i]->get_parameter('amount'),2);
      $r=td($a->input());
      $r.=td($b->input());
      $r.=td($c->input());
      $r.=td($d->input());
      $r.=td($e->input());
      $r.=td($f->input());
      if (! $p_readonly) $r.=td($clean->input());
      $res.=tr($r);

    }
	if ( $p_readonly)
	{
		$r=td('');
		$r.=td('');
		$r.=td('');
		$r.=td(hb('Total'));
		$r.=td(hb(sprintf('%.02f',$amount)));
		$r.=td('');
		$r.=td('');
		$res.=tr($r);
	}
	$res.='</table>';
    $res.='</fieldset>';
    return $res;
  }
}

class Ext_List_Intra_Child extends Ext_List_Intra {
  protected $variable=array(
			    "id"=>"ic_id",
			    "tva_num"=>"ic_tvanum",
			    "amount"=>"ic_amount",
			    "code"=>"ic_code",
			    "periode"=>"ic_periode",
			    "depend"=>"i_id",
			    "qcode"=>"ic_qcode",
			    "name_child"=>'ic_name'
			    );
  function insert() {
$sql=<<<EOF
INSERT INTO tva_belge.intracomm_chld(
            i_id, ic_tvanum, ic_amount, ic_code, ic_periode, ic_qcode,
            ic_name)
  VALUES ($1, $2, $3, $4, $5, $6, $7) returning ic_id;
EOF;
$this->ic_id=$this->db->get_value($sql,array(
					     $this->i_id,
					     $this->ic_tvanum,
					     $this->ic_amount,
					     $this->ic_code,
					     $this->ic_periode,
					     $this->ic_qcode,
					     $this->ic_name));
  }

}

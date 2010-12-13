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
 * \brief generate writing of amortize
 */
require_once('class_amortissement_histo_sql.php');

class Am_Generate
{

  /**
   *Show the form to generate the writing
   * select ledger
   *        year
   *        date of operation
   *        list of material
   */
  function input($p_array)
  {
    global $cn;
    /*
     * select ledger
     */

    $legder = new Acc_Ledger($cn,0);
    $sel_ledger=$legder->select_ledger('ODS',2);
    $sel_ledger->selected=(isset($p_array['p_jrn']))?$p_array['p_jrn']:'';
    /*
     * Operation Date
     */
    $p_date=new IDate('p_date');
    $p_date->value=(isset($p_array['p_date']))?$p_array['p_date']:date('d.m.Y');

    /*
     * select year
     */
    $year=new INum('p_year');
    $year->size=4;
    $year->value=(isset($p_array['p_year']))?$p_array['p_year']:date('Y');

    /*
     * show all the visible material
     */
    require_once('template/util_generate.php');
  }
  /**
   *Propose to save the writing in the selected ledger
@code
array
  'plugin_code' => string 'AMORT' (length=5)
  'sa' => string 'util' (length=4)
  'gDossier' => string '48' (length=2)
  'p_year' => string '' (length=0)
  'p_jrn' => string '4' (length=1)
  'p_date' => string '' (length=0)
  'a_id' => 
    array
      0 => string '86' (length=2)
      1 => string '85' (length=2)
  'p_ck1' => string '' (length=0)

@endcode
   */
  function propose_writing($p_array)
  {
    global $cn;
    $msg='';
    if ( isNumber($p_array['p_year']) == 0 || $p_array['p_year'] < 1900 || $p_array['p_year']>2100) $msg="Année invalide\n";
    if ( isDate($p_array['p_date']) == null) $msg.="Date invalide ";
    if ( $msg != '') 
      {
	echo alert($msg);
	return false;
      }

    $array=array(
		 'p_jrn' => $p_array['p_jrn'],
		 'e_date' => $p_array['p_date'],
		 'periode' => 0,
		 'desc' => 'Amortissement '.$p_array['p_year'],
		 'e_pj' => '',
		 'e_pj_suggest' => '',

		 );
    $idx=0;
    for ($i =0;$i<count($p_array['a_id']);$i++)
      {
	if ( isset ($p_array['p_ck'.$i]))
	  {
	 
	    /*
	     * If selected the add it to array
	     */
	    $n=new Amortissement_Sql($cn,$p_array['a_id'][$i]);
	    $fiche=new Fiche($cn,$n->f_id);
	    $val=$cn->get_value("select ad_amount from amortissement.amortissement_detail ".
				" where a_id = $1 and ad_year=$2",
				array($n->a_id,$p_array['p_year']));
	    $val=($val=='')?0:$val;
	    $mat=array('poste'.$idx => $n->account_deb,
		       'amount'.$idx=> $val,
		       'ld'.$idx =>'Dotation à '.$fiche->strAttribut(ATTR_DEF_QUICKCODE),
		       'ck'.$idx=>1,
		       'qc_'.$idx=>'');
	    $array+=$mat;
	    $idx++;
	    $mat=array('poste'.$idx => $n->account_cred,
		       'amount'.$idx=> $val,
		       'ld'.$idx =>'Amortissement '.$fiche->strAttribut(ATTR_DEF_QUICKCODE),
		       'qc_'.$idx=>'');
	    $array+=$mat;
	    $idx++;	       
	  }
      }
    $array+=array('nb_item'=>$idx);
    $ledger=new Acc_Ledger($cn,$p_array['p_jrn']);
    $ledger->with_concerned=false;    
    echo '<div style="width:80%;margin-left:10%">';
    echo '<form method="POST" style="display:inline">';
    echo $ledger->show_form($array,true);
    echo HtmlInput::submit('save','Sauver');
    echo HtmlInput::hidden('sa',$p_array['sa']);
    echo HtmlInput::hidden('sb',$p_array['sb']);
    echo HtmlInput::hidden('p_year',$p_array['p_year']);
    echo HtmlInput::hidden('p_date',$p_array['p_date']);
    echo HtmlInput::hidden('p_jrn',$p_array['p_jrn']);
    echo HtmlInput::hidden('plugin_code',$p_array['plugin_code']);
    for ($i=0;$i<count($p_array['a_id']);$i++)
      {
	echo HtmlInput::hidden('a_id[]',$p_array['a_id'][$i]);
	if ( isset($p_array['p_ck'.$i]))
	  echo HtmlInput::hidden('p_ck'.$i,'1');
      }

    echo '</form>';
    /*
     * correct
     */
    echo '<form method="POST" style="display:inline">';
    echo dossier::hidden();
    echo HtmlInput::hidden('sa',$p_array['sa']);
    echo HtmlInput::hidden('sb',$p_array['sb']);
    echo HtmlInput::hidden('p_year',$p_array['p_year']);
    echo HtmlInput::hidden('p_date',$p_array['p_date']);
    echo HtmlInput::hidden('p_jrn',$p_array['p_jrn']);
    echo HtmlInput::hidden('plugin_code',$p_array['plugin_code']);
    for ($i=0;$i<count($p_array['a_id']);$i++)
      {
	echo HtmlInput::hidden('a_id[]',$p_array['a_id'][$i]);
	if ( isset($p_array['p_ck'.$i]))
	  echo HtmlInput::hidden('p_ck'.$i,'1');
      }
    echo HtmlInput::submit('correct','Corriger');
    echo '</form>';
    echo '</div>';
    return true;		 
  }
  /**
   * save into amortissement_histo
@code
array
  'plugin_code' => string 'AMORT' (length=5)
  'sa' => string 'util' (length=4)
  'gDossier' => string '48' (length=2)
  'p_year' => string '' (length=0)
  'p_jrn' => string '4' (length=1)
  'p_date' => string '' (length=0)
  'a_id' => 
    array
      0 => string '86' (length=2)
      1 => string '85' (length=2)
  'p_ck1' => string '' (length=0)

@endcode
   */
  function save($p_array,$jr_internal)
  {
    global $cn;
    $msg='';
    for ( $i=0;$i<count($p_array['a_id']);$i++)
      {
	if ( isset($p_array['p_ck'.$i]))
	  {
	    /*
	     * Check if already encoded
	     */
	    if ( $cn->get_value ("select count (*) from amortissement.amortissement_histo where h_year=$1 and a_id=$2",
				 array($p_array['p_year'],$p_array['a_id'][$i])) != 0)
	      {
		/*
		 * Already encoded : continue an exception will be thrown to rollback it
		 */
		$f_id=$cn->get_value("select f_id from amortissement.amortissement where a_id=$1",
				     array($p_array['a_id'][$i]));
		$fiche=new Fiche ($cn,$f_id);
		$msg.="Fiche ".$fiche->strAttribut(ATTR_DEF_QUICKCODE)." déja amortie \n";
	      }
	    else
	      {
		/*
		 * Do not exist we insert into amortissement.amortissement_histo
		 */
		$n=new Amortissement_Histo_Sql($cn);
		$val=$cn->get_value("select ad_amount from amortissement.amortissement_detail ".
				    " where a_id = $1 and ad_year=$2",
				    array($p_array['a_id'][$i],$p_array['p_year']));
		$val=($val=='')?0:$val;

		$n->h_amount=$val;
		$n->h_year=$p_array['p_year'];
		$n->jr_internal=$jr_internal;
		$n->a_id=$p_array['a_id'][$i];
		$n->insert();
	      }
	  }
      }
    return $msg;
  }
}
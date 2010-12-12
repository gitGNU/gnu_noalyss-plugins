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
 * \brief let you add all the materiel you need to amortize
 */
require_once('class_amortissement_sql.php');
require_once('class_amortissement_detail_sql.php');

class Am_Card
{
  function __construct()
  {
  }
  /**
   *@brief display the list of material
   */
  public function  listing($p_all=false)
  {
    global $cn;
    $amort=new Amortissement_Sql($cn);
    if ( $p_all==true)
      $ret=$amort->seek();
    else
      $ret=$amort->seek(" where a_visible = 'Y'");

    require_once('template/material_listing.php');

  }
  /**
   *@brief display a button to add a material
   */
  public function add_card()
  {
    $add=new IButton('add_card');
    $add->label="Ajout d'un bien à amortir";
    $add->javascript=sprintf("add_material(%d,'%s','bxmat')",
			     dossier::id(),
			     $_REQUEST['plugin_code']
			     );
    return $add;
  }
  /**
   * all the data are in the array : we construct an object with data from the array
   */
  public function from_array($p_array)
  {
    global $cn;
    /**
     *@todo you should first verify that all data are correct
     */
    $this->amortissement=new Amortissement_Sql($cn);
    $fiche=new Fiche($cn);
    $fiche->get_by_qcode($p_array['p_card']);
    $this->amortissement->a_id=(isset($p_array['a_id']))?$p_array['a_id']:-1;
    $this->amortissement->f_id=$fiche->id;
    $this->amortissement->account_deb=$p_array['p_deb'];
    $this->amortissement->account_cred=$p_array['p_cred'];
    $this->amortissement->a_start=$p_array['p_year'];
    $this->amortissement->a_amount=$p_array['p_amount'];
    $this->amortissement->a_nb_year=$p_array['p_number'];
    $this->amortissement->a_visible=(isset($p_array['p_visible']))?$p_array['p_visible']:'Y';
    /*
     * if details then load them
     */

    if ( isset($p_array['ad_id']))
      {
	for ($i=0;$i<count($p_array['ad_id']);$i++)
	  {
	    $am=new Amortissement_Detail_Sql($cn);
	    $am->ad_id=$p_array['ad_id'][$i];
	    $am->ad_amount=$p_array['amount'][$i];
	    $am->ad_amount=$p_array['amount'][$i];
	    $am->a_id=$this->amortissement->a_id;
	    $am->ad_percentage=$p_array['pct'][$i];
	    $am->ad_year=$p_array['ad_year'][$i];

	    $this->amortissement_detail[]=clone($am);
	  }
      }
  }
  /**
   * show a form to modify data
   */
  public function input()
  {
    global $cn;
    $this->amortissement->load();
    $this->amortissement_detail=new Amortissement_Detail_Sql($cn);
    $array=$this->amortissement_detail->seek(' where a_id=$1 order by ad_year asc',array($this->amortissement->a_id));
    $a_id=HtmlInput::hidden('a_id',$this->amortissement->a_id);
    $value_a_id=$this->amortissement->a_id;

    $p_year=new INum('p_year');
    $p_year->value=$this->amortissement->a_start;
    $p_number=new INum('p_number');
    $p_number->value=$this->amortissement->a_nb_year;

    $p_visible=new IText('p_visible');
    $p_visible->size=2;
    $p_visible->value=$this->amortissement->a_visible;
    $card=new Fiche($cn,$this->amortissement->f_id);

    $p_card=HtmlInput::hidden('p_card',$card->strAttribut(ATTR_DEF_QUICKCODE));


    $p_deb=new IPoste('p_deb');
    $p_deb->set_attribute('jrn',0);
    $p_deb->set_attribute('account','p_deb');
    $p_deb->set_attribute('label','p_deb_label');
    $p_deb->value=$this->amortissement->account_deb;
    $deb_span=new ISpan('p_deb_label');

    $p_cred=new IPoste('p_cred');
    $p_cred->set_attribute('jrn',0);
    $p_cred->set_attribute('account','p_cred');
    $p_cred->set_attribute('label','p_cred_label');
    $p_cred->value=$this->amortissement->account_cred;

    $cred_span=new ISpan('p_cred_label');

    $p_amount=new INum('p_amount');
    $p_amount->value= $this->amortissement->a_amount;
    require_once('template/material_detail.php');
  }
  /**
   *Verify that all data are correct
   */
  function verify_post()
  {
    global $cn;
    $error_msg='';
    if ( isNumber($_POST['p_year']) == null || $_POST['p_year']<1900||$_POST['p_year'] > 3000 ) $error_msg.=_('Année invalide')."\n";
    if ( isNumber($_POST['p_number']) == null || $_POST['p_number']==0)$error_msg.=_ ('Nombre annuités invalide')."\n";
    if ( isNumber($_POST['p_amount']) == null || $_POST['p_amount']==0) $error_msg.=_ ('Montant invalide')."\n";
    if ( $_POST['p_visible'] != 'Y' && $_POST['p_visible'] != 'N') $error_msg.="Visible Y ou N\n";
    if ( $cn->get_value('select count(*) from tmp_pcmn where pcm_val=$1',array($_POST['p_deb'])) == 0) $error_msg.=" Poste de charge incorrect"."\n";
    if ( $cn->get_value('select count(*) from tmp_pcmn where pcm_val=$1',array($_POST['p_cred'])) == 0) $error_msg.=" Poste à créditer incorrect"."\n";
    return $error_msg;
  }
  /**
-   *  we save into the two tables 
   * amortissement and amortissement_detail
   *@see from_array
   */
  public function update()
  {
    global $cn;

    try 
      {
	$this->amortissement->update();
	for ( $i=0;$i< count($this->amortissement_detail);$i++)
	  {
	    $this->amortissement_detail[$i]->update();
	  }
	/*
	 * remove row from amortissement_detail if ad_amount=0
	 */
	$cn->exec_sql('delete from amortissement.amortissement_detail where ad_amount=0');

      }
    catch (Exception $e)
      {
	echo $e->getMessage();
      }
  }
  /**
   *  we save into the two tables 
   * amortissement and amortissement_detail, the table amortissement_detail
   * is filled via a trigger
   *@see from_array
   */
  public function add()
  {
      $this->amortissement->save();
  }
  public function set_material($f)
  {
    global $cn;
    $this->amortissement=new Amortissement_Sql($cn);
    $this->amortissement_detail=new Amortissement_Detail_Sql($cn);
    $this->amortissement->a_id=$cn->get_value("select a_id from amortissement.amortissement where f_id=$1",
					      array($f));
  }
}
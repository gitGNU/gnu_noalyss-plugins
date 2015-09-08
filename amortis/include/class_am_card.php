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

  // Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

  /*!\file
   * \brief let you add all the materiel you need to amortize
   */
require_once('class_amortissement_sql.php');
require_once('class_amortissement_detail_sql.php');
require_once('class_amortissement_histo_sql.php');

class Am_Card
{
    
    private $amortissement; //<! Amortissement_SQL
    
    private $amortissement_detail; //<! child records Amortissement_Detail_Sql
                                   //contains details per year
    
    private $amortissement_histo; //<! child records Amortissement_Histo_Sql,
                                  // contains histo info : concerned, amount...
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
      $ret=$amort->seek(" join fiche_detail using(f_id) where a_visible = 'Y' and ad_id=1 order by ad_value");

    require_once('template/material_listing.php');

  }
  /**
   *@brief display a button to add a material
   */
  public function add_card()
  {
    $add=new IButton('add_card');
    $add->label=_("Ajout d'un bien à amortir");
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
    $this->amortissement->a_date=$p_array['p_date'];
    $this->amortissement->a_visible=(isset($p_array['p_visible']))?$p_array['p_visible']:'Y';
    
    if ( $p_array['type'] == 1 ) {
        $fiche_deb=new Fiche($cn);
        $fiche_deb->get_by_qcode($p_array['p_card_deb'],false);
        $this->amortissement->card_deb=($fiche_deb->id != 0 ) ? $fiche_deb->id:null;

        $fiche_cred=new Fiche($cn);
        $fiche_cred->get_by_qcode($p_array['p_card_cred'],false);
        $this->amortissement->card_cred=($fiche_cred->id != 0 ) ? $fiche_cred->id:null;
        $this->amortissement->account_deb=null;
        $this->amortissement->account_cred=null;
    } else {
         $this->amortissement->account_deb=$p_array['p_deb'];
         $this->amortissement->account_cred=$p_array['p_cred'];
         $this->amortissement->card_cred=null;
         $this->amortissement->card_deb=null;
    }
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
    /* save detail from amortissement_histo 
     * p_pj[]
     * p_histo[]
     * h[]
     */
    if ( isset ($p_array['h'])) 
      {
	for ($i=0;$i<count($p_array['h']);$i++)
	  {
	    $am=new Amortissement_Histo_Sql($cn,$p_array['h'][$i]);
	    $am->load();
	    $am->h_amount=$p_array['p_histo'][$i];
	    $am->h_pj=$p_array['p_pj'][$i];
            $am->jr_id=0;
            if ( isset ($p_array['op_concerne'][$i+1])) {
                $am->jr_id=$p_array['op_concerne'][$i+1];
                $am->jr_internal="";
            } else {
                $am->jr_id=0;
            }
	    $this->amortissement_histo[]=clone($am);
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

    $p_date=new IDate('p_date');
    $p_date->value=$this->amortissement->a_date;

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
    $select_type=new ISelect('type');
    $select_type->id='select_type_id';
    $select_type->value=array(array('label'=>'--Faites un choix --','value'=>-1),
        array('label'=>'Poste comptable','value'=>'0'),
        array('label'=>'Fiche','value'=>'1')
        );
    
    $select_type->selected=HtmlInput::default_value_post('type','-1');
    $select_type->javascript=' onchange = "show_selected_material(this);"';
    $select_type->selected=-1;
    if ( $this->amortissement->card_deb != '') $select_type->selected=1;
    if ( $this->amortissement->account_deb != '') $select_type->selected=0;
  
    $fiche_deb=new Fiche($cn);
    
    $p_card_deb=new ICard('p_card_deb');
    $p_card_deb->typecard='all';
    $p_card_cred=new ICard('p_card_cred');
    $p_card_cred->typecard='all';
    if ( $this->amortissement->card_deb != '' ) 
    {
        $fiche_deb=new Fiche($cn,$this->amortissement->card_deb);
        $p_card_deb->value=$fiche_deb->get_quick_code();
    }
    if ( $this->amortissement->card_cred != '' ) 
    {
        $fiche_cred=new Fiche($cn,$this->amortissement->card_cred);
        $p_card_cred->value=$fiche_cred->get_quick_code();
    }
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
    if ( isNumber($_POST['p_number']) == null )$error_msg.=_ ('Nombre annuités invalide')."\n";
    if ( isNumber($_POST['p_amount']) == null || $_POST['p_amount']==0) $error_msg.=_ ('Montant invalide')."\n";
    $p_card = HtmlInput::default_value_post('p_card', '');
    $visible=HtmlInput::default_value_post('p_visible','Y');
    if ( $visible != 'Y' && $visible != 'N') $error_msg.=_("Visible Y ou N\n");
    $_POST['p_visible']=$visible;
    switch ( $_POST['type'] )
    {
        case -1:
            $error_msg .= _('Choisissez poste comptable ou fiche');
            break;
        case 0:
           if ( $cn->get_value('select count(*) from tmp_pcmn where pcm_val=$1',array($_POST['p_deb'])) == 0) $error_msg.=" Poste de charge incorrect"."\n";
           if ( $cn->get_value('select count(*) from tmp_pcmn where pcm_val=$1',array($_POST['p_cred'])) == 0) $error_msg.=" Poste à créditer incorrect"."\n";
            break;
        case 1:
           if ( $cn->get_value('select j_poste from vw_poste_qcode where j_qcode=trim(upper($1))',array($_POST['p_card_deb'])) == "") $error_msg.=" Fiche de charge incorrect"."\n";
           if ( $cn->get_value('select j_poste from vw_poste_qcode where j_qcode=trim(upper($1))',array($_POST['p_card_cred'])) == "") $error_msg.=" Fiche contrepartie incorrect"."\n";
            break;
    }
    $p_new=HtmlInput::default_value_post('p_new',-1);
    /*
     * Check duplicate : check that the material (p_card) is not already in the material to amortize
     */
    if ( $p_new != -1 ) {
        $f_id=$cn->get_value('select f_id from vw_poste_qcode where j_qcode=trim(upper($1))',array($p_card));
        if ( $f_id != "") {
            if ( $cn->get_value('select count(*) from amortissement.amortissement where f_id = $1',array($f_id)) > 0 )            $error_msg.=_('Matériel déjà dans la liste');
        }
    }
    /**
     * Check that op_concerned has an internal
     * and only one
     */
    $nb_histo=count($this->amortissement_histo);
    for ($i=0;$i<$nb_histo;$i++)
    {
        $jr_id=$this->amortissement_histo[$i]->jr_id;
        if (isNumber($jr_id) == 1 && $jr_id != 0 )
        {
            $this->amortissement_histo[$i]->jr_internal=$cn->get_value('select jr_internal from jrn where jr_id=$1',
                   array( $jr_id));
        }
    }
    return $error_msg;
  }
  
  /**
   *  we save into the two tables 
   * amortissement and amortissement_detail
   *@see from_array
   */
  public function update()
    {
        global $cn;

        try
        {
            $this->amortissement->update();
            if (isset($this->amortissement_detail))
            {
                for ($i=0; $i<count($this->amortissement_detail); $i++)
                {
                    $this->amortissement_detail[$i]->update();
                    $this->amortissement_histo[$i]->update();
                }
            } /*
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
  /**
   *Display the detail of a card
   */
  public function print_detail($p_code)
  {
    global $cn;
    $card=new Fiche($cn);
    $card->get_by_qcode($p_code);
    $amort=new Amortissement_Sql($cn);
    $amort->a_id=$cn->get_value('select a_id from amortissement.amortissement where f_id=$1',array($card->id));

    if ( $amort->a_id =='') 
      {
	echo '<h2 class="error">Non trouvé</h2>';
	exit();
      }
    $amort->load();
    $p_amount=$amort->a_amount;
    $p_year=$amort->a_start;
    $p_deb=$amort->account_deb;
    $deb_span=$cn->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',
			     array($p_deb));
    $p_cred=$amort->account_cred;
    $cred_span=$cn->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',
			     array($p_cred));
    $p_number=$amort->a_nb_year;
    $a=new Amortissement_Detail_Sql($cn);
    $p_date=$amort->a_date;
    $array=$a->seek(' where a_id=$1 order by ad_year asc',array($amort->a_id));
    
    $fiche_deb=new Fiche($cn);
    
    $p_card_deb=new ICard('p_card_deb');
    $p_card_deb->setReadOnly(true);
    $p_card_cred=new ICard('p_card_cred');
    $p_card_cred->typecard='all';
    $p_card_cred->setReadOnly(true);
    if ( $amort->card_deb != '' ) 
    {
        $fiche_deb=new Fiche($cn,$amort->card_deb);
        $p_card_deb->value=$fiche_deb->get_quick_code();
    }
    if ( $amort->card_cred != '' ) 
    {
        $fiche_cred=new Fiche($cn,$amort->card_cred);
        $p_card_cred->value=$fiche_cred->get_quick_code();
    }
    
    require_once('template/material_display.php');
  }
}
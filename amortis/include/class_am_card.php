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
  public function  listing()
  {
    global $cn;
    $amort=new Amortissement_Sql($cn);
    $ret=$amort->seek();
    require_once('template/material_listing.php');

  }
  /**
   *@brief display a button to add a material
   */
  public function add_card()
  {
    $add=new IButton('add_card');
    $add->label="Ajout d'un bien à amortir";
    $add->javascript=sprintf("add_material(%d,'%s','bx_mat')",
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
    $this->amortissement->a_id=-1;
    $this->amortissement->f_id=$fiche->id;
    $this->amortissement->account_deb=$p_array['p_deb'];
    $this->amortissement->account_cred=$p_array['p_cred'];
    $this->amortissement->a_start=$p_array['p_year'];
    $this->amortissement->a_amount=$p_array['p_amount'];
    $this->amortissement->a_nb_year=$p_array['p_number'];
    /*
     * if details then load them
     */
    $this->amortissement_detail=new Amortissement_Detail_Sql($cn);
    if ( isset($p_array['ad_id']))
      {
	$e=1;
      }
  }
  /**
   * show a form to modify data
   */
  public function input()
  {
    global $cn;
    $this->amortissement->load();
    $array=$this->amortissement_detail->seek(' where a_id=$1 order by ad_year asc',array($this->amortissement->a_id));

    $p_year=new INum('p_year');
    $p_year->value=$this->amortissement->a_start;
    $p_number=new INum('p_number');
    $p_number->value=$this->amortissement->a_nb_year;

    $card=new Fiche($cn,$this->amortissement->f_id);



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
   *  we save into the two tables 
   * amortissement and amortissement_detail
   *@see from_array
   */
  public function update()
  {
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
}
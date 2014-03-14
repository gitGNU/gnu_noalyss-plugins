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
require_once('class_temp_bank_sql.php');
class Bank_Item
{
  function __construct($id=-1)
  {
    $this->id=$id;
  }
  /**
   *show a dialog box to reconciliate or remove a record, is using the $_GET[id] for importbank.import_temp
   */
  function show_item($ctl)
  {
    global $cn,$msg;
    $id=$_GET['id'];
    $bi=new Temp_Bank_Sql($cn,$id);
    $jrn='';
    if ( $bi->jrn_def_id != '')
      {
	$jrn=$cn->get_value('select jrn_def_name from jrn_def where jrn_def_id=$1',
			    array($bi->jrn_def_id));
	$w=new ICard();
	$w->jrn=$bi->jrn_def_id;
	$w->name='fiche'.$id;
	$w->extra='filter';
	$w->typecard='deb';
	$w->set_dblclick("fill_ipopcard(this);");
       	$w->set_attribute('ipopup','ipopcard');
	$w->set_attribute('label','e_third');
	$w->set_attribute('typecard','deb');
	$w->set_callback('filter_card');
	$w->set_function('fill_data');
	$w->set_attribute('inp','fiche');

	$wConcerned=new IConcerned();
	$wConcerned->name="e_concerned".$id;
	$wConcerned->amount_id=abs($bi->amount);
	$wConcerned->extra2='paid';
	$wConcerned->label=_('op. concernée');
	$wConcerned->table=0;
	$wConcerned->value=$bi->tp_rec;
	$name='';$status='';
	if ( $bi->f_id != null)
	  {
	    $w->value=$cn->get_value('select ad_value from fiche_detail where f_id=$1 and ad_id=23',array($bi->f_id));
	    $name=$cn->get_value('select ad_value from fiche_detail where f_id=$1 and ad_id=1',array($bi->f_id));
	  }
	$third=new IText('tp_third');
	$third->value=$bi->tp_third;

	$extra=new IText('tp_extra');
	$extra->value=$bi->tp_extra;
	if ( strlen($bi->libelle) > 20)
	  {
	    $libelle=new ITextArea('libelle');
	    $libelle->value=$bi->libelle;
	    $libelle->heigh=3;
	    $libelle->width=60;

	  }
	else
	  {
	    $libelle=new IText('libelle');
	    $libelle->value=$bi->libelle;
	    $libelle->size=strlen($bi->libelle);
	  }
	$amount=new INum('amount');
	$amount->value=$bi->amount;

	$date=new IDate('tp_date');
	$date->value=$bi->tp_date;

	switch($bi->status)
	  {
	  case 'N':
	    $status='Nouveau';
	    break;
	  case 'E':
	    $status='Erreur : '.$bi->tp_error_msg;
	    break;
	  case 'W':
	    $status='Attente';
	    break;
	  case 'T':
	    $status='Transféré';
	    $w->readOnly=true;
	    $wConcerned->readOnly=true;
	    $amount->readOnly=true;
	    $third->readOnly=true;
	    $extra->readOnly=true;
	    $libelle->readOnly=true;
	    $date->readOnly=true;

	    break;
	  case 'D':
	    $status='Effacer';
	    break;

	  }
      }
    $remove=new ICheckBox('remove');
    $recup=new ICheckBox('recup');


    require_once('template/detail_item.php');
  }
  function show_delete($ctl)
  {
    global $cn,$msg;
    $id=$_GET['id'];
    $bi=new Temp_Bank_Sql($cn,$id);
    $jrn='';
    if ( $bi->jrn_def_id != '')
      {
	$jrn=$cn->get_value('select jrn_def_name from jrn_def where jrn_def_id=$1',
			    array($bi->jrn_def_id));
      }
    require_once('template/delete_item.php');

  }
  function update()
  {
    global $cn;
    $bi_sql=new Temp_Bank_Sql($cn,$this->id);
    $bi_sql->f_id=$this->f_id;
    $bi_sql->status=$this->status;
    $bi_sql->tp_rec=$this->tp_rec;

    $bi_sql->update();
  }
}

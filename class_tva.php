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

class ext_TVA
{

  static function choose_period() {
    require_once('class_iradio.php');
    $monthly=new IRadio('periodic');
    $monthly->value=1;
    
    // month
    $month=new ISelect('bymonth');
    $array=array ();
    for ($i=0;$i<12;$i++) {
      $array[$i]['value']=$i+1; $array[$i]['label']=sprintf('%02d',($i+1));
    }
    $month->value=$array;
    $str_monthly=$monthly->input();
    $str_month=$month->input();
    // year
    $year = new IText('year');
    $year->size=4;
    $str_year=$year->input();
    
    // Tri 
    $quater=new ISelect('byquaterly');
    $array=array();
    for ($i=0;$i<4;$i++) {
      $array[$i]['value']=$i+1; $array[$i]['label']=sprintf('%02d',($i+1));
    }
    $quater->value=$array;
    $quaterly=new IRadio('periodic');
    $quaterly->value=2;
    $str_quaterly=$quaterly->input();
    $str_quater=$quater->input();
    $str_submit=HtmlInput::submit('decl',_('Afficher'));
    $str_hidden=HtmlInput::extension().dossier::hidden();
    ob_start();
    require_once('form_periode.php');
    $r=ob_get_content();
    ob_clean();
    return $r;
  }
  }
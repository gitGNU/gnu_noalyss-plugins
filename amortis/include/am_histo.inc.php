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
 * \brief print all
 */
$sql="select quick_code,a_id,ha_id,h_amount,jr_internal,h_year,h_pj,vw_name
	from amortissement.amortissement
	join amortissement.amortissement_histo using (a_id)
	join vw_fiche_attr using(f_id) where h_amount > 0";

$header=new Sort_Table();
$r=HtmlInput::array_to_string(array('ac','gDossier','sa','sb','plugin_code'),$_REQUEST);
$url=basename($_SERVER['PHP_SELF']).$r;
$header->add('Quick_code',$url,'order by 1 asc','order by 1 desc','qca','qcd');
$header->add('Nom',$url,'order by vw_name asc','order by vw_name desc','na','nd');
$header->add('Montant',$url,'order by h_amount asc','order by h_amount desc','aa','ad');
$header->add('Année',$url,'order by h_year asc','order by h_year desc','ya','yd');
$header->add('Pièce',$url,'order by h_pj asc','order by h_pj desc','pja','pjd');
$header->add('N° interne',$url,'order by jr_internal asc','order by jr_internal desc','nia','nid');

$ord=(isset($_REQUEST['ord']))?$_REQUEST['ord']:'na';

$sql_ord=$header->get_sql_order($ord);

$sql.=$sql_ord;

if ( isset($_POST['remove']))
  {
    $nb_sel=count($_POST['p_sel']);
    for ($i = 0 ; $i < $nb_sel ;$i++)
    {
        if ( isset($_POST['p_sel'][$i]))
              {
                $cn->exec_sql("update amortissement.amortissement_histo set h_amount=0,h_pj='',jr_internal=null where ha_id=$1",
                              array($_POST['p_sel'][$i]));
          }
    }
  }
$array=$cn->get_array($sql);
require_once('template/listing_histo.php');


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
 * \brief print all
 */
$sql="select quick_code,a_id,ha_id,h_amount,jr_internal,h_year,h_pj,vw_name from amortissement.amortissement join amortissement.amortissement_histo using (a_id) join vw_fiche_attr using(f_id) where h_amount > 0 order by vw_name";


if ( isset($_POST['remove']))
  {
    for ($i=0;$i<count($_POST['h']);$i++)
      {
	if ( isset($_POST['p_sel'][$i]))
	  {
	    $cn->exec_sql("update amortissement.amortissement_histo set h_amount=0,h_pj='',jr_internal='' where ha_id=$1",
			  array($_POST['h'][$i]));
	  }
      }
  }
$array=$cn->get_array($sql);
require_once('template/listing_histo.php');


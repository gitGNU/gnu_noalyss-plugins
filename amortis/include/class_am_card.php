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
    for ( $i=0;$i<0;$i++)
      {
	$a=$amort->fetch($ret,$i);
	
      }
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
}
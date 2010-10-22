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
require_once('class_ext_tvagen.php');
require_once('class_acc_parm_code.php');
class Listing extends Ext_Tva_Gen {
  function find_tva_code($p_array) {
    $a='';$and='';
    for ($e=0;$e<count($p_array);$e++){
      $tva_parameter=new Tva_Parameter($this->db);
      if ( $tva_parameter->set_parameter('code',$p_array[$e]) == -1 )
	throw new Exception ("code : $p_array[$e] non trouve");
      $tva_parameter->load();
      if ( ($c=$tva_parameter->get_parameter('value')) != ''){
	$a=$and.$c;$and=',';
      }
    }//end for
    $aa=get_array_nodup($a);
    $a=join(',',$aa);
    return $a;
  }

  
}
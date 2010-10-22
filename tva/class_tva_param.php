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
 * \brief manage the table tva.parameters
 */

/*!\brief 
 *
 *
 */
class 
{
  /* example private $variable=array("val1"=>1,"val2"=>"Seconde valeur","val3"=>0); */
  private static $variable;
  function __construct ($p_init) {
    /* example    $this->set_parameter("val3",$p_init); */
  }
  public function get_parameter($p_string) {
    if ( array_key_exists($p_string,self::$variable) ) {
      $idx=self::$variable[$p_string];
      return $this->$idx;
    }
    else 
      exit (__FILE__.":".__LINE__.'Erreur attribut inexistant');
  }
  public function set_parameter($p_string,$p_value) {
    if ( array_key_exists($p_string,self::$variable) ) {
      $idx=self::$variable[$p_string];
      $this->$idx=$p_value;
    }
    else 
      exit (__FILE__.":".__LINE__.'Erreur attribut inexistant');
    
    
  }
  public function get_info() {    return var_export(self::$variable,true);  }
  public function verify() {
    // Verify that the elt we want to add is correct
  }
  public function save() {
      $this->update();
  }

  public function update() {
    if ( $this->verify() != 0 ) return;
    /*   please adapt
    $sql="update tva_rate set tva_label=$1,tva_rate=$2,tva_comment=$3,tva_poste=$4 ".
      " where tva_id = $5";
    $res=ExecSqlParam($this->cn,
		 $sql,
		 array($this->tva_label,
		       $this->tva_rate,
		       $this->tva_comment,
		       $this->tva_poste,
		       $this->tva_id)
		 );
		 */
  }

  public function load() {

   $sql="select tva_label,tva_rate, tva_comment,tva_poste from tva_rate where tva_id=$1"; 
    /* please adapt
    $res=ExecSqlParam($this->cn,
		 $sql,
		 array($this->tva_id)
		 );
		 */
    if ( pg_NumRows($res) == 0 ) return;
    $row=pg_fetch_array($res,0);
    foreach ($row as $idx=>$value) { $this->$idx=$value; }
  }
  public function delete() {
/*    $sql="delete from tva_rate where tva_id=$1"; 
    $res=ExecSqlParam($this->cn,$sql,array($this->tva_id));
*/
  }
  /*!\brief
   *\param
   *\return
   *\note
   *\see
   *\todo
   */	
  static function test_me() {
  }
  
}

/* test::test_me(); */


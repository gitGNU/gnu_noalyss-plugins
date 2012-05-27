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


/*!\brief
 *
 *
 */
class  Tva_Parameter
{
  private static $variable=array("code"=>"pcode","value"=>"pvalue","account"=>'paccount');
  function __construct ($p_init) {
    $this->cn=$p_init;
  }
  public function get_parameter($p_string) {
    if ( array_key_exists($p_string,self::$variable) ) {
      $idx=self::$variable[$p_string];
      return $this->$idx;
    }
    else
      exit (__FILE__.":".__LINE__.$p_string.' Erreur attribut inexistant');
  }
  public function set_parameter($p_string,$p_value) {
    if ( array_key_exists($p_string,self::$variable) ) {
      $idx=self::$variable[$p_string];
      $this->$idx=$p_value;
    }
    else
      exit (__FILE__.":".__LINE__.$p_string.' Erreur attribut inexistant');


  }
  public function get_info() {    return var_export(self::$variable,true);  }
  public function verify() {
    // Verify that the elt we want to add is correct
  }
  /**
   *@brief retrieve * row thanks a condition
   */
   public function seek($cond,$p_array=null)
   {
   /*
     $sql="select * from * where $cond";
     return $this->cn->get_array($cond,$p_array)
  */
   }
  public function insert() {
    if ( $this->verify() != 0 ) return;

    $sql="insert into tva_belge.parameter (pcode,pvalue,paccount) ".
      " values ($1,$2) ";
    $this->tva_id=$this->cn->exec_sql(
				       $sql,
				       array($this->pcode,
					     $this->pvalue,
					     $this->paccount)
				       );

  }

  public function update() {
    if ( $this->verify() != 0 ) return;

    $sql="update tva_belge.parameter  set pvalue=$1,paccount=$2 ".
      " where pcode = $3";
    $res=$this->cn->exec_sql(
		 $sql,
		 array($this->pvalue,
		       $this->paccount,
		       $this->pcode)
		 );

  }

  public function load() {

   $sql="select pcode from tva_belge.parameter where pcode=$1";

    $res=$this->cn->get_array(
		 $sql,
		 array($this->pcode)
		 );

    if ( count($res) == 0 ) return -1;
    for ($i=0;$i<count($res);$i++) { $this->pcode=$res[$i]['pcode'];$this->pvalue=$res[$i]['pvalue'];$this->paccount=$res[$i]['paccount']; }
    return 0;
  }
  /**
   *@brief check that this accounting does exist in the accounting plan
   *@param $p_code code to check
   *@return the number of row found (0 if none)
   */
  public function exist_pcmn($p_code) {
    $count=$this->cn->get_value('select count(*) from tmp_pcmn where pcm_val::text like $1',array($p_code));
    return $count;
  }
  /**
   *@brief show the content of the table tva_belge.parameter
   */
  public function display() {
	 global $cn;$cn=$this->cn;

	$res=$this->cn->get_array("select pcode from tva_belge.parameter");

    ob_start();
    require_once('form_parameter.php');
    $r=ob_get_contents();
    ob_end_clean();
    return $r;
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

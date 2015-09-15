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
  /**
   *@file
   *@brief Manage the table amortissement.amortissement_detail
   *
   *
   Example
   @code

   @endcode
  */
require_once NOALYSS_INCLUDE.'/lib/class_database.php';
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';


/**
 *@brief Manage the table amortissement.amortissement_detail
 */
class Amortissement_Detail_Sql
{
  /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

  protected $variable=array("ad_id"=>"ad_id","ad_percentage"=>"ad_percentage"
			    ,"ad_year"=>"ad_year"
			    ,"ad_amount"=>"ad_amount"
			    ,"a_id"=>"a_id"
			    );
  function __construct ( & $p_cn,$p_id=-1)
  {
    $this->cn=$p_cn;
    $this->ad_id=$p_id;

    if ( $p_id == -1 )
      {
	/* Initialize an empty object */
	foreach ($this->variable as $key=>$value) $this->$value='';
	$this->ad_id=$p_id;
      }
    else
      {
	/* load it */

	$this->load();
      }
  }
  public function get_parameter($p_string)
  {
    if ( array_key_exists($p_string,$this->variable) )
      {
	$idx=$this->variable[$p_string];
	return $this->$idx;
      }
    else
      throw new Exception (__FILE__.":".__LINE__.$p_string.'Erreur attribut inexistant');
  }
  public function set_parameter($p_string,$p_value)
  {
    if ( array_key_exists($p_string,$this->variable) )
      {
	$idx=$this->variable[$p_string];
	$this->$idx=$p_value;
      }
    else
      throw new Exception (__FILE__.":".__LINE__.$p_string.'Erreur attribut inexistant');
  }
  public function get_info()
  {
    return var_export($this,true);
  }
  public function verify()
  {
    // Verify that the elt we want to add is correct
    /* verify only the datatype */
    if ( settype($this->ad_percentage,'float') == false )
      throw new Exception('DATATYPE ad_percentage $this->ad_percentage non numerique');
    if ( settype($this->ad_year,'float') == false )
      throw new Exception('DATATYPE ad_year $this->ad_year non numerique');
    if ( settype($this->ad_amount,'float') == false )
      throw new Exception('DATATYPE ad_amount $this->ad_amount non numerique');
    if ( settype($this->a_id,'float') == false )
      throw new Exception('DATATYPE a_id $this->a_id non numerique');


  }
  public function save()
  {
    /* please adapt */
    if (  $this->ad_id == -1 )
      $this->insert();
    else
      $this->update();
  }
  /**
   *@brief retrieve array of object thanks a condition
   *@param $cond condition (where clause) (optional by default all the rows are fetched)
   * you can use this parameter for the order or subselect
   *@param $p_array array for the SQL stmt
   *@see Database::get_array
   *@return an empty array if nothing is found
   */
  public function seek($cond='',$p_array=null)
  {
    $sql="select * from amortissement.amortissement_detail  $cond";
    $aobj=array();
    $array= $this->cn->get_array($sql,$p_array);
    // map each row in a object
    $size=$this->cn->count();
    if ( $size == 0 ) return $aobj;
    for ($i=0; $i<$size; $i++)
      {
	$oobj=new Amortissement_Detail_Sql ($this->cn);
	foreach ($array[$i] as $idx=>$value)
	  {
	    $oobj->$idx=$value;
	  }
	$aobj[]=clone $oobj;
      }
    return $aobj;
  }
  public function insert()
  {
    if ( $this->verify() != 0 ) return;
    if ( $this->ad_id==-1 )
      {
	/*  please adapt */
	$sql="insert into amortissement.amortissement_detail(ad_percentage
                     ,ad_year
                     ,ad_amount
                     ,a_id
                     ) values ($1
                     ,$2
                     ,$3
                     ,$4
                     ) returning ad_id";

	$this->ad_id=$this->cn->get_value(
					  $sql,
					  array( $this->ad_percentage
						 ,$this->ad_year
						 ,$this->ad_amount
						 ,$this->a_id
						 )
					  );
      }
    else
      {
	$sql="insert into amortissement.amortissement_detail(ad_percentage
                     ,ad_year
                     ,ad_amount
                     ,a_id
                     ,ad_id) values ($1
                     ,$2
                     ,$3
                     ,$4
                     ,$5
                     ) returning ad_id";

	$this->ad_id=$this->cn->get_value(
					  $sql,
					  array( $this->ad_percentage
						 ,$this->ad_year
						 ,$this->ad_amount
						 ,$this->a_id
						 ,$this->ad_id)
					  );

      }

  }

  public function update()
  {
    if ( $this->verify() != 0 ) return;
    /*   please adapt */
    $sql=" update amortissement.amortissement_detail set ad_percentage = $1
                 ,ad_year = $2
                 ,ad_amount = $3
                 ,a_id = $4
                 where ad_id= $5";
    $res=$this->cn->exec_sql(
			     $sql,
			     array($this->ad_percentage
				   ,$this->ad_year
				   ,$this->ad_amount
				   ,$this->a_id
				   ,$this->ad_id)
			     );

  }
  /**
   *@brief load a object
   *@return 0 on success -1 the object is not found
   */
  public function load()
  {

    $sql="select ad_percentage
                 ,ad_year
                 ,ad_amount
                 ,a_id
                 from amortissement.amortissement_detail where ad_id=$1";
    /* please adapt */
    $res=$this->cn->get_array(
			      $sql,
			      array($this->ad_id)
			      );

    if ( count($res) == 0 )
      {
	/* Initialize an empty object */
	foreach ($this->variable as $key=>$value) $this->$key='';

	return -1;
      }
    foreach ($res[0] as $idx=>$value)
      {
	$this->$idx=$value;
      }
    return 0;
  }

  public function delete()
  {
    $sql="delete from amortissement.amortissement_detail where ad_id=$1";
    $res=$this->cn->exec_sql($sql,array($this->ad_id));
  }
  /**
   * Unit test for the class
   */
  static function test_me()
  {

  }

}
// Amortissement_Detail_Sql::test_me();
?>

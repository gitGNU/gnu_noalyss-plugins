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
 * 
 * Copyright 2010 De Bontridder Dany <dany@alchimerys.be>

*/
  /**
   *@file
   *@brief Manage the table amortissement.amortissement
   *
   *
   Example
   @code

   @endcode
  */
require_once('class_database.php');
require_once('ac_common.php');


/**
 *@brief Manage the table amortissement.amortissement
 */
class Amortissement_Sql
{
  /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

  protected $variable=array("a_id"=>"a_id","f_id"=>"f_id"
			    ,"account_deb"=>"account_deb"
			    ,"account_cred"=>"account_cred"
			    ,"a_start"=>"a_start"
			    ,"a_amount"=>"a_amount"
			    ,"a_nb_year"=>"a_nb_year"
			    ,"a_visible"=>"a_visible"
			    ,"a_date"=>"a_date"
			    );
  function __construct ( & $p_cn,$p_id=-1)
  {
    $this->cn=$p_cn;
    $this->a_id=$p_id;

    if ( $p_id == -1 )
      {
	/* Initialize an empty object */
	foreach ($this->variable as $key=>$value) $this->$value='';
	$this->a_id=$p_id;
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
    if ( settype($this->f_id,'float') == false )
      throw new Exception('DATATYPE f_id $this->f_id non numerique');
    if ( settype($this->a_start,'float') == false )
      throw new Exception('DATATYPE a_start $this->a_start non numerique');
    if ( settype($this->a_amount,'float') == false )
      throw new Exception('DATATYPE a_amount $this->a_amount non numerique');
    if ( settype($this->a_nb_year,'float') == false )
      throw new Exception('DATATYPE a_nb_year $this->a_nb_year non numerique');


  }
  public function save()
  {
    /* please adapt */
    if (  $this->a_id == -1 )
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
    $sql="select * from amortissement.amortissement  $cond";
    $aobj=array();
    $array= $this->cn->get_array($sql,$p_array);
    // map each row in a object
    $size=$this->cn->count();
    if ( $size == 0 ) return $aobj;
    for ($i=0; $i<$size; $i++)
      {
	$oobj=new Amortissement_Sql ($this->cn);
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
    if ( $this->a_id==-1 )
      {
	/*  please adapt */
	$sql="insert into amortissement.amortissement(f_id
                     ,account_deb
                     ,account_cred
                     ,a_start
                     ,a_amount
                     ,a_nb_year
                     ,a_visible
		     ,a_date
                     ) values ($1
                     ,$2
                     ,$3
                     ,$4
                     ,$5
                     ,$6
                     ,$7
		     ,to_date($8,'DD.MM.YYYY')
                     ) returning a_id";

	$this->a_id=$this->cn->get_value(
					 $sql,
					 array( $this->f_id
						,$this->account_deb
						,$this->account_cred
						,$this->a_start
						,$this->a_amount
						,$this->a_nb_year
						,$this->a_visible
						,$this->a_date
						)
					 );
      }
    else
      {
	$sql="insert into amortissement.amortissement(f_id
                     ,account_deb
                     ,account_cred
                     ,a_start
                     ,a_amount
                     ,a_nb_year
                     ,a_visible
                     ,a_id
		     ,a_date) values ($1
                     ,$2
                     ,$3
                     ,$4
                     ,$5
                     ,$6
                     ,$7
                     ,$8
		     ,to_date($9,'DD.MM.YYYY')
                     ) returning a_id";

	$this->a_id=$this->cn->get_value(
					 $sql,
					 array( $this->f_id
						,$this->account_deb
						,$this->account_cred
						,$this->a_start
						,$this->a_amount
						,$this->a_nb_year
						,$this->a_visible
						,$this->a_id
						,$this->a_date)
					 );

      }

  }

  public function update()
  {
    if ( $this->verify() != 0 ) return;
    /*   please adapt */
    $sql=" update amortissement.amortissement set f_id = $1
                 ,account_deb = $2
                 ,account_cred = $3
                 ,a_start = $4
                 ,a_amount = $5
                 ,a_nb_year = $6
                 ,a_visible = $7
		 ,a_date=to_date($8,'DD.MM.YYYY')
                 where a_id= $9";
    $res=$this->cn->exec_sql(
			     $sql,
			     array($this->f_id
				   ,$this->account_deb
				   ,$this->account_cred
				   ,$this->a_start
				   ,$this->a_amount
				   ,$this->a_nb_year
				   ,$this->a_visible
				   ,$this->a_date
				   ,$this->a_id)
			     );

  }
  /**
   *@brief load a object
   *@return 0 on success -1 the object is not found
   */
  public function load()
  {

    $sql="select f_id
                 ,account_deb
                 ,account_cred
                 ,a_start
                 ,a_amount
                 ,a_nb_year
                 ,a_visible
		 ,to_char(a_date,'DD.MM.YYYY') as a_date
                 from amortissement.amortissement where a_id=$1";
    /* please adapt */
    $res=$this->cn->get_array(
			      $sql,
			      array($this->a_id)
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
    $sql="delete from amortissement.amortissement where a_id=$1";
    $res=$this->cn->exec_sql($sql,array($this->a_id));
  }
  /**
   * Unit test for the class
   */
  static function test_me()
  {

  }

}
// Amortissement_Sql::test_me();
?>

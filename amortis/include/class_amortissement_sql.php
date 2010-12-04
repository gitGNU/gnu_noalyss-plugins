<?php
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

  protected $variable=array("a_id"=>"a_id",
			    "f_id"=>"f_id"
			    ,"account_deb"=>"account_deb"
			    ,"account_cred"=>"account_cred"
			    ,"a_start"=>"a_start"
			    ,"a_amount"=>"a_amount"
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
    if ( settype($this->a_amount,'float') == false )
      throw new Exception('DATATYPE a_amount $this->a_amount non numerique');


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
    $ret= $this->cn->exec_sql($sql,$p_array);
    // map each row in a object
    $size=$this->cn->count();
    if ( $size == 0 ) return 0;

    return $ret;
  }
  public function get_seek($ret,$i)
  {
    if ( $ret == 0 ) throw new Exception ('get_seek nothing is found');
    $oobj=new Amortissement ($this->cn);
    $array=Database::fetch_array($ret,$i);

    foreach ($array as $idx=>$value)
      {
	$oobj->$idx=$value;
      }
    $aobj[]=clone $oobj;
    
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
                     ) values ($1
                     ,$2
                     ,$3
                     ,to_date($4,'DD.MM.YYYY')
                     ,$5
                     ) returning a_id";

	$this->a_id=$this->cn->get_value(
					 $sql,
					 array( $this->f_id
						,$this->account_deb
						,$this->account_cred
						,$this->a_start
						,$this->a_amount
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
                     ,a_id) values ($1
                     ,$2
                     ,$3
                     ,to_date($4,'DD.MM.YYYY')
                     ,$5
                     ,$6
                     ) returning a_id";

	$this->a_id=$this->cn->get_value(
					 $sql,
					 array( $this->f_id
						,$this->account_deb
						,$this->account_cred
						,$this->a_start
						,$this->a_amount
						,$this->a_id)
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
                 ,a_start =to_date($4,'DD.MM.YYYY')
                 ,a_amount = $5
                 where a_id= $6";
    $res=$this->cn->exec_sql(
			     $sql,
			     array($this->f_id
				   ,$this->account_deb
				   ,$this->account_cred
				   ,$this->a_start
				   ,$this->a_amount
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
                 ,to_char(a_start,'DD.MM.YYYY') as a_start
                 ,a_amount
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
// Amortissement::test_me();
?>

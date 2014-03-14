<?php
/**
 *@file
 *@brief Manage the table importbank.format_bank
 *
 *
 Example
 @code

 @endcode
*/
require_once('class_database.php');
require_once('ac_common.php');


/**
 *@brief Manage the table importbank.format_bank
 */
class Format_Bank_sql
    {
        /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

        protected $variable=array("id"=>"id","format_name"=>"format_name"
                                  ,"jrn_def_id"=>"jrn_def_id"
				  ,"pos_lib"=>"pos_lib"
				  ,"pos_amount"=>"pos_amount"
				  ,"pos_date"=>"pos_date"
				  ,"pos_operation_nb"=>"pos_operation_nb"
				  ,"pos_third"=>"pos_third"
				  ,"pos_extra"=>"pos_extra"
				  ,"sep_decimal"=>"sep_decimal"
				  ,"sep_thousand"=>"sep_thousand"
				  ,"sep_field"=>"sep_field"
				  ,"format_date"=>"format_date"
				  ,"nb_col"=>"nb_col"
				  ,"skip"=>"skip"
                                 );
        function __construct ( & $p_cn,$p_id=-1)
            {
            $this->cn=$p_cn;
            $this->id=$p_id;

            if ( $p_id == -1 )
                {
                /* Initialize an empty object */
                foreach ($this->variable as $key=>$value) $this->$value=NULL;
                $this->id=$p_id;
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
	      if ( $this->jrn_def_id !==null && settype($this->jrn_def_id,'float') == false )
                throw new Exception('DATATYPE jrn_def_id $this->jrn_def_id non numerique');
	      if ( $this->pos_lib !== null && settype($this->pos_lib,'float') == false )
                throw new Exception('DATATYPE pos_lib $this->pos_lib non numerique');
            if ( $this->pos_amount !== null && settype($this->pos_amount,'float') == false )
                throw new Exception('DATATYPE pos_amount $this->pos_amount non numerique');
            if ( $this->pos_date !== null && settype($this->pos_date,'float') == false )
                throw new Exception('DATATYPE pos_date $this->pos_date non numerique');
            if ( $this->pos_operation_nb !== null && settype($this->pos_operation_nb,'float') == false )
                throw new Exception('DATATYPE pos_operation_nb $this->pos_operation_nb non numerique');
            if ($this->nb_col !== null &&  settype($this->nb_col,'float') == false )
                throw new Exception('DATATYPE nb_col $this->nb_col non numerique');

            }
        public function save()
            {
            /* please adapt */
            if (  $this->id == -1 )
                $this->insert();
            else
                $this->update();
            }
        /**
         *@brief retrieve array of object thanks a condition
         *@param $cond condition (where clause) (optional by default all the rows are fetched)
         * you can use this parameter for the order or subselect
         *@param $p_array array for the SQL stmt
         *@see Database::exec_sql get_object  Database::num_row
         *@return the return value of exec_sql
         */
        public function seek($cond='',$p_array=null)
            {
            $sql="select * from importbank.format_bank  $cond";
            $aobj=array();
            $ret= $this->cn->exec_sql($sql,$p_array);
            return $ret;
            }
        /**
         *get_seek return the next object, the return of the query must have all the column
         * of the object
         *@param $p_ret is the return value of an exec_sql
         *@param $idx is the index
         *@see seek
         *@return object
         */
        public function get_object($p_ret,$idx)
            {
            // map each row in a object
            $oobj=new Format_Bank_sql ($this->cn);
            $array=Database::fetch_array($p_ret,$idx);
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
	    if ( strlen(trim($this->format_name))==0)
	      {
		$get_id=$this->cn->get_value('select id from importbank.format_name where format_name=$1',array($this->format_name));
		if ( $this->cn->size() == 1) {
		  $this->update();
		  return;
		}
		
	      }
            if ( $this->id==-1 )
                {
                /*  please adapt */
                $sql="insert into importbank.format_bank(format_name
                     ,jrn_def_id
                     ,pos_lib
                     ,pos_amount
                     ,pos_date
                     ,pos_operation_nb
                     ,pos_third
		     ,pos_extra
                     ,sep_decimal
                     ,sep_thousand
                     ,sep_field
                     ,format_date
                     ,nb_col
		     ,skip
                     ) values ($1
                     ,$2
                     ,$3
                     ,$4
                     ,$5
                     ,$6
                     ,$7
                     ,$8
                     ,$9
                     ,$10
                     ,$11
		     ,$12
		     ,$13
		     ,$14
                     ) returning id";

                $this->id=$this->cn->get_value(
                              $sql,
                              array( $this->format_name
                                     ,$this->jrn_def_id
                                     ,$this->pos_lib
                                     ,$this->pos_amount
                                     ,$this->pos_date
                                     ,$this->pos_operation_nb
				     ,$this->pos_third
				     ,$this->pos_extra
                                     ,$this->sep_decimal
                                     ,$this->sep_thousand
                                     ,$this->sep_field
                                     ,$this->format_date
                                     ,$this->nb_col
				     ,$this->skip
                                   )
                          );
                }
            else
                {
                $sql="insert into importbank.format_bank(format_name
                     ,jrn_def_id
                     ,pos_lib
                     ,pos_amount
                     ,pos_date
                     ,pos_operation_nb
                     ,pos_third
		     ,pos_extra
                     ,sep_decimal
                     ,sep_thousand
                     ,sep_field
                     ,format_date
                     ,nb_col
                     ,id
		     ,skip
		     ) values ($1
                     ,$2
                     ,$3
                     ,$4
                     ,$5
                     ,$6
                     ,$7
                     ,$8
                     ,$9
                     ,$10
                     ,$11
                     ,$12
		     ,$13
		     ,$14
		     ,$15
                     ) returning id";

                $this->id=$this->cn->get_value(
                              $sql,
                              array( $this->format_name
                                     ,$this->jrn_def_id
                                     ,$this->pos_lib
                                     ,$this->pos_amount
                                     ,$this->pos_date
                                     ,$this->pos_operation_nb
				     ,$this->pos_third
				     ,$this->pos_extra
                                     ,$this->sep_decimal
                                     ,$this->sep_thousand
                                     ,$this->sep_field
                                     ,$this->format_date
                                     ,$this->nb_col
                                     ,$this->id
				     ,$this->skip
				     )
                          );

                }

            }

        public function update()
            {
            if ( $this->verify() != 0 ) return;
            /*   please adapt */
            $sql=" update importbank.format_bank set format_name = $1
                 ,jrn_def_id = $2
                 ,pos_lib = $3
                 ,pos_amount = $4
                 ,pos_date = $5
                 ,pos_operation_nb = $6
		 ,pos_third = $7
		 ,pos_extra=$8
                 ,sep_decimal = $9
                 ,sep_thousand = $10
                 ,sep_field = $11
                 ,format_date = $12
                 ,nb_col = $13
		 ,skip=$14
                 where id= $15";
            $res=$this->cn->exec_sql(
                     $sql,
                     array($this->format_name
                           ,$this->jrn_def_id
                           ,$this->pos_lib
                           ,$this->pos_amount
                           ,$this->pos_date
                           ,$this->pos_operation_nb
			   ,$this->pos_third
			   ,$this->pos_extra
                           ,$this->sep_decimal
                           ,$this->sep_thousand
                           ,$this->sep_field
                           ,$this->format_date
                           ,$this->nb_col
			   ,$this->skip
                           ,$this->id)
                 );

            }
        /**
         *@brief load a object
         *@return 0 on success -1 the object is not found
         */
        public function load()
            {

            $sql="select format_name
                 ,jrn_def_id
                 ,pos_lib
                 ,pos_amount
                 ,pos_date
                 ,pos_operation_nb
		 ,pos_third
		 ,pos_extra
                 ,sep_decimal
                 ,sep_thousand
                 ,sep_field
                 ,format_date
                 ,nb_col
		 ,skip
                 from importbank.format_bank where id=$1";
            /* please adapt */
            $res=$this->cn->get_array(
                     $sql,
                     array($this->id)
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
            $sql="delete from importbank.format_bank where id=$1";
            $res=$this->cn->exec_sql($sql,array($this->id));
            }
        /**
         * Unit test for the class
         */
        static function test_me()
            {

            }

    }
// Format_Bank_sql::test_me();
?>

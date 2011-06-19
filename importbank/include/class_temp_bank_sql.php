<?php
/**
 *@file
 *@brief Manage the table importbank.temp_bank
 *
 *
Example
@code

@endcode
 */
require_once('class_database.php');
require_once('ac_common.php');


/**
 *@brief Manage the table importbank.temp_bank
*/
class Temp_Bank_sql
    {
        /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

        protected $variable=array("id"=>"id","tp_date"=>"tp_date"
                                  ,"jrn_def_id"=>"jrn_def_id"
				  ,"libelle"=>"libelle"
				  ,"amount"=>"amount"
				  ,"ref_operation"=>"ref_operation"
				  ,"status"=>"status"
				  ,"import_id"=>"import_id"
				  ,"tp_third"=>"tp_third"
				  ,"tp_extra"=>"tp_extra"
				  ,"f_id"=>"f_id"
				  ,"tp_rec"=>"tp_rec"
				  ,"tp_error_msg"=>"tp_error_msg"
                                 );
        function __construct ( & $p_cn,$p_id=-1)
            {
            $this->cn=$p_cn;
            $this->id=$p_id;

            if ( $p_id == -1 )
                {
                /* Initialize an empty object */
                foreach ($this->variable as $key=>$value) $this->$value=null;
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
            if ( trim($this->tp_date) == '') $this->tp_date=null;
            if ( trim($this->jrn_def_id) == '') $this->jrn_def_id=null;
            if ( $this->jrn_def_id!== null && settype($this->jrn_def_id,'float') == false )
                throw new Exception('DATATYPE jrn_def_id $this->jrn_def_id non numerique');
            if ( trim($this->libelle) == '') $this->libelle=null;
            if ( trim($this->amount) == '') $this->amount=null;
            if ( $this->amount!== null && settype($this->amount,'float') == false )
                throw new Exception('DATATYPE amount $this->amount non numerique');
            if ( trim($this->ref_operation) == '') $this->ref_operation=null;
            if ( trim($this->status) == '') $this->status=null;
            if ( trim($this->import_id) == '') $this->import_id=null;
            if ( $this->import_id!== null && settype($this->import_id,'float') == false )
                throw new Exception('DATATYPE import_id $this->import_id non numerique');
            if ( trim($this->tp_third) == '') $this->tp_third=null;
            if ( trim($this->tp_extra) == '') $this->tp_extra=null;
            if ( trim($this->f_id) == '') $this->f_id=null;
            if ( $this->f_id!== null && settype($this->f_id,'float') == false )
                throw new Exception('DATATYPE f_id $this->f_id non numerique');


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
            $sql="select * from importbank.temp_bank  $cond";
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
            $oobj=new Temp_Bank_sql ($this->cn);
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
            if ( $this->id==-1 )
                {
                /*  please adapt */
                $sql="insert into importbank.temp_bank(tp_date
                     ,jrn_def_id
                     ,libelle
                     ,amount
                     ,ref_operation
                     ,status
                     ,import_id
                     ,tp_third
                     ,tp_extra
                     ,f_id
		     ,tp_rec
		     ,tp_error_msg
                     ) values (to_date($1,'DD.MM.YYYY')
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
		     
                     ) returning id";

                $this->id=$this->cn->get_value(
                              $sql,
                              array( $this->tp_date
                                     ,$this->jrn_def_id
                                     ,$this->libelle
                                     ,$this->amount
                                     ,$this->ref_operation
                                     ,$this->status
                                     ,$this->import_id
                                     ,$this->tp_third
                                     ,$this->tp_extra
                                     ,$this->f_id
				     ,$this->tp_rec
				     ,$this->tp_error_msg

                                   )
                          );
                }
            else
                {
                $sql="insert into importbank.temp_bank(tp_date
                     ,jrn_def_id
                     ,libelle
                     ,amount
                     ,ref_operation
                     ,status
                     ,import_id
                     ,tp_third
                     ,tp_extra
                     ,f_id
                     ,id) values (to_date($1,'DD.MM.YYYY')
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
                     ) returning id";

                $this->id=$this->cn->get_value(
                              $sql,
                              array( $this->tp_date
                                     ,$this->jrn_def_id
                                     ,$this->libelle
                                     ,$this->amount
                                     ,$this->ref_operation
                                     ,$this->status
                                     ,$this->import_id
                                     ,$this->tp_third
                                     ,$this->tp_extra
                                     ,$this->f_id
                                     ,$this->id
				     ,$this->tp_rec
				     ,$this->tp_error_msg
				     )
                          );

                }

            }

        public function update()
            {
            if ( $this->verify() != 0 ) return;
            /*   please adapt */
            $sql=" update importbank.temp_bank set tp_date =to_date($1,'DD.MM.YYYY')
                 ,jrn_def_id = $2
                 ,libelle = $3
                 ,amount = $4
                 ,ref_operation = $5
                 ,status = $6
                 ,import_id = $7
                 ,tp_third = $8
                 ,tp_extra = $9
                 ,f_id = $10
		 ,tp_rec=$11
		 ,tp_error_msg=$12
                 where id= $13";
            $res=$this->cn->exec_sql(
                     $sql,
                     array($this->tp_date
                           ,$this->jrn_def_id
                           ,$this->libelle
                           ,$this->amount
                           ,$this->ref_operation
                           ,$this->status
                           ,$this->import_id
                           ,$this->tp_third
                           ,$this->tp_extra
                           ,$this->f_id
			   ,$this->tp_rec
			   ,$this->tp_error_msg
                           ,$this->id)
                 );

            }
        /**
         *@brief load a object
         *@return 0 on success -1 the object is not found
         */
        public function load()
            {

            $sql="select to_char(tp_date,'DD.MM.YYYY') as tp_date
                 ,jrn_def_id
                 ,libelle
                 ,amount
                 ,ref_operation
                 ,status
                 ,import_id
                 ,tp_third
                 ,tp_extra
                 ,f_id
		 ,tp_rec
		 ,tp_error_msg
                 from importbank.temp_bank where id=$1";
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
            $sql="delete from importbank.temp_bank where id=$1";
            $res=$this->cn->exec_sql($sql,array($this->id));
            }
        /**
         * Unit test for the class
         */
        static function test_me()
            {
            $cn=new Database(25);
            $cn->start();
            echo h2info('Test object vide');
            $obj=new Temp_Bank_sql($cn);
            var_dump($obj);

            echo h2info('Test object NON vide');
            $obj->set_parameter('j_id',3);
            $obj->load();
            var_dump($obj);

            echo h2info('Update');
            $obj->set_parameter('j_qcode','NOUVEAU CODE');
            $obj->save();
            $obj->load();
            var_dump($obj);

            echo h2info('Insert');
            $obj->set_parameter('j_id',0);
            $obj->save();
            $obj->load();
            var_dump($obj);

            echo h2info('Delete');
            $obj->delete();
            echo (($obj->load()==0)?'Trouve':'non trouve');
            var_dump($obj);
            $cn->rollback();

            }

    }
// Temp_Bank_sql::test_me();
?>

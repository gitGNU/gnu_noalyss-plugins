<?php

class Sql_Impacc
{

	function __construct($p_cn, $p_id = -1)
	{
		$this->cn = $p_cn;
		$pk=$this->primary_key;
		$this->$pk= $p_id;

		if ($p_id == -1)
		{
			/* Initialize an empty object */
			foreach ($this->name as $key )
			{
				$this->$key= null;
			}
			$this->$pk= $p_id;
		}
		else
		{
			/* load it */
			$this->load();
		}
	}

	public function getp($p_string)
	{
		if (array_key_exists( $p_string,$this->name))
		{
			$idx = $this->name[$p_string];
			return $this->$idx;
		}
		else
			throw new Exception(__FILE__ . ":" . __LINE__ . $p_string . 'Erreur attribut inexistant '.$p_string);
	}

	public function setp($p_string, $p_value)
	{
		if (array_key_exists( $p_string,$this->name))
		{
			$idx = $this->name[$p_string];
			$this->$idx = $p_value;
		}
		else
			throw new Exception(__FILE__ . ":" . __LINE__ . $p_string . 'Erreur attribut inexistant '.$p_string);
	}

	public function insert()
	{
		$this->verify();
		$sql = "insert into " . $this->table . " ( ";
		$sep = "";
		$par = "";
		$idx = 1;
		$array = array();
		foreach ($this->name as $key=>$value)
		{
			if (isset($this->default[$value]) && $this->default[$value] == "auto" && $this->$value ==null )
				continue;
			if ( $value==$this->primary_key && $this->$value == -1 ) continue;
			$sql.=$sep.$value;
			switch ($this->type[$value])
			{
				case "date":
					$par .=$sep. 'to_date($' . $idx . ",'" . $this->date_format . "')" ;
					break;
				default:
					$par .= $sep."$" . $idx ;
			}

			$array[] = $this->$value;
			$sep = ",";
			$idx++;
		}
		$sql.=") values (" . $par . ") returning " . $this->primary_key;
		$pk=$this->primary_key;
		$this->$pk = $this->cn->get_value($sql, $array);
	}

	public function delete()
	{
		$pk=$this->primary_key;
		$sql = " delete from " . $this->table . " where " . $this->primary_key . "=" . sql_string($this->$pk);
		$this->cn->exec_sql($sql);
	}

	public function update()
	{
		$this->verify();
		$pk=$this->primary_key;
		$sql = "update " . $this->table . "  ";
		$sep = "";
		$idx = 1;
		$array = array();
		$set=" set ";
		foreach ($this->name as $key=>$value)
		{
			if (isset($this->default[$value]) && $this->default[$value] == "auto"  )
				continue;
			switch ($this->type[$value])
			{
				case "date":
					$par =$value. '=to_date($' . $idx . ",'" . $this->date_format . "')" ;
					break;
				default:
					$par = $value."= $" . $idx ;
			}
			$sql.=$sep." $set " . $par ;
			$array[] = $this->$value;
			$sep = ",";$set="";$idx++;
		}
		$sql.=" where " . $this->primary_key . " =" . $this->$pk;
	    $this->cn->exec_sql($sql, $array);

	}

	public function load()
	{
		$sql = " select ";
		$sep="";$par="";

		foreach ($this->name as $key)
		{

			switch ($this->type[$key])
			{
				case "date":
					$sql .= $sep.'to_char(' . $key . ",'" . $this->date_format . "') as ".$key ;
					break;
				default:
					$sql.=$sep.$key ;
			}
			$sep = ",";
		}
		$pk=$this->primary_key;
		$sql.=" from ".$this->table;
		$sql.=" where " . $this->primary_key . " = " . $this->$pk;
		$result = $this->cn->get_array($sql);
		if ($this->cn->count() == 0 ) {
			$this->$pk=-1;
			return;
		}

		foreach ($result[0] as $key=>$value) {
			$this->$key=$value;
		}
	}

	public function get_info()
	{
		return var_export($this, true);
	}

	public function verify()
	{
		foreach($this->name as $key){
			if ( trim($this->$key)=='') $this->$key=null;
		}
		return 0;
	}
	public function from_array($p_array)
	{
		foreach ($this->name as $key=>$value)
		{
			if ( isset ($p_array[$value]))
			{
				$this->$value=$p_array[$value];
			}
		}
	}
	public function next($ret,$i) {
		global $cn;
		$array=$this->cn->fetch_array($ret,$i);
		$this->from_array($array);
	}

}

class impacc_operation_sql extends sql_impacc
{

	function __construct($p_id=-1)
	{
		$this->table = "impacc.operation";
		$this->primary_key = "o_id";

		$this->name=array(
			"id"=>"o_id",
			"dolibarr"=>"o_doli",
			"date"=>"o_date",
			"qcode"=>"o_qcode",
			"fiche"=>"f_id",
			"desc"=>"o_label",
			"pj"=>"o_pj",
			"amount_unit"=>"amount_unit",
			"amount_vat"=>"amount_vat",
			"number_unit"=>"number_unit",
			"rate"=>"vat_rate",
			"amount_total"=>"amount_total",
			"jrn_def_id"=>"jrn_def_id",
			"o_message"=>"o_message",
			"import_id"=>"i_id",
			"status"=>"o_status"

		);

		$this->type = array(
			"o_id"=>"numeric",
			"o_doli"=>"numeric",
			"o_date"=>"date",
			"o_qcode"=>"text",
			"f_id"=>"numeric",
			"o_label"=>"text",
			"o_pj"=>"text",
			"amount_unit"=>"numeric",
			"amount_vat"=>"numeric",
			"number_unit"=>"numeric",
			"vat_rate"=>"numeric",
			"amount_total"=>"numeric",
			"jrn_def_id"=>"numeric",
			"o_message"=>"text",
			"i_id"=>"numeric",
			"o_status"=>"text"
			);

		$this->default = array(
			"o_id" => "auto",
		);
		$this->date_format = "DD.MM.YYYY";
		global $cn;

		parent::__construct($cn,$p_id);
	}

}

class impacc_operation_tmp_sql extends sql_impacc
{

	function __construct($p_id=-1)
	{
		$this->table = "impacc.operation_tmp";
		$this->primary_key = "o_id";

		$this->name=array(
			"id"=>"o_id",
			"dolibarr"=>"o_doli",
			"date"=>"o_date",
			"qcode"=>"o_qcode",
			"fiche"=>"f_id",
			"desc"=>"o_label",
			"pj"=>"o_pj",
			"amount_unit"=>"amount_unit",
			"amount_vat"=>"amount_vat",
			"number_unit"=>"number_unit",
			"rate"=>"vat_rate",
			"amount_total"=>"amount_total",
			"jrn_def_id"=>"jrn_def_id",
			"message"=>"o_message",
			"import_id"=>"i_id",
			"code"=>"o_result",
			"tva_id"=>"tva_id",
			"type"=>"o_type",
			"poste"=>"o_poste"

		);

		$this->type = array(
			"o_id"=>"numeric",
			"o_doli"=>"text",
			"o_date"=>"text",
			"o_qcode"=>"text",
			"f_id"=>"text",
			"o_label"=>"text",
			"o_pj"=>"text",
			"amount_unit"=>"text",
			"amount_vat"=>"text",
			"number_unit"=>"text",
			"vat_rate"=>"text",
			"amount_total"=>"text",
			"jrn_def_id"=>"text",
			"o_message"=>"text",
			"i_id"=>"numeric",
			"o_result"=>'text',
			"tva_id"=>'numeric',
			"o_type"=>'text',
			"o_poste"=>"text"
			);

		$this->default = array(
			"o_id" => "auto",
		);
		$this->date_format = "DD.MM.YYYY";
		global $cn;

		parent::__construct($cn,$p_id);
	}

}

class impacc_import_sql extends sql_impacc
{
	function __construct($p_id=-1)
	{
		$this->table = "impacc.import";
		$this->primary_key = "i_id";

		$this->name=array(
			"id"=>"i_id",
			"send_file"=>"send_file",
			"temp_file"=>"temp_file",
			"date"=>"i_date",
			"nbrow"=>"i_row"
		);

		$this->type = array(
			"i_id"=>"numeric",
			"send_file"=>"text",
			"temp_file"=>"text",
			"i_date"=>"date",
			"i_row"=>"numeric"
			);

		$this->default = array(
			"i_id" => "auto",
			"i_date" => "auto"
		);
		$this->date_format = "DD.MM.YYYY";
		global $cn;

		parent::__construct($cn,$p_id);
	}
}


class impacc_operation_transfer_sql extends sql_impacc
{
	function __construct($p_id=-1)
	{
		$this->table = "impacc.operation_transfer";
		$this->primary_key = "ot_id";

		$this->name=array(
			"id"=>"ot_id",
			"j_id"=>"j_id",
			"o_id"=>"o_id"
		);

		$this->type = array(
			"ot_id"=>'numeric',
			"j_id"=>'numeric',
			"o_id"=>'numeric',
			);

		$this->default = array(
			"i_id" => "auto"
		);
		$this->date_format = "DD.MM.YYYY";
		global $cn;

		parent::__construct($cn,$p_id);
	}
}
?>
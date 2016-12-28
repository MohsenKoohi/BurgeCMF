<?php
class Constant_manager_model extends CI_Model
{
	private $constant_table_name="constant";
	
	public function __construct()
	{
		parent::__construct();

		return;
	}

	public function install()
	{

		$constant_table=$this->db->dbprefix($this->constant_table_name); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $constant_table (
				`constant_key` VARCHAR(100) NOT NULL
				,`constant_value` VARCHAR(1000) NOT NULL
				,PRIMARY KEY (constant_key)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("constant","");
		$this->module_manager_model->add_module_names_from_lang_file("constant");
		
		return;
	}

	public function uninstall()
	{
		return;
	}

	public function get_all()
	{
		$this->db->from($this->constant_table_name);
		$this->db->order_by("constant_key ASC");
		$result=$this->db->get();

		return $result->result_array();
	}

	public function set($key,$value)
	{
		$props=array(
			"constant_key"=>$key
			,"constant_value"=>$value
		);

		$this->db->replace($this->constant_table_name,$props);

		$this->log_manager_model->info("CONSTANT_SET",$props);

		return;
	}

	public function delete($key)
	{
		$this->db->where("constant_key",$key);
		$this->db->delete($this->constant_table_name);

		$this->log_manager_model->info("CONSTANT_UNSET",array("constant_key"=>$key));

		return;
	}

	public function get($key)
	{
		$result=$this->db->get_where($this->constant_table_name,array("constant_key"=>$key));
		$row=$result->row_array();

		if($row)
			return $row['constant_value'];

		return FALSE;
	}

	public function get_array($keys)
	{
		return $this->db
			->from($this->constant_table_name)
			->where_in("constant_key",$keys)
			->get()
			->result_array();
	}

}
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

		return;
	}

	public function get($key)
	{
		$this->db->from($this->post_table_name);
		$this->db->join($this->post_content_table_name,"post_id = pc_post_id","left");
		
		$this->set_post_query_filter($filter);
		
		$this->db->order_by("post_id DESC");
		$results=$this->db->get();

		return $results->result_array();
	}

}

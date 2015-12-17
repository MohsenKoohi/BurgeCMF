<?php
class Example_manager_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		return;
	}

	public function install()
	{
		$table=$this->db->dbprefix('example'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS  $table(
				
				PRIMARY KEY (user_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("example","example_manager");
		$this->module_manager_model->add_module_name("example","fa","example");
		$this->module_manager_model->add_module_name("example","en","example");

		return;
	}

	public function uninstall()
	{
		return;

	}
	
	public function get_dashbord_info()
	{
		return "";
		$CI=& get_instance();
		$lang=$CI->language->get();
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("hit_counter_dashboard"),$data,TRUE);
		
		return $ret;		
	}


	public function get_sidebar_text($module_id)
	{
		return "";
	}



}
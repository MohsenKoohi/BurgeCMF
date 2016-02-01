<?php

class Category_manager_model extends CI_Model
{
	private $category_table_name="category";

	public function __construct()
	{
		parent::__construct();

      return;
   }


	public function install()
	{
		$tbl_name=$this->db->dbprefix($this->category_table_name); 

		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $tbl_name (
				`category_id` INT AUTO_INCREMENT
				,`category_parent_id` INT DEFAULT 0
				,PRIMARY KEY (category_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("category","category_manager");
		$this->module_manager_model->add_module_names_from_lang_file("category");
		
		return;
	}

	public function uninstall()
	{
		return;
	}

	public function get_dashboard_info()
	{
		return "";
		$CI=& get_instance();
		$lang=$CI->language->get();
		$CI->lang->load('ae_log',$lang);		
		
		$data=array();
		$data['logs']=$this->get_today_logs(2,8);
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("log_dashboard"),$data,TRUE);
		
		return $ret;		
	}	
}
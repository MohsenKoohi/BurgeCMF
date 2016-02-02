<?php

class Category_manager_model extends CI_Model
{
	private $category_table_name="category";
	private $category_description_table_name="category_description";

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

		$tbl_name=$this->db->dbprefix($this->category_description_table_name); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $tbl_name (
				`cd_category_id` INT NOT NULL
				,`cd_lang_id` CHAR(2) NOT NULL
				,`cd_name` VARCHAR(512)
				,`cd_description` TEXT
				,`cd_meta_key_words` VARCHAR(1024)
				,`cd_meta_description` VARCHAR(1024)
				,`cd_url` varchar(1024)

				,PRIMARY KEY (cd_category_id, cd_lang_id)	
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

	public function add()
	{
		$this->db->insert($this->category_table_name,array("category_parent_id"=>0));
		
		$category_id=$this->db->insert_id();

		$category_descs=array();
		foreach($this->language->get_languages() as $index=>$lang)
			$category_descs[]=array(
				"cd_category_id"=>$category_id
				,"cd_lang_id"=>$index
			);

		$this->db->insert_batch($this->category_description_table_name,$category_descs);

		return $category_id;
	}
}
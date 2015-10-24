<?php
class Post_manager_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();

		return;
	}

	public function install()
	{

		$post_table=$this->db->dbprefix('post'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $post_table (
				`post_id` INT  NOT NULL AUTO_INCREMENT
				,`post_creator_uid` INT NOT NULL DEFAULT 0
				,`post_active` TINYINT NOT NULL DEFAULT 1
				,`post_allow_comment` TINYINT NOT NULL DEFAULT 0
				,`post_publication_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
				,`post_comment_count` INT NOT NULL DEFAULT 0
				,PRIMARY KEY (post_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$post_content_table=$this->db->dbprefix('post_content'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $post_content_table (
				`pc_post_id` INT  NOT NULL
				,`pc_lang_id` CHAR(2) NOT NULL
				,`pc_active` TINYINT NOT NULL DEFAULT 1
				,`pc_content` MEDIUMTEXT
				,`pc_title`	 TEXT
				,`pc_keywords` TEXT
				,`pc_description` TEXT
				,PRIMARY KEY (pc_post_id, pc_lang_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("post","post_manager");
		$this->module_manager_model->add_module_names_from_lang_file("post");
		
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
}

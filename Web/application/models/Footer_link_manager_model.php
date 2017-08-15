<?php
class Footer_link_manager_model extends CI_Model
{
	private $footer_link_table_name="footer_link";
	
	public function __construct()
	{
		parent::__construct();

		return;
	}

	public function install()
	{
		$tbl=$this->db->dbprefix($this->footer_link_table_name); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $tbl (
				`fl_id` INT  NOT NULL AUTO_INCREMENT
				,`fl_parent_id` INT
				,`fl_title` VARCHAR(511)
				,`fl_link` VARCHAR(2047)
				,PRIMARY KEY (fl_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("footer_link","footer_link_manager");
		$this->module_manager_model->add_module_names_from_lang_file("footer_link");
		
		return;
	}

	public function uninstall()
	{
		return;
	}
	
	public function add_post()
	{
		$user=$this->user_manager_model->get_user_info();

		$props=array(
			"post_date"=>get_current_time()
			,"post_creator_uid"=>$user->get_id()
		);

		$this->db->insert($this->post_table_name,$props);
		
		$new_post_id=$this->db->insert_id();
		$props['post_id']=$new_post_id;

		$this->log_manager_model->info("POST_ADD",$props);	

		$post_contents=array();
		foreach($this->language->get_languages() as $index=>$lang)
			$post_contents[]=array(
				"pc_post_id"=>$new_post_id
				,"pc_lang_id"=>$index
			);
		$this->db->insert_batch($this->post_content_table_name,$post_contents);

		return $new_post_id;
	}

	public function get_links($lang_id=NULL)
	{
		$this->db
			->select("*")
			->from($this->footer_link_table_name);

		if(0 && $lang_id)
			$this->db->where("fl_lang_id", $lang_id);

		$result=$this->db
			->order_by("fl_id")
			->get()
			->result_array();

		$ret=array();
		foreach($result as $r)
		{
			$parent_id=$r['fl_parent_id'];
			if(!isset($ret[$parent_id]))
				$ret[$parent_id]=array(
					'title'			=> ''
					,'link'			=> ''
					,'children'		=> array()
				);
			$parent_node=&$ret[$parent_id];

			$id=$r['fl_id'];
			if(!isset($ret[$id]))
				$ret[$id]=array(
					'title'			=> ''
					,'link'			=> ''
					,'children'		=> array()
				);
			$node=&$ret[$id];

			$node['title']=$r['fl_title'];
			$node['link']=$r['fl_link'];
			$parent_node['children'][$id]=&$node;

		}

		//bprint_r($ret);exit

		return $ret;
	}

	public function set_links($ins)
	{
		$this->db
			->where("fl_id >=",0)
			->delete($this->footer_link_table_name);

		$this->db->insert_batch($this->footer_link_table_name, $ins);
		
		return;
	}
}

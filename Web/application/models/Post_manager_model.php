<?php
class Post_manager_model extends CI_Model
{
	private $post_table_name="post";
	private $post_content_table_name="post_content";
	private $post_writable_props=array(
		"post_active","post_allow_comment"
	);
	private $post_content_writable_props=array(
		"pc_active","pc_keywords","pc_description","pc_title","pc_content"
		);

	public function __construct()
	{
		parent::__construct();

		return;
	}

	public function install()
	{

		$post_table=$this->db->dbprefix($this->post_table_name); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $post_table (
				`post_id` INT  NOT NULL AUTO_INCREMENT
				,`post_creator_uid` INT NOT NULL DEFAULT 0
				,`post_active` TINYINT NOT NULL DEFAULT 0
				,`post_allow_comment` TINYINT NOT NULL DEFAULT 0
				,`post_comment_count` INT NOT NULL DEFAULT 0
				,PRIMARY KEY (post_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$post_content_table=$this->db->dbprefix($this->post_content_table_name); 
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

	public function add_post()
	{
		$user=$this->user_manager_model->get_user_info();

		$props=array(
			"post_creator_uid"=>$user->get_id()
		);

		$this->db->insert($this->post_table_name,$props);
		
		$new_post_id=$this->db->insert_id();
		$props['post_id']=$new_post_id;

		$this->log_manager_model->info("POST_ADD",$props);	

		$post_contents=array();
		foreach($this->language->get_languages() as $index=>$lang)
			$post_content[]=array(
				"pc_post_id"=>$new_post_id
				,"pc_lang_id"=>$index
			);
		$this->db->insert_batch($this->post_content_table_name,$post_content);

		return $new_post_id;
	}

	public function get_posts($filter)
	{
		$this->db->from($this->post_table_name);
		$this->db->join($this->post_content_table_name,"post_id = pc_post_id","left");
		
		$this->set_post_query_filter($filter);
		
		$this->db->order_by("post_id DESC");
		$results=$this->db->get();

		return $results->result_array();
	}

	private function set_post_query_filter($filter)
	{
		if(isset($filter['lang']))
			$this->db->where("pc_lang_id",$filter['lang']);

		if(isset($filter['lang']))
			$this->db->group_by("post_id");
	
		return;
	}

	public function get_post($post_id)
	{
		return $this->db
			->select("post.* , post_content.* , user_id, user_name ")
			->from("post")
			->join("user","post_creator_uid = user_id","left")
			->join("post_content","post_id = pc_post_id","left")
			->where("post_id",$post_id)
			->get()
			->result_array();
	}

	public function set_post_props($post_id, $props, $post_contents)
	{
		$props=select_allowed_elements($props,$this->post_writable_props);

		if($props)
		{
			foreach ($props as $prop => $value)
				$this->db->set($prop,$value);

			$this->db
				->where("post_id",$post_id)
				->update($this->post_table_name);
		}

		foreach($post_contents as $content)
		{
			$lang=$content['pc_lang_id'];

			$content=select_allowed_elements($content,$this->post_content_writable_props);
			if(!$content)
				continue;

			foreach($content as $prop => $value)
			{
				$this->db->set($prop,$value);
				$props[$lang."_".$prop]=$value;
			}

			$this->db
				->where("pc_post_id",$post_id)
				->where("pc_lang_id",$lang)
				->update($this->post_content_table_name);
		}

		$this->log_manager_model->info("POST_CHANGE",$props);	

		return;

	}

	public function delete_post($post_id)
	{
		$props=array("post_id"=>$post_id);

		$this->db
			->where("post_id",$post_id)
			->delete($this->post_table_name);

		$this->db
			->where("pc_post_id",$post_id)
			->delete($this->post_content_table_name);
		
		$this->log_manager_model->info("POST_DELETE",$props);	

		return;

	}
}

<?php
class Post_manager_model extends CI_Model
{
	private $post_table_name="post";
	private $post_content_table_name="post_content";
	private $post_category_table_name="post_category";
	private $post_writable_props=array(
		"post_date","post_active","post_allow_comment"
	);
	private $post_content_writable_props=array(
		"pc_active","pc_image","pc_keywords","pc_description","pc_title","pc_content","pc_gallery"
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
				,`post_date` DATETIME  
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
				,`pc_image` VARCHAR(1024) NULL
				,`pc_content` MEDIUMTEXT
				,`pc_title`	 TEXT
				,`pc_keywords` TEXT
				,`pc_description` TEXT
				,`pc_gallery` TEXT
				,PRIMARY KEY (pc_post_id, pc_lang_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$post_category_table=$this->db->dbprefix($this->post_category_table_name); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $post_category_table (
				`pcat_post_id` INT  NOT NULL
				,`pcat_category_id` INT NOT NULL
				,PRIMARY KEY (pcat_post_id, pcat_category_id)	
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
	
	public function get_dashboard_info()
	{
		$CI=& get_instance();
		$lang=$CI->language->get();

		$CI->lang->load('ae_post',$lang);
			
		$data=$this->get_statistics();

		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("post_dashboard"),$data,TRUE);
		
		return $ret;		
	}

	private function get_statistics()
	{
		$tb=$this->db->dbprefix($this->post_table_name);

		return $this->db->query("
			SELECT 
				(SELECT COUNT(*) FROM $tb) as total, 
				(SELECT COUNT(*) FROM $tb WHERE post_active) as active
			")->row_array();
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
		$this->db->join($this->post_category_table_name,"post_id = pcat_post_id","left");
		
		$this->set_post_query_filter($filter);

		$results=$this->db->get();

		$rows=$results->result_array();
		foreach($rows as &$row)
			$row['pc_gallery']=json_decode($row['pc_gallery'],TRUE);
		
		return $rows;
	}

	public function get_total($filter)
	{
		$this->db->select("COUNT(*) as count");
		$this->db->from($this->post_table_name);
		$this->db->join($this->post_content_table_name,"post_id = pc_post_id","left");
		$this->db->join($this->post_category_table_name,"post_id = pcat_post_id","left");
			
		$filter['count']=1;
		$this->set_post_query_filter($filter);
		
		$row=$this->db->get()->row_array();

		return $row['count'];
	}

	private function set_post_query_filter($filter)
	{
		if(isset($filter['lang']))
			$this->db->where("pc_lang_id",$filter['lang']);

		if(isset($filter['category_id']))
			$this->db->where("pcat_category_id",$filter['category_id']);

		if(isset($filter['active']))
			$this->db->where(array(
				"post_active"=>$filter['active']
				,"pc_active"=>$filter['active']
			));

		if(isset($filter['post_date_le']))
			$this->db->where("post_date <",$filter['post_date_le']);

		if(isset($filter['order_by']))
		{
			if($filter['order_by']==="random")
				$this->db->order_by("post_id","random");
			else
				$this->db->order_by($filter['order_by']);
		}
		else
			$this->db->order_by("post_id DESC");	

		if(isset($filter['start']))
			$this->db->limit($filter['count'],$filter['start']);

		if(!isset($filter['count']))
			if(isset($filter['lang']) || isset($filter['category_id']))
				$this->db->group_by("post_id");
	
		return;
	}

	public function get_post($post_id,$filter=array())
	{
		$cat_query=$this->db
			->select("GROUP_CONCAT(pcat_category_id)")
			->from($this->post_category_table_name)
			->where("pcat_post_id",$post_id)
			->get_compiled_select();

		$this->db
			->select("post.* , post_content.* , user_id, user_name")
			->select("(".$cat_query.") as categories")
			->from("post")
			->join("user","post_creator_uid = user_id","left")
			->join("post_content","post_id = pc_post_id","left")
			->where("post_id",$post_id);

		$this->set_post_query_filter($filter);

		$results=$this->db
			->get()
			->result_array();

		$this->set_galleries($results);

		return $results;
	}

	private function set_galleries(&$posts)
	{
		foreach($posts as &$post)
		{
			$gallery=array(
				'last_index'	=> 0
				,'images'		=> array()
			);

			if($post['pc_gallery'])
				$gallery=json_decode($post['pc_gallery'],TRUE);

			$post['pc_gallery']=$gallery;
		}

		return;
	}

	public function set_post_props($post_id, $props, $post_contents)
	{	
		$this->db
			->where("pcat_post_id",$post_id)
			->delete($this->post_category_table_name);
		
		$props_categories=$props['categories'];
		
		if($props_categories!=NULL)
		{
			$categories=explode(",",$props_categories);
			$ins=array();
			foreach($categories as $category_id)
				$ins[]=array("pcat_post_id"=>$post_id,"pcat_category_id"=>(int)$category_id);

			if($ins)
				$this->db->insert_batch($this->post_category_table_name,$ins);
		}

		unset($props['categories']);

		$props=select_allowed_elements($props,$this->post_writable_props);

		if($props)
		{
			foreach ($props as $prop => $value)
				$this->db->set($prop,$value);

			$this->db
				->where("post_id",$post_id)
				->update($this->post_table_name);
		}

		$props['categories']=$props_categories;

		foreach($post_contents as $content)
		{
			$lang=$content['pc_lang_id'];

			$content['pc_gallery']=json_encode($content['pc_gallery']);
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

	public function change_category($old_category_id,$new_category_id)
	{
		$rows=$this->db
			->where("pcat_category_id",$old_category_id)
			->or_where("pcat_category_id",$new_category_id)
			->group_by("pcat_post_id")
			->get($this->post_category_table_name)
			->result_array();

		$post_ids=array();
		foreach($rows as $row)
			$post_ids[]=$row['pcat_post_id'];

		if(!$post_ids)
			return;

		$this->db
			->where("pcat_category_id",$old_category_id)
			->or_where("pcat_category_id",$new_category_id)
			->delete($this->post_category_table_name);

		$ins=array();
		foreach($post_ids as $post_id)
			$ins[]=array("pcat_category_id"=>$new_category_id,"pcat_post_id"=>$post_id);

		$this->db->insert_batch($this->post_category_table_name,$ins);

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

		$this->db
			->where("pcat_post_id",$post_id)
			->delete($this->post_category_table_name);
		
		$this->log_manager_model->info("POST_DELETE",$props);	

		return;

	}
}

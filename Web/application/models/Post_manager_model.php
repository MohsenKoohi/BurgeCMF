<?php
class Post_manager_model extends CI_Model
{
	private $post_table_name="post";
	private $post_content_table_name="post_content";
	private $post_category_table_name="post_category";
	private $post_comment_table_name="post_comment";

	private $post_writable_props=array(
		"post_date","post_active","post_allow_comment"
	);
	private $post_content_writable_props=array(
		"pc_active","pc_image","pc_keywords","pc_description","pc_title","pc_content","pc_gallery"
		);

	private $post_comment_statuses=array(
		"waiting","not_verified","verified"
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

		$post_comment_table=$this->db->dbprefix($this->post_comment_table_name); 
		$statuses=$this->post_comment_statuses;
		$default_status=$statuses[0];
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $post_comment_table (
				`pcom_id` INT AUTO_INCREMENT
				,`pcom_post_id` INT
				,`pcom_visitor_name` CHAR(63)
				,`pcom_visitor_ip` CHAR(15)
				,`pcom_date` CHAR(20)
				,`pcom_text` VARCHAR(1023)
				,`pcom_status` ENUM ('".implode("','", $statuses)."') DEFAULT '$default_status'
				,PRIMARY KEY (pcom_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("post","post_manager");
		$this->module_manager_model->add_module_names_from_lang_file("post");

		$this->load->model("constant_manager_model");
		$this->constant_manager_model->set("show_post_comment_after_verification",0);
		
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
		$ctb=$this->db->dbprefix($this->post_comment_table_name);

		return $this->db->query("
			SELECT 
				(SELECT COUNT(*) FROM $tb) as total
				,(SELECT COUNT(*) FROM $tb WHERE post_active) as active
				,(SELECT COUNT(*) FROM $ctb WHERE pcom_status = 'verified') as verified_comments
				,(SELECT COUNT(*) FROM $ctb WHERE pcom_status = 'not_verified') as not_verified_comments
				,(SELECT COUNT(*) FROM $ctb WHERE pcom_status = 'waiting') as waiting_comments
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
			$post_contents[]=array(
				"pc_post_id"=>$new_post_id
				,"pc_lang_id"=>$index
			);
		$this->db->insert_batch($this->post_content_table_name,$post_contents);

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
		$this->db->select("COUNT( DISTINCT post_id ) as count");
		$this->db->from($this->post_table_name);
		$this->db->join($this->post_content_table_name,"post_id = pc_post_id","left");
		$this->db->join($this->post_category_table_name,"post_id = pcat_post_id","left");
		
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

		if(isset($filter['title']))
		{
			$title=trim($filter['title']);
			$title="%".str_replace(" ","%",$title)."%";
			$this->db->where("( `pc_title` LIKE '$title')");
		}

		if(isset($filter['active']))
			$this->db->where(array(
				"post_active"=>$filter['active']
				,"pc_active"=>$filter['active']
			));

		if(isset($filter['post_date_le']))
			$this->db->where("post_date <=",str_replace("/","-",$filter['post_date_le']));

		if(isset($filter['post_date_ge']))
			$this->db->where("post_date >=",str_replace("/","-",$filter['post_date_ge']));

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

		if(isset($filter['group_by']))
			$this->db->group_by($filter['group_by']);
	
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

	public function get_comments_statuses()
	{
		return $this->post_comment_statuses;
	}

	public function get_comments($filter)
	{
		$this->db->from($this->post_comment_table_name);
		$this->db->join($this->post_table_name,"post_id = pcom_post_id","left");
		$this->db->join($this->post_content_table_name,"pcom_post_id = pc_post_id AND pc_lang_id = '".$this->selected_lang."' ","left");
		
		$this->set_comments_query_filter($filter);

		$results=$this->db->get();

		$rows=$results->result_array();
		
		return $rows;
	}

	public function get_total_comments($filter)
	{
		$this->db->select("COUNT( DISTINCT pcom_id ) as count");
		$this->db->from($this->post_comment_table_name);
		$this->db->join($this->post_table_name,"post_id = pcom_post_id","left");
		$this->db->join($this->post_content_table_name,"pcom_post_id = pc_post_id AND pc_lang_id = '".$this->selected_lang."' ","left");
		
		$this->set_comments_query_filter($filter);
		
		$row=$this->db->get()->row_array();

		return $row['count'];
	}

	private function set_comments_query_filter($filter)
	{
		if(isset($filter['comment_post']))
		{
			if((int)$filter['comment_post'])
				$this->db->where("pcom_post_id",(int)$filter['comment_post']);
			elseif(is_string($filter['comment_post']))
			{
				if(strpos($filter['comment_post'], ",")!==FALSE)
					$this->db->where_in("pcom_post_id", explode(",", $filter['comment_post']));				
				else
				{
					$title=trim($filter['comment_post']);
					$title="%".str_replace(" ","%",$title)."%";
					$this->db->where("( `pc_title` LIKE '$title')");
				}
			}
		}

		if(isset($filter['comment_writer_name']))
		{
			$name=trim($filter['comment_writer_name']);
			$name="%".str_replace(" ","%",$name)."%";
			$this->db->where("( `pcom_visitor_name` LIKE '$name')");
		}

		if(isset($filter['comment_status']))
			$this->db->where("pcom_status", $filter['comment_status']);

		if(isset($filter['comment_ip']))
		{
			$ip=trim($filter['comment_ip']);
			$ip="%".str_replace(" ","%",$ip)."%";
			$this->db->where("( `pcom_visitor_ip` LIKE '$ip')");
		}

		if(isset($filter['comment_date_le']))
			$this->db->where("pcom_date <=",str_replace("/","-",$filter['comment_date_le']));

		if(isset($filter['comment_date_ge']))
			$this->db->where("pcom_date >=",str_replace("/","-",$filter['comment_date_ge']));

		if(isset($filter['order_by']))
			$this->db->order_by($filter['order_by']);
		else
			$this->db->order_by("pcom_id ASC");

		if(isset($filter['start']))
			$this->db->limit($filter['count'],$filter['start']);

		return;
	}


	public function show_post_comment_after_verification()
	{
		$this->load->model("constant_manager_model");
		return $this->constant_manager_model->get("show_post_comment_after_verification");
	}

	public function add_comment($post_id, $in_props)
	{
		$props=array(
			"pcom_post_id"				=> $post_id
			,"pcom_visitor_name"		=> $in_props['name']
			,"pcom_visitor_ip"		=> $in_props['ip']
			,"pcom_text"				=> $in_props['text']
			,"pcom_date"				=> get_current_time()
		);

		$this->db->insert($this->post_comment_table_name, $props);

		$pcom_id=$this->db->insert_id();

		$props['pcom_id']=$pcom_id;
		$this->log_manager_model->info("POST_COMMENT_ADD", $props);

		return $pcom_id;
	}

	public function update_comments($comment_updates, $deleted_comment_ids)
	{
		if($comment_updates)		
		{
			$this->db
				->update_batch($this->post_comment_table_name, $comment_updates, "pcom_id");

			foreach($comment_updates as $c)
				$this->log_manager_model->info("POST_COMMENT_CHANGE", $c);
		}

		if($deleted_comment_ids)
		{
			$this->db
				->where_in("pcom_id",$deleted_comment_ids)
				->delete($this->post_comment_table_name);

			$props=array(
				"pcom_ids"		=> implode(",", $deleted_comment_ids)
			);
			$this->log_manager_model->info("POST_COMMENT_DELETE", $props);
		}

		return;
	}
}

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
}

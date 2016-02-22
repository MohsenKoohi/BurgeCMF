<?php

class Category_manager_model extends CI_Model
{
	private $category_table_name="category";
	private $category_description_table_name="category_description";
	private $organized_category_file_path;

	private $category_description_writable_props=array(
		'cd_name','cd_url','cd_description','cd_image','cd_meta_keywords','cd_meta_description'
	);

	public function __construct()
	{
		parent::__construct();

		$this->organized_category_file_path=CATEGORY_CACHE_DIR."/organized_cats.json";

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
				,`cd_url` varchar(1024)
				,`cd_description` TEXT
				,`cd_image` varchar(1024)
				,`cd_meta_keywords` VARCHAR(1024)
				,`cd_meta_description` VARCHAR(1024)
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
		$CI=& get_instance();
		//$lang=$CI->language->get();
		//$CI->lang->load('ae_log',$lang);		
		
		$data=array();
		$row=$this->db
			->select("COUNT(*) as count")
			->from($this->category_table_name)
			->get()
			->row_array();
		$data['total']=$row['count'];
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("category_dashboard"),$data,TRUE);
		
		return $ret;		
	}

	//this method is responsible for creating hierarchical structure of categories,
	//so we don't need to run multiplt queries to retreive the structure from the database.
	//it should be updated 
	private function organize()
	{
		$result=$this->db
			->select("*")
			->from($this->category_table_name)
			->join($this->category_description_table_name,"category_id = cd_category_id","left")
			->order_by("category_id ASC, cd_lang_id ASC")
			->get();

		$rows=$result->result_array();
		
		$cats=array();
		foreach($rows as $row)
		{
			$cid=$row['category_id'];
			if(!isset($cats[$cid]))
			{
				$cats[$cid]=array();

				$cats[$cid]['id']=$cid;
				$cats[$cid]['parents']=array();
				$cats[$cid]['parents'][0]=$row['category_parent_id'];
				
				$cats[$cid]['names']=array();
				$cats[$cid]['urls']=array();
				$cats[$cid]['images']=array();
				$cats[$cid]['children']=array();
			}

			$cats[$cid]['names'][$row['cd_lang_id']]=$row['cd_name'];
			$cats[$cid]['urls'][$row['cd_lang_id']]=$row['cd_url'];
			$cats[$cid]['images'][$row['cd_lang_id']]=$row['cd_image'];
		}

		$cats[0]=array("id"=>0,"names"=>"ROOT","parents"=>array(0),'children'=>array());

		foreach($cats as &$cat)
		{
			if(!$cat['id'])
				continue; 

			$parent=&$cats[$cat['parents'][0]];
			$parent['children'][]=&$cat;

			$i=0;
			$parent=&$cat;
			do
			{
				$parent_id=$parent['parents'][0];
				$parent=&$cats[$parent_id];

				$cat['parents'][$i]=$parent['id'];
				$i++;
			}
			while($parent['id']);
		}

		file_put_contents($this->organized_category_file_path, json_encode($cats));

		//bprint_r($cats);
		//exit();

		return;
	}

	public function get_all()
	{
		if(!file_exists($this->organized_category_file_path))
			$this->organize();

		$cats=json_decode(file_get_contents($this->organized_category_file_path),TRUE);

		return $cats;
	}


	//creates the hierarchy of categories
	//using get_all
	//$type can be radio, or checkbox
	//$lang is the language selected for the name of each category
	//$ignore_ids are ids (and their children) which must not have radio or checkbox
	public function get_hierarchy($type,$lang,$ignore_ids=array())
	{
		$categories=$this->get_all();
		//bprint_r($categories);

		return "<ul>".$this->create_hierarchy($categories[0],$type,$lang,$ignore_ids)."</ul>";
	}

	private function create_hierarchy(&$category,$type,$lang,&$ignore_ids)
	{
		$id=$category['id'];
		if(in_array($id,$ignore_ids))
			$inp="&nbsp;";
		else
			switch($type)
			{
				case 'radio':
					$inp="<input type='radio' name='category' value='".$id."'/>";
					break;

				case 'checkbox':
					$inp="<input type='checkbox' data-id='".$id."' />";
					break;
			}

		if(!$id)
			$name="{root_text}";
		else
			if($category['names'][$lang])
				$name=$category['names'][$lang];
			else
				$name="{no_title_text}";
		

		$ret="<li>$inp <span data-id='$id'>$name</span>";

		
		if($category['children'])
		{	
			$ret.="<ul>";
			
			foreach($category['children'] as $child)
			{
				if(in_array($child['parents'][0],$ignore_ids))
					$ignore_ids[]=$child['id'];

				$ret.=$this->create_hierarchy($child,$type,$lang,$ignore_ids);
			}

			$ret.="</ul>";
		}
		
		$ret.="</li>";

		return $ret;
	}
	

	public function get_info($category_id,$lang_id=NULL)
	{
		$this->db
			->select("*")
			->from($this->category_table_name)
			->join($this->category_description_table_name,"category_id = cd_category_id","LEFT")
			->where("category_id",(int)$category_id);

		if($lang_id)
			$this->db->where("cd_lang_id",$lang_id);

		$result=$this->db
			->get()
			->result_array();

		if($lang_id)
			return $result[0];

		return $result;
	}

	public function add($parent_id)
	{
		$this->db->insert($this->category_table_name,array("category_parent_id"=>$parent_id));
		
		$category_id=$this->db->insert_id();

		$category_descs=array();
		foreach($this->language->get_languages() as $index=>$lang)
			$category_descs[]=array(
				"cd_category_id"=>$category_id
				,"cd_lang_id"=>$index
			);

		$this->db->insert_batch($this->category_description_table_name,$category_descs);

		$this->log_manager_model->info("CATEGORY_CREATE",array("category_id"=>$category_id));	

		$this->organize();

		return $category_id;
	}

	//parentize of its sub-categories and posts 
	//to its parent ;)
	public function delete($category_id)
	{
		$row=$this->db
			->get_where($this->category_table_name,array("category_id"=>$category_id))
			->row_array();

		$parent_id=$row['category_parent_id'];
		
		$this->db
			->set("category_parent_id",$parent_id)
			->where("category_parent_id",$category_id)
			->update($this->category_table_name);

		$this->db
			->where("category_id",$category_id)
			->delete($this->category_table_name);

		$this->db
			->where("cd_category_id",$category_id)
			->delete($this->category_description_table_name);

		$this->load->model("post_manager_model");
		$this->post_manager_model->change_category($category_id,$parent_id);

		$this->log_manager_model->info("CATEGORY_DELETE",array("category_id"=>$category_id));	

		$this->organize();

		return;
	}

	public function set_props($category_id,$category_props)
	{
		$log_props=array();

		$parent_id=$category_props['category_parent_id'];
		$this->db
			->set("category_parent_id",$parent_id)
			->where("category_id",$category_id)
			->update($this->category_table_name);

		$log_props["category_parent_id"]=$parent_id;

		foreach($category_props['descriptions'] as $category_description)
		{
			$lang=$category_description['cd_lang_id'];

			$props=select_allowed_elements(
				$category_description,
				$this->category_description_writable_props
			);

			foreach($props as $prop => $value)
			{
				$this->db->set($prop,$value);
				$log_props[$lang."_".$prop]=$value;
			}

			$this->db
				->where("cd_category_id",$category_id)
				->where("cd_lang_id",$lang)
				->update($this->category_description_table_name);
		}

		$this->log_manager_model->info("CATEGORY_CHANGE",$log_props);	

		$this->organize();

		return;
	}
}
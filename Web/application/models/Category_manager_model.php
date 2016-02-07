<?php

class Category_manager_model extends CI_Model
{
	private $category_table_name="category";
	private $category_description_table_name="category_description";
	private $organized_category_file_path;

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
				,`cd_description` TEXT
				,`cd_meta_keywords` VARCHAR(1024)
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
				$cats[$cid]['children']=array();
			}

			$cats[$cid]['names'][$row['cd_lang_id']]=$row['cd_name'];
			$cats[$cid]['urls'][$row['cd_lang_id']]=$row['cd_url'];
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

	public function get_info($category_id)
	{
		return $this->db
			->select("*")
			->from($this->category_table_name)
			->join($this->category_description_table_name,"category_id = cd_category_id","LEFT")
			->where("category_id",(int)$category_id)
			->get()
			->result_array();
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

		$this->organize();

		return $category_id;
	}
}
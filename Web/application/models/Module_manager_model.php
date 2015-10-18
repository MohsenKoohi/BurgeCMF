<?php
class Module_manager_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		return;
	}

	public function get_all_modules_info($lang)
	{
		$this->db->select("module.module_id, module_name");
		$this->db->from("module_name");
		$this->db->join("module","module.module_id = module_name.module_id","left");
		$this->db->where("module_name.lang",$lang);
		$this->db->order_by("module.sort_order","ASC");
		$results=$this->db->get();

		return $results->result_array();
	}

	//this method adds a module to the framework
	public function add_module($module,$model_name,$sort_order)
	{
		$result=$this->db->get_where("module",array("module_id"=>$module));
		if($result->num_rows())
			$result=FALSE;
		else
		{
			$this->db->insert(
				"module",
				array(
					"module_id"		=>$module
					,"model_name"	=>$model_name
					,"sort_order"	=>$sort_order	
				)
			);
			$result=TRUE;
		}
		$this->logger->info("[add_module] [module_id:$module] [result:$result]");

		return $result;
	}

	//adds the name of a module in a language
	public function add_module_name($module_id,$lang,$name)
	{
		$sql=$this->db->insert_string("module_name",array("module_id"=>$module_id,"lang"=>$lang,"module_name"=>$name));
		$sql.='  ON DUPLICATE KEY UPDATE module_name = '.$this->db->escape($name);
		$this->db->query($sql);
		$this->logger->info("[add_module_name] [module_id:$module_id] [lang:$lang] [name:$name] [result:1]");

		return TRUE;
	}

	//returns an array of modules a user has access to, and their links
	public function get_user_modules_names($user_id,$lang="")
	{
		$ret=array();
		if(!$user_id)
			return $ret;

		if(!$lang)
		{
			$lang_obj=& Language::get_instance();
			$lang=$lang_obj->get();				
		}
	
		$this->load->model("access_manager_model");
		$modules=$this->access_manager_model->get_user_modules($user_id);

		$this->db->select("*");
		$this->db->from("module_name");
		$this->db->join("module","module.module_id = module_name.module_id","left");
		$this->db->where("module_name.lang",$lang);
		$this->db->where_in("module_name.module_id",$modules);
		$this->db->order_by("module.sort_order","ASC");
		$results=$this->db->get();

		foreach ($results->result_array() as $row)
			$ret[]=array(
				"id"=>$row['module_id']
				,"model"=>$row['model_name']
				,"name"=>$row['module_name']
				,"link"=>get_link("admin_".$row['module_id'])
			);

		return $ret;
	}

	public function get_dashbord_info()
	{
		$CI=& get_instance();
		$lang=$CI->language->get();
		$CI->lang->load('admin_module',$lang);		
		
		$data=array();
		$data['modules']=$this->get_all_modules_info($lang);
		$data['total_text']=$CI->lang->line("total");
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("module_dashboard"),$data,TRUE);
		
		return $ret;		
	}
}
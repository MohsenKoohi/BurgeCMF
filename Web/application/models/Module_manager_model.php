<?php
class Module_manager_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		return;
	}

	public function install()
	{
		$module_table=$this->db->dbprefix('module'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $module_table (
				`module_id` char(50) NOT NULL
				,`sort_order` int DEFAULT 0
				,`model_name` char(100)
				,PRIMARY KEY (module_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$module_name_table=$this->db->dbprefix('module_name'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $module_name_table (
				`module_id` char(50) NOT NULL,
				`lang` char(2) NOT NULL,
				`module_name` char(100) NOT NULL,
				PRIMARY KEY (module_id, lang)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->add_module("module","module_manager");
		$this->add_module_names_from_lang_file("module");
		
		//we have a pseudo module here ;)
		$this->add_module("dashboard","");
		$this->add_module_names_from_lang_file("dashboard");

		return;
	}

	public function uninstall()
	{

		return;
	}

	public function install_module($module_model_name)
	{
		$this->log_manager_model->info("MODULE_INSTALL",array("module"=>$module_model_name));
		
		$this->load->model($module_model_name."_model");
		$model=$this->{$module_model_name."_model"};
		
		if(!method_exists($model, "install"))
			return;

		$model->{"install"}();

		return;
	}

	public function uninstall_module($module_model_name)
	{
		$this->log_manager_model->info("MODULE_UNINSTALL",array("module"=>$module_model_name));

		$this->load->model($module_model_name."_model");
		$model=$this->{$module_model_name."_model"};
		
		if(!method_exists($model, "uninstall"))
			return;

		$model->{"uninstall"}();

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
	public function add_module($module,$model_name,$sort_order=1)
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
					,"sort_order"	=>(int)$sort_order	
				)
			);
			$result=TRUE;
		}
		
		$this->log_manager_model->info("MODULE_ADD",
			array("module_id"=>$module)
		);

		return $result;
	}

	//adds the name of a module in a language
	public function add_module_name($module_id,$lang,$name)
	{
		$sql=$this->db->insert_string("module_name",array("module_id"=>$module_id,"lang"=>$lang,"module_name"=>$name));
		$sql.='  ON DUPLICATE KEY UPDATE module_name = '.$this->db->escape($name);
		$this->db->query($sql);

		$this->log_manager_model->info("MODULE_ADD_NAME",array(
			"module_id"=>$module_id
			,"lang"=>$lang
			,"name"=>$name
		));

		return TRUE;
	}

	//Searchs for the module_id index in each $lang/modules_lang.php file and add the name of module
	//It is usefull for initial modules which we don't want to change all modules files
	//after adding a new lang
	public function add_module_names_from_lang_file($module_id)
	{
		$CI=& get_instance();
		foreach($CI->language->get_languages() as $lang => $value)
		{
			$CI->lang->load('ae_all_modules',$lang);
			$name=$CI->lang->line($module_id);
			$this->add_module_name($module_id,$lang,$name);
		}

		return;
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
		$this->db->order_by("module.sort_order ASC, module.module_id ASC");
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
		$CI->lang->load('ae_module',$lang);		
		
		$data=array();
		$data['modules']=$this->get_all_modules_info($lang);
		$data['total_text']=$CI->lang->line("total");
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("module_dashboard"),$data,TRUE);
		
		return $ret;		
	}
}
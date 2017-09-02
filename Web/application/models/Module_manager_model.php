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
				,`cron_period` INT DEFAULT 0
				,`cron_last_execution` DATETIME DEFAULT  NULL
				,`cron_priority` INT DEFAULT 0
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

	//assigns period in minutes
	//assigns more priority with a lager number
	public function set_cron($module_id, $period, $priority)
	{
		$this->db
			->set("cron_period", (int)$period)
			->set("cron_priority", (int)$priority)
			->where("module_id", $module_id)
			->update("module");

		$this->log_manager_model->info("MODULE_CRON_UPDATE",array(
			"module"		=> $module_id
			,"period"	=> $period
			,"priority"	=> $priority	
		));

		return;
	}

	public function get_cron_modules()
	{
		return $this->db
			->select("*, ADDTIME( cron_last_execution , CONCAT('0 00:',cron_period,':00')) as eet")
			->from("module")
			->where("cron_period > 0")
			->where(" ( ISNULL (cron_last_execution) OR  ADDTIME( cron_last_execution , CONCAT('0 00:',cron_period,':00') ) < NOW() ) ",NULL,FALSE)
			->order_by("cron_priority DESC")
			->get()
			->result_array();
	}

	public function set_cron_execution($module_id)
	{
		$this->db
			->set("cron_last_execution","NOW()",FALSE)
			->where("module_id", $module_id)
			->update("module");

		$this->log_manager_model->info("MODULE_CRON_EXECUTE",array(
			"module"		=> $module_id
		));

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
		$this->db->select("module.module_id, module_name, model_name");
		$this->db->from("module_name");
		$this->db->join("module","module.module_id = module_name.module_id","left");
		$this->db->where("module_name.lang",$lang);
		$this->db->order_by("module.sort_order","ASC");
		$results=$this->db->get();

		return $results->result_array();
	}

	public function resort($module_ids)
	{
		$update_array=array();
		$i=1;
		foreach($module_ids as $module_id)
			$update_array[]=array(
				"module_id"=>$module_id
				,"sort_order"=>$i++
			);

		$this->db->update_batch("module",$update_array, "module_id");
		
		$this->log_manager_model->info("MODULE_RESORT",array("module_ids"=>implode(",",$module_ids)));	

		return;
	}

	//In next version we should allow for one module to have multiple items
	//in side menu, or dashboard, thus we should set the name of method
	//in addition to model, and separate it by "/", to allow the
	//module manager to identify which method should be called
	//2016/02/28

	//We have a problem with this method.
	//While it is possible to exclude modules from being shown in the dashboard
	// (by passing "" for $model_name)
	//, all modules including pseudo modules display in side menu.
	//In next version it should be an option that a module can be isplayed in side menu.
	//For example we don't want to show file manager module to be shown in the side menu
	//It can be done using database or by defining a function for each module.
	//If we want to use function, it is necessary to cache values for side menu 
	//and we should define a side menu manager, which maintatin a (for example JSON) file
	//which contains all side menu items.
	//It should also have an update method to update a specific item
	//this update is also necessary for get_sidebar_text method of each module
	//used in Burge_CMF_Controller .
	//2015/11/02

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
	public function get_user_modules_names($user,$lang="")
	{
		$ret=array();
		if(!$user)
			return $ret;

		if(!$lang)
		{
			$lang_obj=& Language::get_instance();
			$lang=$lang_obj->get();				
		}
	
		$this->load->model("access_manager_model");
		$modules=$this->access_manager_model->get_user_modules($user);

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

	public function get_dashboard_info()
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
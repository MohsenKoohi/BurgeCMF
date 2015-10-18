<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		if(ENVIRONMENT!=='development')
			redirect(get_link("admin_no_access"));
	}

	public function install()
	{	
		$user_pass="badmin";
		$initial_modules=array("dashboard","post","module","user","access","hit_counter","change_pass");
		$module_models=array("","post_manager","module_manager","user_manager","access_manager","hit_counter","");
		$module_names_fa=array("داشبورد","پست‌ها","ماژول‌ها","کاربران","سطح دسترسی","تعداد بازدید","تغییر رمز");
		$module_names_en=array("Dashboard","Posts","Modules","Users","Access Levels","Visiting Counter","Chage Password");

		$this->logger->info("[admin/setup/install]");

		$user_table=$this->db->dbprefix('user'); 
		$result=$this->db->query("SHOW TABLES LIKE '$user_table'");
		if(0 && $result->num_rows())
		{
			redirect(get_link("admin_no_access"));
			return;
		}

		echo "<h1>Installing Burge CMF</h1>";

		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $user_table (
				`user_id` int(20) auto_increment NOT NULL,
				`user_email` char(100) NOT NULL UNIQUE,
				`user_pass` char(32) DEFAULT NULL,
				`user_salt` char(32) NOT NULL,
				PRIMARY KEY (user_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("user_manager_model");
		$this->user_manager_model->add_if_not_exist($user_pass,$user_pass);
		$user=new User($user_pass);

		echo "Username: $user_pass<br>Pass: $user_pass<br>";
		echo "<h2>Login <a href='".get_link("admin_login")."'>here</a>.</h2>";

		$module_table=$this->db->dbprefix('module'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $module_table (
				`module_id` char(50) NOT NULL
				,`sort_order` int(20) DEFAULT 0
				,`model_name` char(100)
				,PRIMARY KEY (module_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);
		$this->load->model("module_manager_model");
		$i=0;
		foreach ($initial_modules as $module) 
			$this->module_manager_model->add_module($module,$module_models[$i],$i++);

		$module_name_table=$this->db->dbprefix('module_name'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $module_name_table (
				`module_id` char(50) NOT NULL,
				`lang` char(2) NOT NULL,
				`module_name` char(100) NOT NULL,
				PRIMARY KEY (module_id, lang)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		foreach ($initial_modules as $index => $module)
		{
			$this->module_manager_model->add_module_name($module,"fa",$module_names_fa[$index]);
			$this->module_manager_model->add_module_name($module,"en",$module_names_en[$index]);
		}

		$access_table=$this->db->dbprefix('access'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $access_table (
				`user_id` int(20) NOT NULL,
				`module_id` char(50) NOT NULL,
				PRIMARY KEY (user_id , module_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("access_manager_model");
		$this->access_manager_model->set_allowed_modules_for_user($user->get_id(),$initial_modules);

		$hit_counter_table=$this->db->dbprefix('hit_counter'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $hit_counter_table (
				`ht_url` varchar(1000) NOT NULL,
				`ht_url_md5` char(16) NOT NULL,
				`ht_year` char(4) NOT NULL,
				`ht_month` char(2) NOT NULL,
				`ht_count` bigint DEFAULT 1,
				PRIMARY KEY (ht_url_md5, ht_year, ht_month)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$post_table=$this->db->dbprefix('post'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $hit_counter_table (
				`ht_url` varchar(1000) NOT NULL,
				`ht_url_md5` char(16) NOT NULL,
				`ht_year` char(4) NOT NULL,
				`ht_month` char(2) NOT NULL,
				`ht_count` bigint DEFAULT 1,
				PRIMARY KEY (ht_url_md5, ht_year, ht_month)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		return;
	}

	public function uninstall()
	{
		$this->logger->info("[ admin/setup/uninstall ]");
		echo "<h1>Uninstalling Burge CMF</h1>";

		$table_names=array("user","module","module_name","access","hit_counter","post");
		foreach($table_names as $tn)
		{
			$table_name=$this->db->dbprefix($tn); 
			$this->db->query("DROP TABLE IF EXISTS $table_name");	
		}
		
		echo "Done";

		return;
	}
}
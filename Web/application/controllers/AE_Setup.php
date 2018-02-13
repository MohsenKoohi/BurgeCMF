<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Setup extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		if(ENVIRONMENT!=='development')
			redirect(get_link("admin_no_access"));

		//check directories permssion
		$this->check_directories_permission();

	}

	private function check_directories_permission()
	{
		$this->load->helper("init");
		file_put_contents(IMAGES_DIR."/.htaccess", " Options -Indexes ");
		
		$dirs=array(CAPTCHA_DIR);
		$result=TRUE;

		foreach($dirs as $dir)
		{
			$cdp_message="";
			$result= $result && check_directory_permission($dir, $cdp_message);
			echo $cdp_message;
		}

		if(!$result)
		{
			echo "<h2>Please check the errors, and try again.";
			exit;
		}

		return;
	}

	public function install()
	{	
		$user_pass="badmin";

		echo "<h1>Installing Burge CMF</h1>";

		$this->log_manager_model->info("CMF_INSTALL");

		$this->load->model("module_manager_model");

		$this->module_manager_model->install_module("module_manager");
		
		$this->module_manager_model->install_module("user_manager");
		
		$this->load->model("user_manager_model");
		$props=array(
			"user_name"=>$user_pass
			,"user_email"=>$user_pass
			,"user_pass"=>$user_pass
			,"user_code"=>10
		);
		$this->user_manager_model->add_if_not_exist($props);
		
		$user=new User($user_pass);
		echo "Username: $user_pass<br>Pass: $user_pass<br>";
		echo "<h2>Login <a href='".get_link("admin_login")."'>here</a>.</h2>";

		$this->module_manager_model->install_module("access_manager");

		$this->module_manager_model->install_module("hit_counter");

		$this->module_manager_model->install_module("log_manager");

		$this->module_manager_model->install_module("constant_manager");

		$this->module_manager_model->install_module("post_manager");

		$this->module_manager_model->install_module("file_manager");

		$this->module_manager_model->install_module("category_manager");

		$this->module_manager_model->install_module("contact_us_manager");

		$this->module_manager_model->install_module("footer_link_manager");

		$default_lang=array_keys(LANGUAGES())[0];
		$modules_info=$this->module_manager_model->get_all_modules_info($default_lang);
		$modules=array();
		foreach($modules_info as $md)
			$modules[]=$md['module_id'];
		$this->load->model("access_manager_model");
		$this->access_manager_model->set_modules(-$user->get_id(),$modules);

		return;
	}

	public function uninstall()
	{
		$this->log_manager_model->info("CMF_UNINSTALL");
		echo "<h1>Uninstalling Burge CMF</h1>";

		$table_names=array("user","module","module_name","access","hit_counter","post","post_content");
		foreach($table_names as $tn)
		{
			$table_name=$this->db->dbprefix($tn); 
			$this->db->query("DROP TABLE IF EXISTS $table_name");	
		}
		
		echo "Done";

		return;
	}
}
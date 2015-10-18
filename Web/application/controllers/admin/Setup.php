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

		echo "<h1>Installing Burge CMF</h1>";
		
		$this->logger->info("[admin/setup/install]");

		$this->load->model("module_manager_model");

		$this->module_manager_model->install_module("module_manager");
		
		$this->module_manager_model->install_module("user_manager");
		$this->load->model("user_manager_model");
		$this->user_manager_model->add_if_not_exist($user_pass,$user_pass);
		$user=new User($user_pass);
		echo "Username: $user_pass<br>Pass: $user_pass<br>";
		echo "<h2>Login <a href='".get_link("admin_login")."'>here</a>.</h2>";

		$this->module_manager_model->install_module("access_manager");

		$this->module_manager_model->install_module("hit_counter");

		$this->module_manager_model->install_module("post_manager");

		$default_lang=array_keys(LANGUAGES())[0];
		$modules_info=$this->module_manager_model->get_all_modules_info($default_lang);
		$modules=array();
		foreach($modules_info as $md)
			$modules[]=$md['module_id'];
		$this->load->model("access_manager_model");
		$this->access_manager_model->set_allowed_modules_for_user($user->get_id(),$modules);

		return;
	}

	public function uninstall()
	{
		$this->logger->info("[ admin/setup/uninstall ]");
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
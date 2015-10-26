<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{

		echo time()."<br>";
		$dt=jdate("Y/m/d H:i:s");
		echo $dt."<br>";
		$g=formatted_jalali_to_georgian($dt);
		echo $g."<br>";
		echo (new DateTime($g))->getTimestamp();
		

		return;
		$this->load->model("access_manager_model");
		$this->load->model("user_manager_model");
		$this->load->model("module_manager_model");

		$this->lang->load('admin_access',$this->selected_lang);

		$this->data['users_info']=$this->user_manager_model->get_all_users_info();
		$this->data['modules_info']=$this->module_manager_model->get_all_modules_info($this->selected_lang);
		
		$this->data['selected_user_id']="";
		$this->data['selected_module_id']="";		

		if($this->input->post())
		{
			$this->lang->load('error',$this->selected_lang);

			if($this->input->post('post_type') === "user_access")
				$this->change_user_access();

			if($this->input->post('post_type') === "module_access")
				$this->change_module_access();			
		}

		$this->data['access_info']=array();
		foreach($this->data['users_info'] as $user)
		{	
			$user_id=$user['user_id'];
			$modules=$this->access_manager_model->get_user_modules($user_id);
			foreach ($modules as $module) 
				$this->data['access_info'][$user_id][$module]=1;
		}
		$this->data['lang_pages']=get_lang_pages(get_link("admin_access",TRUE));
		$this->data['header_title']=$this->lang->line("access_levels");

		$this->send_admin_output("access");

		return;	 
	}

}
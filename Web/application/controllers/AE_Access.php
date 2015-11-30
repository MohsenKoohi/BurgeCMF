<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Access extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->load->model("access_manager_model");
		$this->load->model("user_manager_model");
		$this->load->model("module_manager_model");

		$this->lang->load('ae_access',$this->selected_lang);

		$this->data['users_info']=$this->user_manager_model->get_all_users_info();
		$this->data['modules_info']=$this->module_manager_model->get_all_modules_info($this->selected_lang);
		
		if($this->input->post())
		{
			$this->lang->load('error',$this->selected_lang);

			if($this->input->post('post_type') === "user_access")
				$this->change_user_access();

			if($this->input->post('post_type') === "module_access")
				$this->change_module_access();			
		}

		$this->data['selected_user_id']=$this->session->flashdata('selected_user_id');
		$this->data['selected_module_id']=$this->session->flashdata('selected_module_id');

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

	private function change_user_access()
	{
		$user_id=(int)$this->input->post("user_id");
		if(!$user_id)
		{
			set_message($this->lang->line("select_user"));
			redirect(get_link("admin_access"));
			return;
		}

		$this->session->set_flashdata('selected_user_id',$user_id);

		$modules=array();
		foreach($this->data['modules_info'] as $mod)
		{
			$mid=$mod['module_id'];
			$post_string="module_id_".$mid;
			$post_var=$this->input->post($post_string);
			
			if($post_var === "on")
				$modules[]=$mid;
		}

		$this->access_manager_model->set_allowed_modules_for_user($user_id, $modules);
		set_message($this->lang->line("changed_successfully"));
		redirect(get_link("admin_access"));

		return;
	}


	private function change_module_access()
	{
		$module_id=$this->input->post("module_id");
		if(!$module_id)
		{
			set_message($this->lang->line("select_module"));
			redirect(get_link("admin_access"));
			return;
		}

		$this->session->set_flashdata('selected_module_id',$module_id);

		$users=array();
		foreach($this->data['users_info'] as $user)
		{
			$uid=$user['user_id'];
			$post_string="user_id_".$uid;
			$post_var=$this->input->post($post_string);
			
			if($post_var === "on")
				$users[]=$uid;
		}

		$this->access_manager_model->set_allowed_users_for_module($module_id, $users);
		set_message($this->lang->line("changed_successfully"));
		redirect(get_link("admin_access"));

		return;
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Dashboard extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{	

		$this->load->model("user_manager_model");
		$this->load->model("module_manager_model");
		$user_info=& $this->user_manager_model->get_user_info();

		$this->data['modules']=array();

		$modules=$this->module_manager_model->get_user_modules_names($user_info->get_id());
		foreach ($modules as $module)
		{
			$name=$module['name'];
			$link=$module['link'];
			$id=$module['id'];
			$model_name=$module['model'];
			if(!$model_name)
				continue;
			$this->load->model($model_name."_model");
			$model=$this->{$model_name."_model"};

			if(!method_exists($model, "get_dashboard_info"))
				continue;

			$text=$model->{"get_dashboard_info"}();

			$this->data['modules'][]=array(
				"id"		=>$id
				,"name"	=>$name
				,"link"	=>$link
				,"text"	=>$text
			);
		}

		$this->lang->load('ae_dashboard',$this->selected_lang);		
		
		$this->data['lang_pages']=get_lang_pages(get_link("admin_dashboard",TRUE));
		$this->data['header_title']=$this->lang->line("dashboard");

		$this->send_admin_output("dashboard");

		return;
	}
}
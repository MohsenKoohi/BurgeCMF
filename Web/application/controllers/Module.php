<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->load->model("module_manager_model");

		$this->lang->load('admin_module',$this->selected_lang);

		$this->data['modules_info']=$this->module_manager_model->get_all_modules_info($this->selected_lang);
	
		$this->data['lang_pages']=get_lang_pages(get_link("admin_module",TRUE));
		$this->data['header_title']=$this->lang->line("modules");
		
		$this->send_admin_output("module");

		return;	 
	}

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Module extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model("module_manager_model");
		$this->lang->load('ae_module',$this->selected_lang);

		return;
	}

	public function index()
	{

		if($this->input->post("post_type")==="resort")
			return $this->resort();

		$this->data['modules_info']=$this->module_manager_model->get_all_modules_info($this->selected_lang);
	
		$this->data['message']=get_message();
		$this->data['lang_pages']=get_lang_pages(get_link("admin_module",TRUE));
		$this->data['header_title']=$this->lang->line("modules");
		
		$this->send_admin_output("module");

		return;	 
	}

	private function resort()
	{
		$ids=$this->input->post("ids");
		$ids=explode(",",$ids);

		$this->module_manager_model->resort($ids);

		set_message($this->lang->line("modules_sorted_successfully"));

		return redirect(get_link("admin_module"));
	}

}
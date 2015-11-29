<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Log extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		
		$this->lang->load('admin_log',$this->selected_lang);

		$this->data['logs']=$this->log_manager_model->get_today_logs(2,30);
	
		$this->data['lang_pages']=get_lang_pages(get_link("admin_log",TRUE));
		$this->data['header_title']=$this->lang->line("log");
		
		$this->send_admin_output("log");

		return;	 
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{	
		$this->lang->load('admin_dashboard',$this->selected_lang);		
		
		$this->data['lang_pages']=get_lang_pages(get_link("admin_dashboard",TRUE));
		$this->data['header_title']=$this->lang->line("dashboard");

		$this->send_admin_output("dashboard");

		return;
	}
}
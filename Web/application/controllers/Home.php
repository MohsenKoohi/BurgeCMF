<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{	
		$this->data['message']=get_message();
		
		$this->lang->load('customer_home',$this->selected_lang);	

		$this->data['lang_pages']=get_lang_pages(get_link("home_link",TRUE));
		
		$this->data['header_title'].=$this->lang->line("header_title");
		$this->data['header_description'].=$this->lang->line("header_description");
		$this->data['header_keywords'].=$this->lang->line("header_keywords");

		$this->data['header_canonical_url']=get_link("home_link");
		

		$this->send_customer_output("home");

		return;
	}
}
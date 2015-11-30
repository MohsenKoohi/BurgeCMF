<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CE_Home extends Burge_CMF_Controller {
	protected $hit_level=1;

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{	
		$this->data['message']=get_message();
		
		$this->lang->load('customer_home',$this->selected_lang);	

		$this->data['lang_pages']=get_lang_pages(get_link("home_url",TRUE));
		
		$this->data['header_title'].=$this->lang->line("header_title");
		$this->data['header_meta_description'].=$this->lang->line("header_meta_description");
		$this->data['header_meta_keywords'].=$this->lang->line("header_meta_keywords");

		$this->data['header_canonical_url']=get_link("home_url");

		$this->send_customer_output("home");

		return;
	}
}
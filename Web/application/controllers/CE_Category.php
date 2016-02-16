<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CE_Category extends Burge_CMF_Controller {
	protected $hit_level=2;

	function __construct()
	{
		parent::__construct();
	}

	public function index($category_id)
	{	
		$this->data['message']=get_message();
		echo $category_id;

		return;
		$this->lang->load('ce_home',$this->selected_lang);	

		$this->data['lang_pages']=get_lang_pages(get_link("home_url",TRUE));
		
		$this->data['header_title']=$this->lang->line("header_title").$this->lang->line("header_separator").$this->data['header_title'];
		$this->data['header_meta_description']=$this->lang->line("header_meta_description");
		$this->data['header_meta_keywords'].=$this->lang->line("header_meta_keywords");

		$this->data['header_canonical_url']=get_link("home_url");

		$this->send_customer_output("home");

		return;
	}
}
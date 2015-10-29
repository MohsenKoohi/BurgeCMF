<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hit_counter extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->load->model("hit_counter_model");
		
		$this->lang->load('admin_hit_counter',$this->selected_lang);

		$this->data['counters_info']=$this->hit_counter_model->get_all_counts();
	
		$this->data['lang_pages']=get_lang_pages(get_link("admin_hit_counter",TRUE));
		$this->data['header_title']=$this->lang->line("visiting_counter");
		
		$this->send_admin_output("hit_counter");

		return;	 
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Footer_Link extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->lang->load('ae_footer_link',$this->selected_lang);
		$this->load->model("footer_link_manager_model");

	}

	public function index()
	{
		if($this->input->post("post_type")==="set_links")
			return $this->set_links();

		$this->data['message']=get_message();
		$this->data['links']=$this->footer_link_manager_model->get_links();

		$this->data['raw_page_url']=get_link("admin_footer_link");
		$this->data['lang_pages']=get_lang_pages(get_link("admin_footer_link",TRUE));
		$this->data['header_title']=$this->lang->line("footer_link");

		$this->send_admin_output("footer_link");

		return;	 
	}	

	private function set_links()
	{
		$ins=array();
		$links=$this->input->post("links");

		foreach($links as $id => $l)
			$ins[]=array(
				'fl_id'				=> $id
				,'fl_parent_id'	=> $l['parent_id']
				,'fl_title'			=> $l['title']
				,'fl_link'			=> $l['link']
			);

		$this->footer_link_manager_model->set_links($ins);

		set_message($this->lang->line("set_successfully"));

		redirect(get_link("admin_footer_link"));

		return;
	}
}
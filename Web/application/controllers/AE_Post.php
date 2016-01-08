<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Post extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->lang->load('ae_post',$this->selected_lang);
		$this->load->model("post_manager_model");

	}

	public function index()
	{
		if($this->input->post("post_type")==="add_post")
			return $this->add_post();

		$this->set_posts_info();

		$this->data['lang_pages']=get_lang_pages(get_link("admin_post",TRUE));
		$this->data['header_title']=$this->lang->line("posts");

		$this->send_admin_output("post");

		return;	 
	}	

	private function set_posts_info()
	{
		$filters=array();
		$filters['lang']=$this->language->get();

		$this->data['posts_info']=$this->post_manager_model->get_posts($filters);

		return;
	}

	private function add_post()
	{
		$post_id=$this->post_manager_model->add_post();

		return redirect(get_admin_post_details_link($post_id));
	}

	public function details($post_id)
	{
		if($this->input->post("post_type")==="edit_post")
			return $this->edit_post();

		$this->data['post_id']=$post_id;
		$this->data['post_info']=$this->post_manager_model->get_post($post_id);

		$this->data['lang_pages']=get_lang_pages(get_admin_post_details_link($post_id,TRUE));
		$this->data['header_title']=$this->lang->line("post_details")." ".$post_id;

		$this->send_admin_output("post_details");

		return;
	}

}
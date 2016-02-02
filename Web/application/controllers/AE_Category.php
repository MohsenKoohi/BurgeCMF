<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Category extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model("category_manager_model");
		$this->lang->load('ae_category',$this->selected_lang);

	}

	public function index()
	{
		if($this->input->post("post_type")==="add_category")
			return $this->add_category();

		$this->data['message']=get_message();

		$this->data['lang_pages']=get_lang_pages(get_link("admin_category",TRUE));
		$this->data['header_title']=$this->lang->line("categories");
		
		$this->send_admin_output("category");

		return;
	}

	private function add_category()
	{
		$id=$this->category_manager_model->add();

		//return redirect(get_admin_category_details_link($id));
	}

	public function details()
	{

	}
}
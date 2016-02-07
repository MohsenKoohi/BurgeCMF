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

		$this->data['categories']=$this->category_manager_model->get_all();
		$this->data['lang_pages']=get_lang_pages(get_link("admin_category",TRUE));
		$this->data['header_title']=$this->lang->line("categories");
		
		$this->send_admin_output("category");

		return;
	}

	private function add_category()
	{
		$id=$this->category_manager_model->add();

		set_message($this->lang->line("category_added_successfully"));

		return redirect(get_admin_category_details_link($id));
	}

	public function details($category_id)
	{
		$this->data['message']=get_message();

		$info=$this->category_manager_model->get_info((int)$category_id);

		foreach($info as &$row)
		{
			$info[$row['cd_lang_id']]=&$row;
			$row['lang']=$this->all_langs[$row['cd_lang_id']];
		}

		$this->data['info']=array();
		foreach($this->all_langs as $lang => $lang_name)
			$this->data['info'][$lang]=$info[$lang];

		$this->data['category_url_first_part']=get_customer_category_details_link($category_id,"");
		
		$this->data['category_id']=$category_id;
		$this->data['lang_pages']=get_lang_pages(get_admin_category_details_link($category_id,TRUE));
		$this->data['header_title']=$this->data['info'][$this->selected_lang]['cd_name'];

		$this->send_admin_output("category_details");
	}
}
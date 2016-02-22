<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CE_Category extends Burge_CMF_Controller {
	protected $hit_level=2;

	function __construct()
	{
		parent::__construct();
		$this->load->model("category_manager_model");
	}

	public function index($category_id,$category_name="")
	{	
		$category_info=$this->category_manager_model->get_info((int)$category_id,$this->selected_lang);
		if(!$category_info)
			redirect(get_link("home_url"));

		$category_link=get_customer_category_details_link($category_id,$category_info['cd_url']);
		if(get_customer_category_details_link($category_id,urldecode($category_name)) !== $category_link)
			redirect($category_link,"location",301);

		//$this->lang->load('ce_category',$this->selected_lang);	

		$this->data['category_info']=$category_info;

		$this->data['message']=get_message();

		$this->load->model("post_manager_model");
		$this->data['posts']=$this->post_manager_model->get_posts(array(
			"lang"=>$this->selected_lang
			,"category_id"=>$category_id
			,"active"=>1
			,"start"=>0
			,"count"=>20
			,"order_by"=>"post_id DESC"
		));

		$this->data['lang_pages']=get_lang_pages(get_link("home_url",TRUE));
		
		$this->data['page_title']=$category_info['cd_name'];
		$this->data['header_title']=$category_info['cd_name'].$this->lang->line("header_separator").$this->data['header_title'];
		$this->data['header_meta_description']=$category_info['cd_meta_description'];
		$this->data['header_meta_keywords'].=",".$category_info['cd_meta_keywords'];

		$this->data['header_canonical_url']=$category_link;

		$this->send_customer_output("category");

		return;
	}
}
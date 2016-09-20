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
		
		$this->lang->load('ce_home',$this->selected_lang);	

		$this->load->model("post_manager_model");
		$this->data['posts']=$this->post_manager_model->get_posts(array(
			"lang"=>$this->selected_lang
			,"category_id"=>0
			,"post_date_le"=>get_current_time()
			,"active"=>1
			,"start"=>0
			,"count"=>20
			,"order_by"=>"post_date DESC"
		));

		foreach($this->data['posts'] as &$post_info)
		{
			if(!$post_info['pc_image'])
				if($post_info['pc_gallery'])
				{
					foreach($post_info['pc_gallery']['images'] as $img)
						break;
					$post_info['pc_image']=get_link("post_gallery_url").'/'.$img['image'];
				}
		}

		$this->data['lang_pages']=get_lang_pages(get_link("home_url",TRUE));
		
		$this->data['header_title']=$this->lang->line("header_title").$this->lang->line("header_separator").$this->data['header_title'];
		$this->data['header_meta_description']=$this->lang->line("header_meta_description");
		$this->data['header_meta_keywords'].=",".$this->lang->line("header_meta_keywords");

		$this->data['page_main_image']=get_link("images_url")."/logo-back-white.jpg";

		$this->data['header_canonical_url']=get_link("home_url");

		$this->send_customer_output("home");

		return;
	}

	public function redirect()
	{
		return redirect(get_link("home_url"));
	}
}
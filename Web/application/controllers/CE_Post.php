<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CE_Post extends Burge_CMF_Controller {
	protected $hit_level=2;

	function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			"post_manager_model"
			,"category_manager_model"
		));
	}

	public function index($post_id,$post_hash,$post_name="")
	{	
		$post_info_array=$this->post_manager_model->get_post((int)$post_id,array(
			"lang"=> $this->selected_lang
			,"post_date_le"=>get_current_time()
			,"active"=>1
			));
		$post_info=$post_info_array[0];

		if(!$post_info || !($post_hash === get_customer_post_link_hash($post_info['post_date'])))
			redirect(get_link("home_url"));

		$this->data['post_gallery']=$post_info['pc_gallery']['images'];

		$cat_ids=explode(',',$post_info['categories']);
		$this->data['post_categories']=$this->category_manager_model->get_categories_short_desc($cat_ids,$this->selected_lang);

		$post_link=get_customer_post_details_link($post_id,$post_info['pc_title'],$post_info['post_date']);
		if($post_info['pc_title'] && $post_name)
			if(get_customer_post_details_link($post_id,urldecode($post_name),$post_info['post_date']) !== $post_link)
				redirect($post_link,"location",301);

		$this->data['post_info']=$post_info;
		if($post_info['pc_image'])
			$this->data['page_main_image']=$post_info['pc_image'];
		else
			if($this->data['post_gallery'])
				$this->data['page_main_image']=get_link("post_gallery_url").'/'.$this->data['post_gallery'][0]['image'];
			
		$this->data['message']=get_message();

		$this->data['lang_pages']=get_lang_pages(get_customer_post_details_link($post_id,"",$post_info['post_date'],TRUE));
		
		$this->data['header_title']=$post_info['pc_title'].$this->lang->line("header_separator").$this->data['header_title'];
		$this->data['header_meta_description']=$post_info['pc_description'];
		$this->data['header_meta_keywords'].=",".$post_info['pc_keywords'];

		$this->data['header_canonical_url']=$post_link;

		$this->send_customer_output("post");

		return;
	}
}
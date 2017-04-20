<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CE_Category extends Burge_CMF_Controller {
	protected $hit_level=2;

	function __construct()
	{
		parent::__construct();
		$this->load->model("category_manager_model");
	}

	public function index($category_id,$category_hash,$category_name="",$page=1)
	{	
		$category_info=$this->category_manager_model->get_info((int)$category_id,$this->selected_lang);
		if(!$category_info || ($category_info['category_hash']!== $category_hash))
			redirect(get_link("home_url"));

		if($this->input->get("page"))
			$page=(int)$this->input->get("page");

		$category_link=get_customer_category_details_link($category_id,$category_info['category_hash'],$category_info['cd_url'],$page);
		if($category_info['cd_url'])
			if(get_customer_category_details_link($category_id,$category_hash,urldecode($category_name),$page) !== $category_link)
				redirect($category_link,"location",301);

		//$this->lang->load('ce_category',$this->selected_lang);	

		$this->data['category_info']=$category_info;
		if($category_info['cd_image'])
			$this->data['page_main_image']=$category_info['cd_image'];

		$this->data['message']=get_message();

		$this->load->model("post_manager_model");

		$per_page=20;
		$filter=array(
			"lang"=>$this->selected_lang
			,"category_id"=>$category_id
			,"post_date_le"=>get_current_time()
			,"active"=>1
		);
		$total_posts=$this->post_manager_model->get_total($filter);
		$total_pages=ceil($total_posts/$per_page);
		$this->data['total_pages']=$total_pages;

		if($total_pages>0 && $page>$total_pages)
			redirect(get_customer_category_details_link($category_id,$category_hash,$category_info['cd_url'],$total_pages));
		if($page<1)
			redirect(get_customer_category_details_link($category_id,$category_hash,$category_info['cd_url']));

		$this->data['current_page']=$page;
		$base_url=get_customer_category_details_link($category_id,$category_hash,$category_info['cd_url'],"page_number");
		
		$pagination_settings=array(
			"current_page"		=> $page
			,"total_pages"		=> $total_pages
			,"base_url"			=> $base_url
			,"page_text"		=> $this->lang->line("page")
		);
		//$this->data['pagination']=get_select_pagination($pagination_settings);

		$pagination_settings['base_url']=get_customer_category_details_link($category_id,$category_hash,$category_info['cd_url'],"");
		$this->data['pagination']=get_link_pagination($pagination_settings);

		$filter['start']=$per_page*($page-1);
		$filter['count']=$per_page;
		$filter['order_by']="post_date DESC";

		$this->data['posts']=$this->post_manager_model->get_posts($filter);
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

		if($page>1)
			$this->data['header_prev_url']=get_customer_category_details_link($category_id,$category_hash,$category_info['cd_url'],$page-1);
		if($page<$total_pages)
			$this->data['header_next_url']=get_customer_category_details_link($category_id,$category_hash,$category_info['cd_url'],$page+1);

		$this->data['lang_pages']=get_lang_pages(get_customer_category_details_link($category_id,$category_hash,$category_info['cd_url'],$page,TRUE));
		
		$this->data['header_title']=$category_info['cd_name'].$this->lang->line("header_separator").$this->data['header_title'];
		$this->data['header_meta_description']=$category_info['cd_meta_description'];
		$this->data['header_meta_keywords'].=",".$category_info['cd_meta_keywords'];

		$this->data['header_canonical_url']=$category_link;

		$this->send_customer_output("category");

		return;
	}
}
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

		if($post_info['post_allow_comment'])
			if($this->input->post("post_type") == 'add_comment')
				return $this->add_comment($post_id, $post_info);

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
			{
				foreach($this->data['post_gallery'] as $img)
					break;
				$this->data['page_main_image']=get_link("post_gallery_url").'/'.$img['image'];
				$this->data['post_info']['pc_image']=$this->data['page_main_image'];
			}

		if($post_info['post_allow_comment'])
		{
			$comments=$this->post_manager_model->get_comments($post_id);
			if($this->post_manager_model->show_post_comment_after_verification())
				foreach($commments as $index => $comment)
					if($comment['pcom_status'] != 'verified')
						unset($comments[$index]);
			else
				foreach($commments as $index => $comment)
					if($comment['pcom_status'] == 'not_verified')
						unset($comments[$index]);

			$this->data['comments']=$comments;
		}
			
		$this->data['message']=get_message();

		$this->data['lang_pages']=get_lang_pages(get_customer_post_details_link($post_id,"",$post_info['post_date'],TRUE));
		
		$this->data['header_title']=$post_info['pc_title'].$this->lang->line("header_separator").$this->data['header_title'];
		$this->data['header_meta_description']=$post_info['pc_description'];
		$this->data['header_meta_keywords'].=",".$post_info['pc_keywords'];

		$this->data['header_canonical_url']=$post_link;

		$this->send_customer_output("post");

		return;
	}

	private function add_comment($post_id, $post_info)
	{
		$page_link=get_customer_post_details_link($post_id,$post_info['pc_title'],$post_info['post_date']);

		$text=trim($this->input->post("text"));
		$name=trim($this->input->post("name"));
		if(!$text || !$name)
		{
			set_message($this->lang->line("please_fill_all_fields"));
			return redirect($page_link);
		}

		$ip=$this->input->ip_address();

		$this->post_manager_model->add_comment($post_id, array(
			"name"		=> $name
			,"text"		=> $text
			,"ip"			=> $ip
		));

		set_message($this->lang->line("your_comment_submitted_successfully"));
		
		return redirect($page_link);
	}
}
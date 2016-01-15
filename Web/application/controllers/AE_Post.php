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

		//we may have some messages that our post has been deleted successfully.
		$this->data['message']=get_message();
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
			return $this->edit_post($post_id);

		if($this->input->post("post_type")==="delete_post")
			return $this->delete_post($post_id);

		$this->data['post_id']=$post_id;
		$post_info=$this->post_manager_model->get_post($post_id);
		$this->data['langs']=$this->language->get_languages();

		$this->data['post_contents']=array();
		foreach($this->data['langs'] as $lang => $val)
			foreach($post_info as $pi)
				if($pi['pc_lang_id'] === $lang)
				{
					$this->data['post_contents'][$lang]=$pi;
					break;
				}
		if($post_info)
			$this->data['post_info']=array(
				"post_allow_comment"=>$post_info[0]['post_allow_comment']
				,"post_active"=>$post_info[0]['post_active']
				,"user_name"=>$post_info[0]['user_name']
				,"user_id"=>$post_info[0]['user_id']
				,"post_title"=>$this->data['post_contents'][$this->language->get()]['pc_title']
			);
		else
			$this->data['post_info']=array();

		$this->data['message']=get_message();
		$this->data['lang_pages']=get_lang_pages(get_admin_post_details_link($post_id,TRUE));
		$this->data['header_title']=$this->lang->line("post_details")." ".$post_id;

		$this->send_admin_output("post_details");

		return;
	}

	private function delete_post($post_id)
	{
		$this->post_manager_model->delete_post($post_id);

		set_message($this->lang->line('post_deleted_successfully'));

		return redirect(get_link("admin_post"));
	}

	private function edit_post($post_id)
	{
		$post_props=array();
		$post_props['post_active']=(int)($this->input->post('post_active') === "on");
		$post_props['post_allow_comment']=(int)($this->input->post('post_allow_comment') === "on");
		
		$post_content_props=array();
		foreach($this->language->get_languages() as $lang=>$name)
		{
			$post_content=$this->input->post($lang);
			$post_content['pc_lang_id']=$lang;

			if(isset($post_content['pc_active']))
				$post_content['pc_active']=(int)($post_content['pc_active']=== "on");
			else
				$post_content['pc_active']=0;

			$post_content_props[]=$post_content;
		}

		$this->post_manager_model->set_post_props($post_id,$post_props,$post_content_props);
		
		set_message($this->lang->line("changes_saved_successfully"));

		redirect(get_admin_post_details_link($post_id));

		return;
	}
}
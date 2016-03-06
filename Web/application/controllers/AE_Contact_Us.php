<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Contact_Us extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->lang->load('ae_contact_us',$this->selected_lang);
		$this->load->model("contact_us_manager_model");

	}

	public function index()
	{
		$this->set_messages_info();

		//we may have some messages that our post has been deleted successfully.
		$this->data['message']=get_message();
		$this->data['lang_pages']=get_lang_pages(get_link("admin_contact_us",TRUE));
		$this->data['header_title']=$this->lang->line("contact_us");

		$this->send_admin_output("contact_us");

		return;	 
	}	

	private function set_messages_info()
	{
		$filters=array();

		$this->data['raw_page_url']=get_link("admin_contact_us");
		
		$this->initialize_filters($filters);

		$total=$this->contact_us_manager_model->get_total_messages($filters);
		if($total)
		{
			$per_page=20;
			$page=1;
			if($this->input->get("page"))
				$page=(int)$this->input->get("page");

			$start=($page-1)*$per_page;
			$filters['start']=$start;
			$filters['length']=$per_page;
			
			$this->data['messages_info']=$this->contact_us_manager_model->get_messages($filters);
			
			$end=$start+sizeof($this->data['messages_info'])-1;

			unset($filters['start']);
			unset($filters['length']);

			$this->data['messages_current_page']=$page;
			$this->data['messages_total_pages']=ceil($total/$per_page);
			$this->data['messages_total']=$total;
			$this->data['messages_start']=$start+1;
			$this->data['messages_end']=$end+1;		
		}
		else
		{
			$this->data['messages_current_page']=0;
			$this->data['messages_total_pages']=0;
			$this->data['messages_total']=$total;
			$this->data['messages_start']=0;
			$this->data['messages_end']=0;
		}
			
		$this->data['filter']=$filters;

		return;
	}

	private function initialize_filters(&$filters)
	{
		if($this->input->get("ref_id"))
			$filters['ref_id']=$this->input->get("ref_id");

		if($this->input->get("sender"))
			$filters['sender']=$this->input->get("sender");

		if($this->input->get("time"))
			$filters['time']=$this->input->get("time");

		if($this->input->get("subject"))
			$filters['subject']=$this->input->get("subject");

		if($this->input->get("status"))
			$filters['status']=$this->input->get("status");

		persian_normalize($filters);

		return;
	}

	public function details($message_id)
	{
		if($this->input->post("post_type")==="edit_message")
			return $this->edit_message($message_id);

		if($this->input->post("post_type")==="delete_message")
			return $this->delete_message($message_id);

		$this->data['message_id']=$message_id;
		$message_info=$this->contact_us_manager_model->get_message($message_id);

		$this->data['message']=get_message();
		$this->data['lang_pages']=get_lang_pages(get_admin_contact_us_message_details_link($message_id,TRUE));
		$this->data['header_title']=$this->lang->line("message_details")." ".$message_id;

		$this->send_admin_output("message_details");

		return;
	}

	private function delete_message($message_id)
	{
		$this->post_manager_model->delete_post($post_id);

		set_message($this->lang->line('post_deleted_successfully'));

		return redirect(get_link("admin_post"));
	}

	private function edit_message($message_id)
	{
		$post_props=array();
		$post_props['categories']=$this->input->post("categories");

		$post_props['post_date']=$this->input->post('post_date');
		$post_props['post_active']=(int)($this->input->post('post_active') === "on");
		$post_props['post_allow_comment']=(int)($this->input->post('post_allow_comment') === "on");
		
		$post_content_props=array();
		foreach($this->language->get_languages() as $lang=>$name)
		{
			$post_content=$this->input->post($lang);
			$post_content['pc_content']=$_POST[$lang]['pc_content'];
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
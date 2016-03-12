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
		if($this->input->post("post_type")==="send_response")
			return $this->send_response($message_id);

		if($this->input->post("post_type")==="delete_message")
			return $this->delete_message($message_id);

		$this->data['message_id']=$message_id;
		$info=$this->contact_us_manager_model->get_messages(
			array("message_id"=>$message_id)
		);
		if(isset($info[0]))
			$this->data['info']=$info[0];
		else
			$this->data['info']="";

		$this->data['message']=get_message();
		$this->data['lang_pages']=get_lang_pages(get_admin_contact_us_message_details_link($message_id,TRUE));
		$this->data['header_title']=$this->lang->line("message")." ".$message_id.$this->lang->line("header_separator").$this->lang->line("contact_us");

		$this->send_admin_output("contact_us_details");

		return;
	}

	private function delete_message($message_id)
	{
		$res=$this->contact_us_manager_model->delete($message_id);

		if($res)
			set_message($this->lang->line('message_deleted_successfully'));
		else
			set_message($this->lang->line('message_cant_be_deleted'));

		return redirect(get_link("admin_contact_us"));
	}

	private function send_response($message_id)
	{
		$subject=$this->input->post("subject");
		$response=trim($this->input->post("content"));
		$lang=$this->input->post("language");

		$info=$this->contact_us_manager_model->get_messages(
			array("message_id"=>$message_id)
		);
		if(isset($info[0]))
			$info=$info[0];
		else
			return redirect(get_link("admin_contact_us"));

		if($response)
		{
			$response=persian_normalize($response);
			$subject=persian_normalize($subject);

			$this->lang->load('ae_general_lang',$lang);
			$this->lang->load('email_lang',$lang);	

			$subject.=$this->lang->line("header_separator").$info['cu_ref_id'].$this->lang->line("header_separator").$this->lang->line("main_name");

			$mo_response=$subject."\n".$response;
			$this->contact_us_manager_model->set_response($message_id,$mo_response);
			
			$response_to=$this->lang->line("response_to")."<br>".nl2br($info['cu_message_content']);

			$message=str_replace(
				array('$content','$slogan','$response_to'),
				array(nl2br($response),$this->lang->line("slogan"),$response_to)
				,$this->lang->line("email_template")
			);

			burge_cmf_send_mail($info['cu_sender_email'],$subject,$message);

			set_message($this->lang->line("response_sent_successfully"));
		}
		else
			set_message($this->lang->line("response_content_is_empty"));

		return redirect(get_admin_contact_us_message_details_link($message_id));
	}

	public function send_new()
	{
		if($this->input->post("post_type")==="send_message")
		{
			$receivers=$this->input->post("receivers");
			$subject=$this->input->post("subject");
			$content=$this->input->post("content");
			$lang=$this->input->post("language");

			if($receivers && $subject && $content)
			{
				$receivers=preg_replace("/\s*[\n]+\s*/", ";", $receivers);
				$receivers=explode(";", $receivers);
				
				$this->lang->load('ae_general_lang',$lang);
				$this->lang->load('email_lang',$lang);	

				$subject.=$this->lang->line("header_separator").$this->lang->line("main_name");

				$message=str_replace(
					array('$content','$slogan','$response_to'),
					array($content,$this->lang->line("slogan"),"")
					,$this->lang->line("email_template")
				);

				$this->log_manager_model->info("CONTACT_US_NEW_MESSAGE",array(
					"receivers"=>implode(";", $receivers)
					,"subject"=>$subject
					,"message"=>$content
				));

				burge_cmf_send_mail($receivers,$subject,$message);

				set_message($this->lang->line("message_sent_successfully"));
				redirect(get_link("admin_contact_us_send_new"));
				return;
			}
			else
				set_message($this->lang->line("fill_all_fields"));

			
		}

		$this->data['message']=get_message();
		$this->data['lang_pages']=get_lang_pages(get_link("admin_contact_us_send_new",TRUE));
		$this->data['header_title']=$this->lang->line("send_new_message");

		$this->send_admin_output("contact_us_send_new");

	}
}
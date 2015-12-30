<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Constant extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->load->model("constant_manager_model");

		$this->lang->load('ae_constant',$this->selected_lang);

		if($this->input->post())
		{
			if($this->input->post("post_type") === "constants_list")
				return $this->set_constants();

			if($this->input->post("post_type") === "add_constant")
				return $this->add_constant();	
		}

		$this->data['message']=get_message();

		$this->data['constants']=$this->constant_manager_model->get_all();
	
		$this->data['lang_pages']=get_lang_pages(get_link("admin_constant",TRUE));
		$this->data['header_title']=$this->lang->line("constants");
		
		$this->send_admin_output("constant");

		return;	 
	}

	private function add_constant()
	{
		$key=$this->input->post("key");
		$key=preg_replace("/\s+/", "_",trim($key));
		$value=$this->input->post("value");
		
		if(!$key || !$value)
			$this->data['message']=$this->lang->line("fill_all_fields");
		else
		{
			$res=$this->constant_manager_model->set($key,$value);
			
			$this->data['message']=$this->lang->line("added_successfully");
		}

		return;
	}

	private function modify_users()
	{
		$res=FALSE;
		$users=$this->user_manager_model->get_all_users_info();
		foreach ($users as $user)
		{
			$uid=$user['user_id'];

			//check if user has been deleted
			$delete_string="delete_user_id_".$uid;
			$post_delete=$this->input->post($delete_string);
			if($post_delete==="on")
			{
				$this->user_manager_model->delete_user($uid,$user['user_email']);
				$res=TRUE;

				continue;
			}

			//check if password has been changed
			$pass_string="pass_user_id_".$uid;
			$post_pass=$this->input->post($pass_string);
			$post_pass=trim($post_pass);
			if($post_pass)
			{
				$this->user_manager_model->change_user_pass($user['user_email'],$post_pass);
				$res=TRUE;		
			}

			//name and code changes
			$name=$this->input->post("name_user_id_".$uid);
			$code=$this->input->post("code_user_id_".$uid);
			if($name && $code)
			{
				$this->user_manager_model->change_user_props($uid,array(
					"user_name"=>$name
					,"user_code"=>$code
				));
				$res=TRUE;
			}
			
		}

		if($res)
			$this->data['message']=$this->lang->line("modfied_successfully");
	}
}
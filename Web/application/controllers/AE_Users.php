<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Users extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function search($name)
	{
		$max_count=5;
		$name=urldecode($name);
		$name=persian_normalize($name);

		$results=$this->user_manager_model->get_users(array(
			"name"=>$name
			,"start"=>0
			,"length"=>$max_count
		));

		$ret=array();

		foreach ($results as $res)	
			$ret[]=array(
				"id"=>$res['user_id']
				,"name"=>$res['user_code']." - ".$res['user_name']
			);

		$this->output->set_content_type('application/json');
    	$this->output->set_output(json_encode($ret));

    	return;
	}


	public function index($user_id)
	{
		$user_id=(int)$user_id;

		$this->load->model("user_manager_model");

		$this->lang->load('ae_user',$this->selected_lang);

		if($this->input->post())
		{
			$this->lang->load('error',$this->selected_lang);

			if($this->input->post("post_type") === "add_user")
				return $this->add_user();

			if($this->input->post("post_type") === "modify_users")
				return $this->modify_user($user_id);

			if($this->input->post("post_type") === "add_user_group")
				return $this->add_user_group();

			if($this->input->post("post_type") === "modify_user_groups")
				return $this->modify_user_groups();
		}

		$this->data['users_info']=$this->user_manager_model->get_all_users_info();
		$this->data['user_groups']=$this->user_manager_model->get_all_user_groups();
		$this->data['user_id']=$user_id;
		
		$this->data['message']=get_message();
		$this->data['lang_pages']=get_lang_pages(get_admin_user_details_link($user_id,TRUE));
		$this->data['header_title']=$this->lang->line("users");
		
		$this->send_admin_output("user");

		return;	 
	}

	private function add_user()
	{
		$result=FALSE;

		$user_email=$this->input->post("email");
		$user_pass=$this->input->post("password");
		$user_code=$this->input->post("code");
		$user_name=$this->input->post("name");
		$user_group_id=(int)$this->input->post("group_id");

		if(!$user_pass || !$user_email)
			set_message($this->lang->line("fill_all_fields"));
		else
		{
			$user_id=$this->user_manager_model->add_if_not_exist(array(
				"user_name"			=> $user_name
				,"user_email"		=> $user_email
				,"user_code"		=> $user_code
				,"user_pass"		=> $user_pass
				,"user_group_id"	=> $user_group_id
			));

			if($user_id)
			{
				set_message($this->lang->line("added_successfully"));
				$result=TRUE;
			}
			else
				set_message($this->lang->line("repeated_email"));
		}

		if($result)
			return redirect(get_admin_user_details_link($user_id));
		else
			return redirect(get_link("admin_user")."#add_user"); 
	}

	private function modify_user($user_id)
	{
		$user_info=$this->user_manager_model->get_user($user_id);

		if($this->input->post("delete") === "on")
		{
			$this->user_manager_model->delete_user($user_id);
			set_message($this->lang->line("user_deleted_successfully"));
			redirect(get_link("admin_user"));
			return ;
		}

		//check if password has been changed
		$password=$this->input->post("password");
		$password=trim($password);
		if($password)
		{
			$this->user_manager_model->change_user_pass($user_info['user_email'],$password);

			set_message($this->lang->line("user_password_changed_successfully"));
			redirect(get_admin_user_details_link($user_id));
			return ;		
		}

		//name and code changes
		$name=$this->input->post("name");
		$code=$this->input->post("code");
		$group_id=$this->input->post("group_id");
		$this->user_manager_model->change_user_props($user_id,array(
			"user_name"=>$name
			,"user_code"=>$code
			,"user_group_id"=>$group_id
		));

		set_message($this->lang->line("modfied_successfully"));
		redirect(get_admin_user_details_link($user_id));
		return;
	}

	private function add_user_group()
	{
		
		$ug_name=$this->input->post("name");

		if(!$ug_name)
			set_message($this->lang->line("fill_all_fields"));
		else
		{
			$res=$this->user_manager_model->add_user_group(array(
				"ug_name"=>$ug_name
			));

			set_message($this->lang->line("ug_added_successfully"));
		}

		return redirect(get_link("admin_user")); 
	}

	private function modify_user_groups()
	{
		$res=FALSE;
		$user_groups=$this->user_manager_model->get_all_user_groups();

		foreach ($user_groups as $ug)
		{
			$ug_id=$ug['ug_id'];

			//check if user group has been deleted
			$delete_string="delete_user_group_id_".$ug_id;
			$post_delete=$this->input->post($delete_string);
			if($post_delete==="on")
			{
				$this->user_manager_model->delete_user_group($ug_id);
				$res=TRUE;

				continue;
			}

			//name change
			$name=$this->input->post("ug_name_id_".$ug_id);
			if($name)
			{
				$this->user_manager_model->change_user_group_props($ug_id,array(
					"ug_name"=>$name
				));
				$res=TRUE;
			}
			
		}

		if($res)
			set_message($this->lang->line("modfied_successfully"));

		return redirect(get_link("admin_user"));
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Change_Pass extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->lang->load('admin_change_pass',$this->selected_lang);

		if($this->input->post())
		{
			$this->lang->load('error',$this->selected_lang);

			if($this->input->post("prev_pass") && $this->input->post("new_pass") && $this->input->post("repeat_pass"))
			{
				if($this->input->post("new_pass") === $this->input->post("repeat_pass"))
				{
					$prev_pass=$this->input->post("prev_pass");
					$new_pass=$this->input->post("new_pass");

					$this->load->model("user_manager_model");
					if($this->user_manager_model->change_logged_user_pass($prev_pass,$new_pass))
					{
						$message=$this->lang->line("success_password_change");
					}
					else				
						$message=$this->lang->line("match_prev_password");
				}
				else
					$message=$this->lang->line("match_repeat_password");
			}
			else
				$message=$this->lang->line("fill_all_fields");
		}

		if(isset($message))
			$this->data['message']=$message;

		$this->data['lang_pages']=get_lang_pages(get_link("admin_change_pass",TRUE));
		$this->data['header_title']=$this->lang->line("change_pass");
		
		$this->send_admin_output("change_pass");

		return;	 
	}
}
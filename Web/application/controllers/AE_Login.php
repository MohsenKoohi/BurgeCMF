<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Login extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->load->model("user_manager_model");
		if($this->user_manager_model->has_user_logged_in())
		{
			redirect(get_link("admin_url"));
			return;
		}
		
		$lang=$this->language->get();
		$this->lang->load('ae_login',$lang);

		if($this->input->post())
		{
			$this->lang->load('error',$lang);

			if($this->input->post("email") && $this->input->post("pass"))
			{
				if(verify_captcha($this->input->post("captcha")))
				{
					$pass=$this->input->post("pass");
					$email=$this->input->post("email");
					
					$this->load->model("user_manager_model");
					if($this->user_manager_model->login($email,$pass))
					{
						redirect(get_link("admin_url"));
						return;
					}
					else				
						$message=$this->lang->line("incorrect_fields");
				}
				else
					$message=$this->lang->line("captcha");
			}
			else
				$message=$this->lang->line("fill_all_fields");
		}

		if(isset($message))
			$this->data['message']=$message;
		else
			$this->data['message']=get_message();
		
		$this->data['lang_pages']=get_lang_pages(get_link("admin_login",TRUE));
		$this->data['header_title']=$this->lang->line("login");
		$this->data['captcha']=get_captcha();
		
		$this->send_admin_output("login");

		return;	 
	}
}
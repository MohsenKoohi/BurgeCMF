<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

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
		$this->lang->load('admin_login',$lang);

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

		
		$data=get_initialized_data();
		
		$data['lang_pages']=get_lang_pages(get_link("admin_login",TRUE));

		if(isset($message))
			$data['message']=$message;
		
		$data['header_title']=$this->lang->line("login");
		foreach($this->lang->language as $index => $val)
			$data[$index."_text"]=$val;
	
		$data['captcha']=get_captcha();
		
		$tpl_dir=get_template_dir($lang);
		$this->load->library('parser');
		$this->parser->parse($tpl_dir.'/admin/header',$data);
		$this->parser->parse($tpl_dir.'/admin/login',$data);
		$this->parser->parse($tpl_dir.'/admin/footer',$data);			

		return;	 
	}
}
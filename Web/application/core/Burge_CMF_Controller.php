<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Burge_CMF_Controller extends CI_Controller{
	public $user;
	
	protected $selected_lang;
	protected $default_lang;
	protected $all_langs;

	protected $hit_level=0;

	protected $data;
	public $in_admin_env=FALSE;

	public function __construct()
	{
		parent::__construct();

		$parts=explode("/",uri_string());
		if(sizeof($parts)>0 &&  $parts[0]===ADMIN_URL_FOLDER)
		{
			$module="dashboard";
			if(sizeof($parts)>1)
				$module=$parts[1];

			$this->load->model("user_manager_model");
			$this->user=& $this->user_manager_model->get_user_info();

			$this->load->model("access_manager_model");
			$access_result=$this->access_manager_model->check_access($module,$this->user);
			if(!$access_result)
			{
				redirect(get_link("home_url"));
				return;
			}

			//Now we are sure the user has been logged
			//Do every general work here
			
			$this->in_admin_env=TRUE;
		}

		$this->selected_lang=Language::get();
		$this->default_lang=Language::get_default_language();
		$this->all_langs=Language::get_languages();


		if($this->in_admin_env)
		{
			//setting initial common data for the admin env
			$this->lang->load('admin_general',$this->selected_lang);	

			$this->data=get_initialized_data(FALSE);
			$this->data['user_logged_in']=TRUE;
		}
		else
		{
			//since we are in customer env., we should count this hit
			if($this->hit_level>=0)
			{
				$this->load->model("hit_counter_model");
				$this->hit_counter_model->count($parts,$this->hit_level);
			}

			$this->lang->load('customer_general',$this->selected_lang);	

			$this->data=get_initialized_data(TRUE);	

			$this->data['header_title']=$this->lang->line("header_title");
			$this->data['header_meta_description']=$this->lang->line("header_meta_description");
			$this->data['header_meta_keywords']=$this->lang->line("header_meta_keywords");

		}
		
		return;
	}

	protected function send_admin_output($view_file)
	{

		foreach($this->lang->language as $index => $val)
			$this->data[$index."_text"]=$val;

		//loading side menu items
		$this->load->model("module_manager_model");
		$this->data['side_menu_modules']=$this->module_manager_model->get_user_modules_names($this->user->get_id());
	
		$this->load->library('parser');
		$this->parser->parse($this->get_admin_view_file("header"),$this->data);
		$this->parser->parse($this->get_admin_view_file($view_file),$this->data);
		$this->parser->parse($this->get_admin_view_file("footer"),$this->data);			
	}

	public function get_admin_view_file($file_name)
	{
		$ret="";
		$view_folder=APPPATH."views/";
		
		$path=$this->selected_lang."/admin/".$file_name;
		if(file_exists($view_folder.$path.".php"))
		 	$ret=$path;
		else
			foreach($this->all_langs as $lang=>$value)
			{
			 	$path=$lang."/admin/".$file_name;
				if(file_exists($view_folder.$path.".php"))
				{
					$ret=$path;
					break;
				}
			}
			
		return $ret;
	}

	protected function send_customer_output($view_file)
	{
		foreach($this->lang->language as $index => $val)
			$this->data[$index."_text"]=$val;

		$this->load->library('parser');
		$this->parser->parse($this->get_customer_view_file("header"),$this->data);
		$this->parser->parse($this->get_customer_view_file($view_file),$this->data);
		$this->parser->parse($this->get_customer_view_file("footer"),$this->data);			
	}

	public function get_customer_view_file($file_name)
	{
		$ret="";
		$view_folder=APPPATH."views/";
		
		$path=$this->selected_lang."/customer/".$file_name;
		if(file_exists($view_folder.$path.".php"))
		 	$ret=$path;
		else
			foreach($this->all_langs as $lang=>$value)
			{
			 	$path=$lang."/customer/".$file_name;
				if(file_exists($view_folder.$path.".php"))
				{
					$ret=$path;
					break;
				}
			}
			
		return $ret;
	}

}

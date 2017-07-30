<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Burge_CMF_Controller extends CI_Controller{
	//here we require to set/unset if we want to initialize session 
	//and sending headers to the browser or not
	//since some times we want to redirect for example for short links
	//but CodeIgniter always sends headers, which prevents some bots to be redirected

	public $user;
	
	public $selected_lang;
	public $default_lang;
	public $all_langs;

	//visit logging should go to the end of controller
	//which allows the hit_level to change during the controller execution
	//and also allows not logging redirected visits
	protected $hit_level=0;

	protected $data;
	public $in_admin_env=FALSE;

	private $view_directories;

	public function __construct()
	{
		parent::__construct();

		mb_internal_encoding("UTF-8");

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
				//there is a problem here which should be fixed in our next versions
				//when the connection has been started from an iframe
				//or from a javascript ajax, 
				//we shouldn't redirect 
				//we should send a response indicates that there is no access
				//and the connection starter should be able to intrepret it
				//and redirect the main page to the home_url
				
				redirect(get_link("home_url")."#");
				return;
			}

			//Now we are sure the user has been logged
			//Do every general work here
			
			$this->in_admin_env=TRUE;
		}

		$this->selected_lang=Language::get();
		$this->default_lang=Language::get_default_language();
		$this->all_langs=Language::get_languages();

		$this->set_view_directories();

		if($this->in_admin_env)
		{
			//setting initial common data for the admin env
			$this->lang->load('ae_general',$this->selected_lang);	

			$this->data=get_initialized_data(FALSE);
			
			$this->data['selected_lang']=$this->selected_lang;
			$this->data['all_langs']=$this->all_langs;

			if($this->user)
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

			$this->lang->load('ce_general',$this->selected_lang);	

			$this->data=get_initialized_data(TRUE);	

			$this->data['selected_lang']=$this->selected_lang;
			$this->data['all_langs']=$this->all_langs;

			$this->data['header_title']=$this->lang->line("header_title");
			$this->data['header_meta_description']=$this->lang->line("header_meta_description");
			$this->data['header_meta_keywords']=$this->lang->line("header_meta_keywords");
		}
		
		return;
	}

	private function set_view_directories()
	{
		$this->view_directories=array();

		if(!isset($this->selected_lang['en']))
			$this->view_directories[]="en";

		foreach($this->all_langs as $lang => $value)
			$this->view_directories[]=$lang;

		return;
	}

	protected function send_admin_output($view_file)
	{

		foreach($this->lang->language as $index => $val)
			$this->data[$index."_text"]=$val;

		//loading side menu items
		$this->load->model("module_manager_model");
		
		if($this->user)
			$modules=$this->module_manager_model->get_user_modules_names($this->user);
		else
			$modules=array();

		//some modules may have a number or text which may change in different times
		//and like the user knows it changes. for example for messages module
		//but since it requires all modules to be loaded,  it increases the process
		//time and we don't run by default
		if(FALSE)
			foreach ($modules as &$module)
			{
				$module_id=$module['id'];
				$model_name=$module['model'];
				if(!$model_name)
					continue;
				$this->load->model($model_name."_model");
				$model=$this->{$model_name."_model"};

				if(!method_exists($model, "get_sidebar_text"))
					continue;

				$text=$model->{"get_sidebar_text"}($module_id);

				$module['name'].=$text;
			}

		$this->data['side_menu_modules']=$modules;
	
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
			foreach($this->view_directories as $dir)
			{
			 	$path=$dir."/admin/".$file_name;
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

		$this->load->model("category_manager_model");
		$categories=$this->category_manager_model->get_all();
		$this->data['categories']=$categories[0]['children'];

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
			foreach($this->view_directories as $dir)
			{
			 	$path=$dir."/customer/".$file_name;
				if(file_exists($view_folder.$path.".php"))
				{
					$ret=$path;
					break;
				}
			}
			
		return $ret;
	}

}

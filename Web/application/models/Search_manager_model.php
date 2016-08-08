<?php
class Search_manager_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		return;
	}

	public function install()
	{
		
		return;
	}

	public function uninstall()
	{

		return;
	}		

	public function search($text,$filter)
	{
		$this->load->model("module_manager_model");
		$modules=$this->module_manager_model->get_all_modules_info($this->selected_lang);

		$results=array();

		foreach ($modules as $module)
		{
			$name=$module['name'];
			$model_name=$module['model'];
			if(!$model_name)
				continue;
		
			$this->load->model($model_name."_model");
			$model=$this->{$model_name."_model"};

			if(!method_exists($model, "search_module"))
				continue;

			$results[$name]=$model->{"search_module"}($text, $filter);
		}

		return $results;
	}
}
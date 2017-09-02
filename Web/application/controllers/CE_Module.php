<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CE_Module extends Burge_CMF_Controller {
	protected $hit_level=1;

	private $input_pass_hash = "LKJADS(uadsf";
	private $cron_max_execution_time = 120; 		//in seconds

	function __construct()
	{
		parent::__construct();
	}

	public function cron()
	{	
		//checking the password
		//you need to create a cron job on the server to load this page (using php, lynx or ...)
		//http://localhost/Web/BurgeCMF/Web/cron?pw=$(echo `date +"%Y/%m/%d %H:%M:%S"`"LKJADS(uadsf"|sha1sum)
		//which LKJADS(uadsf is your $input_pass_hash

		if(TRUE)
		{
			$get_pass=$this->input->get("pw");
			$get_pass=trim($get_pass," -");

			date_default_timezone_set("Europe/Paris");
			$pass=sha1(date("Y/m/d H:i:s").$this->input_pass_hash."\n");
			if($pass !== $get_pass)
				exit();
		}

		$this->load->model("module_manager_model");
		$modules=$this->module_manager_model->get_cron_modules();
		
		$st=time();

		foreach($modules as $m)
		{
			$model_name=$m['model_name'];
			if(!$model_name)
				continue;

			$this->load->model($model_name."_model");
			$model=$this->{$model_name."_model"};

			if(method_exists($model, "cron"))
			{
				$remaining_seconds = $this->cron_max_execution_time - (time()-$st);
				$model->{"cron"}($remaining_seconds);
			}

			//echo "<br>\n".$model_name."<br>\n";

			$this->module_manager_model->set_cron_execution($m['module_id']);

			if(time()-$st > $this->cron_max_execution_time)
				break;
		}

		return;		
	}
}
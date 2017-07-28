<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Constant extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model("constant_manager_model");
	}

	public function index()
	{
		$this->lang->load('ae_constant',$this->selected_lang);

		if($this->input->post())
		{
			if($this->input->post("post_type") === "constants_list")
				return $this->modify_constants();

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
		$key=preg_replace("/\s+/", "_",persian_normalize(trim($key)));
		$value=persian_normalize($this->input->post("value"));
		
		if(!$key || !$value)
			$this->data['message']=$this->lang->line("fill_all_fields");
		else
		{
			$res=$this->constant_manager_model->set($key,$value);
			
			set_message($this->lang->line("added_successfully"));
		}

		return redirect(get_link("admin_constant"));
	}

	private function modify_constants()
	{
		$res=FALSE;
		$constants=$this->constant_manager_model->get_all();
		foreach ($constants as $cons)
		{
			$key=$cons['constant_key'];

			//check if user has been deleted
			$delete_string="delete_".$key;
			$post_delete=$this->input->post($delete_string);
			if($post_delete==="on")
			{
				$this->constant_manager_model->delete($key);
				$res=TRUE;

				continue;
			}

			$changed_string="changed_".$key;
			$post_changed=$this->input->post($changed_string);
			if($post_changed !== "on")
				continue;

			$value_string="value_".$key;
			$post_value=$this->input->post($value_string);
			$value=persian_normalize(trim($post_value));
			if(!$value)
				$value=false;

			$this->constant_manager_model->set($key,$value);
			$res=TRUE;			
		}

		if($res)
			set_message($this->lang->line("modified_successfully"));

		return redirect(get_link("admin_constant"));
	}
}
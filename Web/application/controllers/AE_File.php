<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_File extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model("file_manager_model");
	}

	public function index()
	{
		
		$this->data['message']=get_message();

		$this->lang->load('ae_file',$this->selected_lang);
	
		$this->data['lang_pages']=get_lang_pages(get_link("admin_file",TRUE));
		$this->data['header_title']=$this->lang->line("files");
		
		$this->send_admin_output("file");
	
		return;		
	}

	public function inline()
	{
		$this->load->library('parser');
		//$this->data[]
		
		$this->parser->parse($this->get_admin_view_file("file_inline"),$this->data);
		
	}

	public function action($action_name)
	{
		echo $action_name;
	}

	public function conf()
	{
		echo $this->file_manager_model->get_conf();

		return;
	}

	public function lang($lang)
	{
		echo file_get_contents(SCRIPTS_DIR."/roxy/lang/".$lang.".json");

		return;
	}
}
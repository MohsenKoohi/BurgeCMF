<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Post extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->lang->load('ae_post',$this->selected_lang);

		$this->data['lang_pages']=get_lang_pages(get_link("admin_post",TRUE));
		$this->data['header_title']=$this->lang->line("posts");

		$this->send_admin_output("post");

		return;	 
	}

	public function search()
	{
		echo "<a href='".get_admin_post_details_link(12)."' onclick='window.parent.window.show_post_details(12);'>Click</a>";
	}

	public function details($id)
	{
		echo "<h1>Details ".$id."</h1>";
	}

}
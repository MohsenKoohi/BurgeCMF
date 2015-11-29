<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Logout extends CI_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->load->model("user_manager_model");
		$this->user_manager_model->set_user_logged_out();
		
		redirect(get_link("admin_no_access"));
		return;		
	}
}
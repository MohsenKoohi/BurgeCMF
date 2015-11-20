<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{		
	
		$this->data['header_description']='';
		$this->data['header_keywords']='';
		$this->data['header_canonical_url']=HOME_URL;
		$this->data['header_title']='';

		$this->send_customer_output("home");

		return;
	}
}
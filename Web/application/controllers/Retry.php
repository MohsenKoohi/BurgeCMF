<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retry extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$prev=$this->input->get("prev");

		set_message("مدت ارسال شما از حد مقرر بیشتر شده است.<br>لطفا اطلاعات را مجددا ثبت نمایید");

		redirect($prev);
	}
}
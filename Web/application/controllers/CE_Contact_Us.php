<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CE_Contact_Us extends Burge_CMF_Controller {
	protected $hit_level=2;

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{	
		if($this->input->post())
			return $this->add_message();

		$this->data['message']=get_message();

		$this->data['captcha']=get_captcha();

		$this->data['lang_pages']=get_lang_pages(get_link("customer_contact_us",TRUE));
		
		$this->data['header_meta_robots']="noindex";

		$this->data['header_title']=$this->lang->line("contact_us").$this->lang->line("header_separator").$this->data['header_title'];
		$this->data['header_meta_description']=$this->data['header_title'];
		$this->data['header_meta_keywords']=$this->data['header_title'];

		$this->data['header_canonical_url']=get_link("customer_contact_us");

		$this->send_customer_output("contact_us");

		return;
	}

	private function add_message()
	{
		if(verify_captcha($this->input->post("captcha")))
		{
			$fields=array("name","email","department","subject","content");
			$props=array();
			foreach($fields as $field)
				$props[$field]=$this->input->post($field);

			if($props['name']  && $props['email'] && $props['content'] )
			{
				$this->load->model("contact_us_manager_model");
				$ref_id=$this->contact_us_manager_model->add_message($props);

				set_message(str_replace("ref_id",$ref_id,$this->lang->line("message_sent_successfully")));
			}
			else
				set_message($this->lang->line("fill_all_fields"));
		}
		else
			set_message($this->lang->line("captcha_incorrect"));

		redirect(get_link("customer_contact_us"));

		return;
	}
}
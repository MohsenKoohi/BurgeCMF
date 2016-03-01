<?php
class Contact_us_manager_model extends CI_Model {
	private $contact_us_table_name="contact_us";

	public function __construct()
	{
		parent::__construct();
	}

	public function install()
	{
		$table=$this->db->dbprefix($this->contact_us_table_name); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $table (
				`cu_id` int NOT NULL
				,`cu_ref_id` char(20) DEFAULT NULL
				,`cu_sender_name` char(200) DEFAULT NULL
				,`cu_sender_email` char(200) DEFAULT NULL
				,`cu_message_time` char(20) DEFAULT NULL
				,`cu_message_department` varchar(128) DEFAULT NULL
				,`cu_message_subject` varchar(1024) DEFAULT NULL
				,`cu_message_content` varchar(4096) DEFAULT NULL
				,`cu_response` varchar(4096) DEFAULT NULL
				,`cu_response_time` char(20) DEFAULT NULL
				,`cu_response_user_id` int DEFAULT NULL								
				,PRIMARY KEY (cu_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");
		$this->module_manager_model->add_module("contact_us","contact_us_manager");
		$this->module_manager_model->add_module_names_from_lang_file("contact_us");
		
		return;
	
	}

	public function uninstall()
	{

		return;
	}	

	public function get_dashboard_info()
	{
		return "";
		$CI=& get_instance();
		$lang=$CI->language->get();
		$CI->lang->load('ae_module',$lang);		
		
		$data=array();
		$data['modules']=$this->get_all_modules_info($lang);
		$data['total_text']=$CI->lang->line("total");
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("module_dashboard"),$data,TRUE);
		
		return $ret;		
	}

	public function add($email,$message)
	{
		$this->db->insert("contact_messages",array(
			"cm_email"=>$email,
			"cm_message"=>$message,
			"cm_message_time"=>jdate("Y/m/d H:i:s")
		));

		$id=$this->db->insert_id();
		$idmod=$id%100000;
		$tail=random_string("numeric",5);
		$ref_id=jdate("Ymd").$tail.sprintf("%05d",$idmod);

		$this->db->set("cm_ref_id", $ref_id);
		$this->db->where("cm_id",$id);
		$this->db->limit(1);
		$this->db->update('contact_messages');

		return $ref_id;
	}
}
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
				`cu_id` int AUTO_INCREMENT 
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

	public function add_message($props)
	{
		$this->db->insert($this->contact_us_table_name,array(
			"cu_sender_name"=>$props['name']
			,"cu_sender_email"=>$props['email']
			,"cu_message_time"=>get_current_time()
			,"cu_message_department"=>$props['department']
			,"cu_message_subject"=>$props['subject']
			,"cu_message_content"=>$props['content']
		));

		$id=$this->db->insert_id();
		$idmod=$id%100000;
		$tail=random_string("numeric",5);
		$date_function=DATE_FUNCTION;
		$ref_id=$date_function("Ymd").$tail.sprintf("%05d",$idmod);

		$this->db->set("cu_ref_id", $ref_id);
		$this->db->where("cu_id",$id);
		$this->db->limit(1);
		$this->db->update($this->contact_us_table_name);

		$props["ref_id"]=$ref_id;
		$this->log_manager_model->info("CONTACT_US_ADD",$props);

		return $ref_id;
	}
}
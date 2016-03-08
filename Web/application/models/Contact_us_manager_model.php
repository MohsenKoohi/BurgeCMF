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
		$CI=& get_instance();
		$lang=$CI->language->get();
		$CI->lang->load('ae_contact_us',$lang);		
		
		$data=array();
		$info=$this->get_statistics();
		$data['responded']=$info['responded'];
		$data['total']=$info['total'];
		$data['responded_text']=$CI->lang->line("responded");
		$data['total_text']=$CI->lang->line("total");
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("contact_us_dashboard"),$data,TRUE);
		
		return $ret;		
	}

	private function get_statistics()
	{
		$tb=$this->db->dbprefix($this->contact_us_table_name);

		return $this->db->query("
			SELECT 
				(SELECT COUNT(*) FROM $tb) as total, 
				(SELECT COUNT(*) FROM $tb WHERE !ISNULL(`cu_response_time`)) as responded
			")->row_array();
	}

	public function get_messages($filter)
	{
		$this->db
			->select($this->contact_us_table_name.".*")
			->select("user.user_name, user.user_code")
			->from($this->contact_us_table_name)
			->join("user",$this->contact_us_table_name.".cu_response_user_id = user.user_id","LEFT");

		$this->set_filters($filter);

		$results=$this->db->get();

		return $results->result_array();
	}

	public function get_total_messages($filter)
	{
		$this->db
			->select("COUNT(*) as count")
			->from($this->contact_us_table_name);

		$this->set_filters($filter);

		$results=$this->db->get();

		return $results->row_array()['count'];
	}

	private function set_filters($filter)
	{
		if(isset($filter['ref_id']))
		{
			$id=trim($filter['ref_id']);
			$ref_id="%".str_replace(" ","%",$id)."%";
			$this->db->where("( `cu_ref_id` LIKE '$ref_id' OR `cu_id` = '$id' )");
		}

		if(isset($filter['sender']))
		{
			$sender="%".str_replace(" ","%",trim($filter['sender']))."%";
			$this->db->where(" ( `cu_sender_name` LIKE '$sender' OR `cu_sender_name` LIKE '$sender' )");
		}

		if(isset($filter['time']))
		{
			$time=trim($filter['time']);
			$time="%".str_replace(" ","",$time)."%";
			$this->db->where("( `cu_message_time` LIKE '$time' OR `cu_response_time` LIKE '$time'  )");
		}

		if(isset($filter['subject']))
		{
			$subject=trim($filter['subject']);
			$subject="%".str_replace(" ","%",$subject)."%";
			$this->db->where("( `cu_message_subject` LIKE '$subject' OR `cu_message_department` LIKE '$subject'  )");
		}

		if(isset($filter['status']))
		{
			if($filter['status'] === "responded")
				$this->db->where("!ISNULL (`cu_response_time`)");

			if($filter['status'] === "not_responded")
				$this->db->where("ISNULL (cu_response_time)");
		}

		if(isset($filter['message_id']))
			$this->db->where("cu_id",(int)$filter['message_id']);


		if(isset($filter['start']))
			$this->db->limit($filter['length'],$filter['start']);

		//echo $this->db->get_compiled_select();


		$this->db->order_by("cu_id DESC");

		return;
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

	public function set_response($message_id,$response)
	{
		$props=array(
			'cu_response'=>$response
			,'cu_response_time'=>get_current_time()
			,'cu_response_user_id'=>$this->user_manager_model->get_user_info()->get_id()
		);

		$this->db->set($props);
		$this->db->where("cu_id",$message_id);
		$this->db->limit(1);
		$this->db->update($this->contact_us_table_name);

		$props['cu_id']=$message_id;

		$this->log_manager_model->info("CONTACT_US_REPLY",$props);
		
		return ;
	}

	public function delete($message_id)
	{
		//return FALSE;

		$this->db
			->where("cu_id",$message_id)
			->delete($this->contact_us_table_name);

		$this->log_manager_model->info("CONTACT_US_DELETE",array("cu_id"=>$message_id));

		return TRUE;
	}
}
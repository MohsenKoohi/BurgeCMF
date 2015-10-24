<?php
class User_manager_model extends CI_Model
{
	private $user_info=NULL;
	private $user_info_initialized=FALSE;

	public function __construct()
	{
		parent::__construct();

		$this->load->library("core/user");
		
		return;
	}

	public function install()
	{
		$user_table=$this->db->dbprefix('user'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $user_table (
				`user_id` int AUTO_INCREMENT NOT NULL,
				`user_email` char(100) NOT NULL UNIQUE,
				`user_pass` char(32) DEFAULT NULL,
				`user_salt` char(32) NOT NULL,
				PRIMARY KEY (user_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("user","user_manager");
		$this->module_manager_model->add_module_names_from_lang_file("user");
		

		//we have a pseudo module ;)
		$this->module_manager_model->add_module("change_pass","");
		$this->module_manager_model->add_module_names_from_lang_file("change_pass");

		//we have another pseudo module ;)
		$this->module_manager_model->add_module("logout","");
		$this->module_manager_model->add_module_names_from_lang_file("logout");

		return;
	}

	public function uninstall()
	{
		return;
	}

	//returns info of the logged user 
	public function &get_user_info()
	{
		if($this->user_info_initialized)
			return $this->user_info;

		$this->user_info_initialized=TRUE;
		$email=$this->get_logged_user_email();
		if($email)
		{
			$this->user_info=new User($email);
		}

		return $this->user_info;
	}

	public function get_all_users_info()
	{
		$this->db->select(array("user_id","user_email"));
		$this->db->from("user");
		$this->db->order_by("user_id","DESC");
		$results=$this->db->get();

		return $results->result_array();
	}

	public function add_if_not_exist($email,$pass)
	{
		$result=$this->db->get_where("user",array("user_email"=>$email));
		if($result->num_rows() == 1)
		{
			$this->logger->info("[add_user] [email:".$email."] [repeated] [result:0]");
			return TRUE;
		}

		$salt=random_string("alnum",32);
		$this->db->insert("user",array(
			"user_email"=>$email,
			"user_pass"=>$this->getPass($pass,$salt),
			"user_salt"=>$salt
		));

		$this->logger->info("[add_user] [email:".$email."] [done] [result:1]");
		return FALSE;
	}

	public function delete_user($user_id,$user_email)
	{
		$this->db->where(array("user_id"=>$user_id,"user_email"=>$user_email));
		$this->db->delete("user");

		$this->load->model("access_manager_model");
		$this->access_manager_model->unset_user_access($user_id);

		$this->logger->info("[delete_user] [email:".$user_email."] [done] [result:1]");
		return;
	}

	public function change_logged_user_pass($prev_pass,$new_pass)
	{	
		$email=$this->get_logged_user_email();

		$result=$this->db->get_where("user",array("user_email"=>$email));
		if($result->num_rows() != 1)
		{
			$this->logger->info("[change_logged_user_pass] [email:".$email."] [incorrect_email] [result:0]");
			return FALSE;
		}

		$row=$result->row();				
		if($row->user_pass !== $this->getPass($prev_pass, $row->user_salt))
		{
			$this->logger->info("[change_logged_user_pass] [email:".$email."] [incorrect_pass] [result:0]");
			return FALSE;
		}

		return $this->change_user_pass($email,$new_pass);
	}

	public function change_user_pass($user_email,$new_pass)
	{
		$salt=random_string("alnum",32);
		$this->db->set("user_pass", $this->getPass($new_pass,$salt));
		$this->db->set("user_salt", $salt);
		$this->db->where("user_email",$user_email);
		$this->db->limit(1);
		$this->db->update('user');

		if(!$this->db->affected_rows())
		{
			$this->logger->info("[change_user_pass] [email:".$user_email."] [incorrect_email] [result:0]");
			return FALSE;
		}

		$this->logger->info("[change_user_pass] [email:".$user_email."] [done] [result:1]");
		return TRUE;		
	}

	public function login($email,$pass)
	{
		$result=$this->db->get_where("user",array("user_email"=>$email));
		if($result->num_rows() != 1)
		{
			$this->logger->info("[login_user] [email:".$email."] [incorrect_email] [result:0]");
			return false;
		}

		$row=$result->row();		
		
		if($row->user_pass === $this->getPass($pass, $row->user_salt))
		{
			$this->set_user_logged_in($email);
			$this->logger->info("[login_user] [email:".$email."] [done] [result:1]");
			return true;
		}

		$this->logger->info("[login_user] [email:".$email."] [incorrect_pass] [result:0]");
		return false;
	}

	public function login_openid($email,$openid_server)
	{
		$result=$this->db->get_where("user",array("user_email"=>$email));
		if($result->num_rows() != 1)
		{
			$this->logger->info("[login_user] [openid:".$openid_server."] [email:".$email."] [incorrect_email] [result:0]");
			return false;
		}

		$row=$result->row();		
		
		$this->set_user_logged_in($email);

		$this->logger->info("[login_user] [openid:".$openid_server."] [email:".$email."] [done] [result:1]");
		
		return true;
	}

	public function set_password($email,$pass="")
	{
		if(!$pass)
			$pass=random_string("alnum",7);
		$salt=random_string("alnum",32);

		$this->db->set("user_pass", $this->getPass($pass,$salt));
		$this->db->set("user_salt", $salt);
		$this->db->where("user_email",$email);
		$this->db->limit(1);
		$this->db->update('user');

		if(!$this->db->affected_rows())
		{
			$this->logger->info("[new_pass] [email:".$email."] [incorrect_email] [result:0]");
			return FALSE;
		}
			
		$this->logger->info("[new_pass] [email:".$email."] [done] [result:1]");
		return $pass;
	}

	private function set_user_logged_in($email)
	{
		$this->session->set_userdata(SESSION_VARS_PREFIX."user_logged_in","true");
		$this->session->set_userdata(SESSION_VARS_PREFIX."user_email",$email);
		$this->session->set_userdata(SESSION_VARS_PREFIX."user_last_visit",time());

		return;
	}

	public function set_user_logged_out()
	{
		$this->logger->info("[user_logged_out] [done]");

		$this->session->unset_userdata(SESSION_VARS_PREFIX."user_logged_in");
		$this->session->unset_userdata(SESSION_VARS_PREFIX."user_email");
		$this->session->unset_userdata(SESSION_VARS_PREFIX."user_last_visit");

		return;
	}

	private function get_logged_user_email()
	{
		if(!$this->has_user_logged_in())
			return FALSE;

		return $this->session->userdata(SESSION_VARS_PREFIX."user_email");
	}

	public function has_user_logged_in()
	{

		if($this->session->userdata(SESSION_VARS_PREFIX."user_logged_in") !== 'true')
			return FALSE;

		if(time()-$this->session->userdata(SESSION_VARS_PREFIX."user_last_visit") < USER_SESSION_EXPIRATION)
		{
			$this->session->set_userdata(SESSION_VARS_PREFIX."user_last_visit",time());
			return TRUE;
		}
			
		$this->set_user_logged_out();

		return FALSE;
	}

	private function getPass($pass,$salt)
	{
		return md5(md5($pass).$salt);
	}

	public function get_dashbord_info()
	{
		$CI=& get_instance();
		$lang=$CI->language->get();
		$CI->lang->load('admin_user',$lang);		
		
		$data=array();
		$data['users']=$this->get_all_users_info();
		$data['total_text']=$CI->lang->line("total");
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("user_dashboard"),$data,TRUE);
		
		return $ret;		
	}
}
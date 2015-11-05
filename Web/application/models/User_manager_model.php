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
			$this->log_manager_model->info("USER_ADD",array(
			"user_email"=>$email
			,"result"=>FALSE
			));
			return TRUE;
		}

		$salt=random_string("alnum",32);
		$this->db->insert("user",array(
			"user_email"=>$email,
			"user_pass"=>$this->getPass($pass,$salt),
			"user_salt"=>$salt
		));

		$user_id=$this->db->insert_id();

		$this->log_manager_model->info("USER_ADD",array(
			"user_email"=>$email
			,"user_id"=>$user_id
			,"result"=>TRUE
		));

		return FALSE;
	}

	public function delete_user($user_id,$user_email)
	{
		$this->db->where(array("user_id"=>$user_id,"user_email"=>$user_email));
		$this->db->delete("user");

		$this->load->model("access_manager_model");
		$this->access_manager_model->unset_user_access($user_id);

		
		$this->log_manager_model->info("USER_DELETE",array(
			"user_id"=>$user_id
			,"user_email"=>$user_email		
		));

		return;
	}

	public function change_logged_user_pass($prev_pass,$new_pass)
	{	
		$email=$this->get_logged_user_email();

		$result=$this->db->get_where("user",array("user_email"=>$email));
		if($result->num_rows() != 1)
		{
			$this->log_manager_model->info("USER_CHANGE_PASS",array(
				"user_email"=>$email
				,"desc"=>"incorrect_email"
				,"result"=>FALSE
			));

			return FALSE;
		}

		$row=$result->row();				
		if($row->user_pass !== $this->getPass($prev_pass, $row->user_salt))
		{
			$this->log_manager_model->info("USER_CHANGE_PASS",array(
				"user_email"=>$email
				,"desc"=>"incorrect_pass"
				,"result"=>FALSE
			));

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
			$this->log_manager_model->info("USER_CHANGE_PASS",array(
				"user_email"=>$user_email
				,"desc"=>"incorrect_email"
				,"result"=>FALSE
			));
			return FALSE;
		}

		$this->log_manager_model->info("USER_CHANGE_PASS",array(
				"user_email"=>$user_email
				,"result"=>TRUE
		));
		return TRUE;		
	}

	public function login($email,$pass)
	{
		$result=$this->db->get_where("user",array("user_email"=>$email));
		if($result->num_rows() != 1)
		{
			$this->log_manager_model->info("USER_LOGIN",array(
				"user_email"=>$email
				,"desc"=>"incorrect_email"
				,"result"=>FALSE
			));
			return FALSE;
		}

		$row=$result->row();		
		
		if($row->user_pass === $this->getPass($pass, $row->user_salt))
		{
			$this->set_user_logged_in($email);

			$this->log_manager_model->info("USER_LOGIN",array(
				"user_email"=>$email
				,"result"=>TRUE
			));

			return TRUE;
		}

		$this->log_manager_model->info("USER_LOGIN",array(
				"user_email"=>$email
				,"desc"=>"incorrect_pass"
				,"result"=>FALSE
			));

		return TRUE;
	}

	public function login_openid($email,$openid_server)
	{
		$result=$this->db->get_where("user",array("user_email"=>$email));
		if($result->num_rows() != 1)
		{
			$this->log_manager_model->info("USER_LOGIN",array(
				"user_email"=>$email
				,"openid"=>$openid_server
				,"desc"=>"incorrect_email"
				,"result"=>FALSE
			));

			return false;
		}

		$row=$result->row();		
		
		$this->set_user_logged_in($email);

		$this->log_manager_model->info("USER_LOGIN",array(
			"user_email"=>$email
			,"openid"=>$openid_server
			,"result"=>TRUE
		));
		
		return true;
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
		$this->log_manager_model->info("USER_LOGOUT",array(
			"user_email"=>$this->session->userdata(SESSION_VARS_PREFIX."user_email")
		));

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
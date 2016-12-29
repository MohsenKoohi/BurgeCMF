<?php
class User_manager_model extends CI_Model
{
	private $user_info=NULL;
	private $user_info_initialized=FALSE;
	private $user_props_can_be_written=array("user_email","user_name","user_code","user_pass","user_group_id");
	private $user_props_can_be_read=array("user_id","user_email","user_name","user_code","user_group_id");
	private $user_props_can_be_modified_directly=array("user_name","user_code","user_group_id");

	private $ug_props_can_be_written=array("ug_name");
	private $ug_props_can_be_read=array("ug_id","ug_name");
	private $ug_props_can_be_modified_directly=array("ug_name");


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
				`user_id` INT AUTO_INCREMENT NOT NULL
				,`user_email` CHAR(100) NOT NULL UNIQUE
				,`user_name` CHAR(100)
				,`user_code` CHAR(20)
				,`user_group_id` INT DEFAULT 0
				,`user_pass` CHAR(32) DEFAULT NULL
				,`user_salt` CHAR(32) NOT NULL
				,PRIMARY KEY (user_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$user_group_table=$this->db->dbprefix('user_group'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $user_group_table (
				`ug_id` INT AUTO_INCREMENT NOT NULL
				,`ug_name` CHAR(100) NOT NULL 
				,PRIMARY KEY (ug_id)	
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

	public function get_users($filter=array())
	{
		$this->db
			->select("user_id, user_name, user_code")
			->from("user");

		if(isset($filter['name']))
			$this->db->where("user_name LIKE '%".str_replace(' ', '%', $filter['name'])."%'");

		if(isset($filter['start']) && isset($filter['length']))
			$this->db->limit((int)$filter['length'],(int)$filter['start']);

		if(isset($filter['order_by']))
			$this->db->order_by($filter['order_by']);
		else
			$this->db->order_by("user_id DESC");

		return $this->db->get()->result_array();
		
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
		$this->db->select($this->user_props_can_be_read);
		$this->db->from("user");
		$this->db->order_by("user_id","ASC");
		$results=$this->db->get();

		return $results->result_array();
	}

	public function get_user($user_id)
	{
		$this->db->select($this->user_props_can_be_read);
		$this->db->from("user");
		$this->db->where("user_id",(int)$user_id);
		$results=$this->db->get();

		return $results->row_array();
	}

	public function get_user_group($ug_id)
	{
		$this->db->select($this->ug_props_can_be_read);
		$this->db->from("user_group");
		$this->db->where("ug_id",(int)$ug_id);
		$results=$this->db->get();

		return $results->row_array();
	}

	public function get_all_user_groups()
	{
		return $this->db
			->select($this->ug_props_can_be_read)
			->from("user_group")
			->order_by("ug_id","ASC")
			->get()
			->result_array();
	}

	public function add_if_not_exist($user_props)
	{
		$props=select_allowed_elements($user_props,$this->user_props_can_be_written);

		$email=$props['user_email'];

		$result=$this->db->get_where("user",array("user_email"=>$email));
		if($result->num_rows() == 1)
		{
			$this->log_manager_model->info("USER_ADD",array(
			"user_email"=>$email
			,"result"=>0
			));
			return FALSE;
		}

		$salt=random_string("alnum",32);
		$props['user_salt']=$salt;
		$props['user_pass']=$this->getPass($props['user_pass'],$salt);
		$this->db->insert("user",$props);

		$user_id=$this->db->insert_id();
		$props['user_id']=$user_id;
		$props['result']=1;
		unset($props['user_pass'],$props['user_salt']);

		$this->log_manager_model->info("USER_ADD",$props);

		return $user_id;
	}

	public function add_user_group($ug_props)
	{
		$props=select_allowed_elements($ug_props,$this->ug_props_can_be_written);

		$this->db->insert("user_group",$props);

		$user_group_id=$this->db->insert_id();
		$props['new_user_group_id']=$user_group_id;
		$this->log_manager_model->info("USER_GROUP_ADD",$props);

		return $user_group_id;
	}

	public function delete_user($user_id)
	{
		//there is a note here
		//when you delete a user, if he has been logged into the system befor his deletion
		//he can continue to modify system.
		//yes, it could be, but now we have an access manager,
		//who checks all accesses and after deletion,
		//even previous pages can't post new info to the system.

		$this->db->where(array("user_id"=>$user_id));
		$this->db->delete("user");

		$this->load->model("access_manager_model");
		$this->access_manager_model->unset_all_modules(-$user_id);
		
		$this->log_manager_model->info("USER_DELETE",array(
			"user_id"=>$user_id
		));

		return;
	}

	public function delete_user_group($ug_id)
	{
		$this->db->where(array("ug_id"=>$ug_id));
		$this->db->delete("user_group");

		$this->db->set("user_group_id",0);
		$this->db->where("user_group_id",$ug_id);
		$this->db->update('user');

		$this->load->model("access_manager_model");
		$this->access_manager_model->unset_all_modules($ug_id);

		$this->log_manager_model->info("USER_GROUP_DELETE",array(
			"ug_id"=>$ug_id
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
				,"result"=>0
			));

			return FALSE;
		}

		$row=$result->row();				
		if($row->user_pass !== $this->getPass($prev_pass, $row->user_salt))
		{
			$this->log_manager_model->info("USER_CHANGE_PASS",array(
				"user_email"=>$email
				,"desc"=>"incorrect_pass"
				,"result"=>0
			));

			return FALSE;
		}

		$this->log_manager_model->info("USER_CHANGE_PASS",array(
			"user_email"=>$email
			,"desc"=>"changed"
			,"result"=>1
		));

		return $this->change_user_pass($email,$new_pass);
	}

	public function change_user_props($user_id,$user_props)
	{
		$props=select_allowed_elements($user_props,$this->user_props_can_be_modified_directly);
		
		$this->db->set($props);
		$this->db->where("user_id",$user_id);
		$this->db->limit(1);
		$this->db->update('user');

		$props['user_id']=$user_id;
		
		$this->log_manager_model->info("USER_CHANGE_PROPS",$props);

		return TRUE;		
	}

	public function change_user_group_props($ug_id,$ug_props)
	{
		$props=select_allowed_elements($ug_props,$this->ug_props_can_be_modified_directly);
		
		$this->db->set($props);
		$this->db->where("ug_id",$ug_id);
		$this->db->limit(1);
		$this->db->update('user_group');

		$props['ug_id']=$ug_id;
		
		$this->log_manager_model->info("USER_GROUP_CHANGE_PROPS",$props);

		return TRUE;		
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
				,"result"=>0
			));
			return FALSE;
		}

		$this->log_manager_model->info("USER_CHANGE_PASS",array(
				"user_email"=>$user_email
				,"result"=>1
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
				,"result"=>0
			));
			return FALSE;
		}

		$row=$result->row();		
		
		if($row->user_pass === $this->getPass($pass, $row->user_salt))
		{
			$this->set_user_logged_in($email);

			$this->log_manager_model->info("USER_LOGIN",array(
				"user_email"=>$email
				,"result"=>1
			));

			return TRUE;
		}

		$this->log_manager_model->info("USER_LOGIN",array(
				"user_email"=>$email
				,"desc"=>"incorrect_pass"
				,"result"=>0
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
				,"result"=>0
			));

			return FALSE;
		}

		$row=$result->row();		
		
		$this->set_user_logged_in($email);

		$this->log_manager_model->info("USER_LOGIN",array(
			"user_email"=>$email
			,"openid"=>$openid_server
			,"result"=>1
		));
		
		return TRUE;
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

	public function get_dashboard_info()
	{
		$CI=& get_instance();
		$lang=$CI->language->get();
		$CI->lang->load('ae_user',$lang);		
		
		$data=array();
		$data['users']=$this->get_all_users_info();
		$data['total_text']=$CI->lang->line("total");
		$data['user_groups']=$this->get_all_user_groups();
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("user_dashboard"),$data,TRUE);
		
		return $ret;		
	}
}
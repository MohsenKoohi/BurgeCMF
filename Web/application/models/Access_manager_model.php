<?php
class Access_manager_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		return;
	}

	public function install()
	{
		$access_table=$this->db->dbprefix('access'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $access_table (
				`user_id` int NOT NULL,
				`module_id` char(50) NOT NULL,
				PRIMARY KEY (user_id , module_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("access","access_manager");
		$this->module_manager_model->add_module_names_from_lang_file("access");
		
		return;
	}

	public function uninstall()
	{
		return;
	}

	//this method adds access to an array of modules for a user
	public function set_allowed_modules_for_user($user_id,$modules)
	{
		$this->unset_user_access($user_id);

		if(!$modules || !sizeof($modules))
			return;

		$batch=array();
		foreach ($modules as $module)
			$batch[]=array(
				"user_id"=>$user_id
				,"module_id"=>$module
				);

		$this->db->insert_batch("access",$batch);

		$this->logger->info("[add_user_access] [user_id:".$user_id."] [modules:".implode(",", $modules)."] [result:1]");

		return TRUE;
	}

	public function unset_user_access($user_id)
	{
		$this->db->delete("access",array("user_id"=>$user_id));

		$this->logger->info("[delete_user_access] [user_id:".$user_id."] [modules:all] [result:1]");

		return;
	}

	public function set_allowed_users_for_module($module_id,$users)
	{
		$this->unset_module_access($module_id);

		if(!$users || !sizeof($users))
			return;

		$batch=array();
		foreach ($users as $user_id)
			$batch[]=array(
				"user_id"=>$user_id
				,"module_id"=>$module_id
				);

		$this->db->insert_batch("access",$batch);

		$this->logger->info("[add_module_access] [module_id:".$module_id."] [users:".implode(",", $users)."] [result:1]");

		return TRUE;
	}

	public function unset_module_access($module_id)
	{
		$this->db->delete("access",array("module_id"=>$module_id));

		$this->logger->info("[delete_module_access] [module_id:".$module_id."] [users:all] [result:1]");

		return;
	}

	//checks if a user has access to a module
	public function check_access($module,&$user)
	{
		$report="[check_access] [to:$module]";
		$result=FALSE;

		if($user)
		{
			$report.=" [logged_in:yes]";
			
			//check access to module
			$query_result=$this->db->get_where("access",array("user_id"=>$user->get_id(),"module_id"=>$module));
			if($query_result->num_rows() == 1)
			{
				$report.=" [email:".$user->get_email()."]";
				$report.=" [has_access:yes]";				
				$result=TRUE;
			}
			else
				$report.=" [has_access:no]";
		}
		else
			$report.=" [logged_in:no]";

		$report.=" [result:".(int)$result."]";

		$this->logger->info($report);
		
		return $result;
	}

	//returns an array of all modules a user has access to
	public function get_user_modules($user_id)
	{
		$ret=array();
		if(!$user_id)
			return $ret;

		$result=$this->db->get_where("access",array("user_id"=>$user_id));
		foreach($result->result_array() as $row)
			$ret[]=$row["module_id"];

		return $ret;
	}

}
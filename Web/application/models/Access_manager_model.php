<?php
class Access_Manager_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		return;
	}

	//this method adds access to an array of modules for a user
	public function set_allowed_modules_for_user($modules,&$user)
	{

		$this->db->delete("access",array("user_id",$user->get_id()));

		$batch=array();
		foreach ($modules as $module)
			$batch[]=array(
				"user_id"=>$user->get_id()
				,"module_id"=>$module
				);

		$this->db->insert_batch("access",$batch);

		$this->logger->info("[add_access] [user_email:".$user->get_email()."] [modules:".implode(",", $modules)."] [result:1]");

		return TRUE;
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
	public function get_user_modules(&$user)
	{
		$ret=array();
		if(!$user)
			return $ret;

		$result=$this->db->get_where("access",array("user_id"=>$user->get_id()));
		foreach($result->result_array() as $row)
			$ret[]=$row["module_id"];

		return $ret;
	}

}
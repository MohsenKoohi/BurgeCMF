<?php
class Access_manager_model extends CI_Model
{
	private $access_table_name="access";

	public function __construct()
	{
		parent::__construct();
		
		return;
	}

	public function install()
	{
		//we use negative numbers indicating ids of users
		//and positive numbers indicating ids of groups
		//so we don't need to add a type column 

		$access_table=$this->db->dbprefix($this->access_table_name); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $access_table (
				`user_group_id` int NOT NULL,
				`module_id` char(50) NOT NULL,
				PRIMARY KEY (user_group_id , module_id)	
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

	//checks if a user has access to a module
	public function check_access($module,&$user)
	{
		$log_context=array("module"=>$module);
		$result=FALSE;

		if("login" === $module)
		{
			$result=TRUE;
			$log_context['has_access']=TRUE;
		}
		else
			if($user)
			{
				$user_id=$user->get_id();
				$group_id=$user->get_group_id();

				$log_context['user_id']=$user_id;
				
				//check access to module
				$query_result=$this->db
					->from($this->access_table_name)
					->where("module_id",$module)
					->where("( ( user_group_id = -$user_id ) || ( user_group_id = $group_id ) )")
					->group_by("module_id")
					->get();

				if($query_result->num_rows() == 1)
				{
					//$log_context['user_email']=$user->get_email();
					$log_context['user_name']=$user->get_name();
					$log_context['user_code']=$user->get_code();
					$log_context['user_group_id']=$group_id;

					$log_context['has_access']=TRUE;

					$result=TRUE;
				}
				else
					$log_context['has_access']=FALSE;
			}
			else
				$log_context['user_id']=-1;

		$log_context['result']=(int)$result;

		$this->log_manager_model->info("ACCESS_CHECK",$log_context);
		
		return $result;
	}

	//returns an array of all modules a user has access to
	public function get_user_modules($user)
	{
		$ret=array();
		if(!$user)
			return $ret;

		$user_id=$user->get_id();
		$group_id=$user->get_group_id();

		$result=$this->db
			->select("GROUP_CONCAT(module_id) as module_ids")
			->from($this->access_table_name)
			->where("( ( user_group_id = -$user_id ) || ( user_group_id = $group_id ) )")
			->get()
			->row_array();

		$module_ids=explode(",", $result['module_ids']);

		return $module_ids;		
	}

	public function get_modules($access_id)
	{
		if(!$access_id)
			return array();

		$result=$this->db
			->select("GROUP_CONCAT(module_id) as module_ids")
			->from($this->access_table_name)
			->where(array("user_group_id"=>$access_id))
			->get()
			->row_array();

		return explode(",", $result['module_ids']);
	}

	public function unset_all_modules($access_id)
	{
		if(!$access_id)
			return;

		$this->db
			->where("user_group_id",$access_id)
			->delete($this->access_table_name);

		$this->log_manager_model->info("ACCESS_UNSET",array(
			"access_id"=>$access_id
		));
	}

	//this method adds access to an array of modules for a user
	public function set_modules($access_id,$module_ids)
	{
		if(!$access_id)
			return;

		$this->unset_all_modules($access_id);

		if(!$module_ids || !sizeof($module_ids))
			return;

		$batch=array();
		foreach ($module_ids as $module_id)
			$batch[]=array(
				"user_group_id"	=> $access_id
				,"module_id"		=> $module_id
				);

		$this->db->insert_batch($this->access_table_name,$batch);

		$this->log_manager_model->info("ACCESS_SET",array(
			"module_ids"=>implode(" , ", $module_ids),
			"access_id"=>$access_id
		));

		return TRUE;
	}
	

}
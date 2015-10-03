<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User
{
	private $CI;
	private $user_email=NULL;
	private $user_id=-1;

	//you can asssing more fields here
	//and then load from db

	public function __construct($email=null)
	{
		$this->CI=& get_instance();

		if(is_string($email))
		{
			$this->load_data($email);
		}
	}

	private function load_data($email)
	{
		$this->CI->db->select();
		$this->CI->db->from("user");
		$this->CI->db->where("user_email",$email);
		$result=$this->CI->db->get();
		
		$info=$result->row();
		if(!$info)
			return FALSE;

		$this->user_id=$info->user_id;
		$this->user_email=$info->user_email;

		return TRUE;
	}

	public function get_email()
	{
		return $this->user_email;
	}

	public function get_id()
	{
		return $this->user_id;
	}

}
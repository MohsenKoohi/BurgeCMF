<?php
//It's a log manager which insists on events 
//system logs are gathered by log system of CI
//we added it since we need an event log manager with ability to retreive 
//different types of reports from data

class Log_manager_model extends CI_Model
{
	private $logger;

	private $log_dir=LOG_DIR;
	private $log_prefix=LOGS_PREFIX;
	private $date_function=DATE_FUNCTION;
	private $log_extension="txt";

	private $today_day;
	private $today_month;
	private $today_year;

	private $event_types=array(
		"UNKOWN"						=>0
		,"NEW_VISIT"				=>1

		,"CMF_INSTALL" 			=>21
		,"CMF_UNINSTALL" 			=>22

		,"MODULE_INSTALL"			=>51
		,"MODULE_UNINSTALL"		=>52
		,"MODULE_ADD"				=>53
		,"MODULE_ADD_NAME"		=>54
		,"MODULE_RESORT"			=>55
		,"MODULE_CRON_UPDATE"	=>56
		,"MODULE_CRON_EXECUTE"	=>57

		,"ACCESS_SET"				=>81
		,"ACCESS_UNSET"			=>82
		,"ACCESS_CHECK"			=>84

		,"USER_ADD"					=>101
		,"USER_DELETE"				=>102
		,"USER_CHANGE_PASS"		=>103
		,"USER_LOGIN"				=>104
		,"USER_LOGOUT"				=>105
		,"USER_CHANGE_PROPS"		=>106
		,"USER_GROUP_ADD"				=>107
		,"USER_GROUP_DELETE"			=>108
		,"USER_GROUP_CHANGE_PROPS"	=>109
		

		,"CONSTANT_SET"			=>131
		,"CONSTANT_UNSET"			=>132		

		,"POST_ADD"					=>161
		,"POST_CHANGE"				=>162
		,"POST_DELETE"				=>163
		,"POST_COMMENT_ADD"		=>164
		,"POST_COMMENT_CHANGE"	=>165
		,"POST_COMMENT_DELETE"	=>166
		
		,"FILE_DIR_CREATE"		=>201
		,"FILE_DIR_DELETE"		=>202
		,"FILE_DIR_COPY"			=>203
		,"FILE_DIR_MOVE"			=>204
		,"FILE_DIR_RENAME"		=>205
		,"FILE_DIR_DOWNLOAD"		=>206		
		,"FILE_FILE_UPLOAD"		=>207
		,"FILE_FILE_DELETE"		=>208
		,"FILE_FILE_COPY"			=>209
		,"FILE_FILE_MOVE"			=>210
		,"FILE_FILE_RENAME"		=>211

		,"CATEGORY_CREATE"		=>241
		,"CATEGORY_DELETE"		=>242
		,"CATEGORY_CHANGE"		=>243	
		,"CATEGORY_RESORT"		=>244
		,"CATEGORY_HASH_UPDATE"	=>245

		,"CONTACT_US_ADD"				=>271
		,"CONTACT_US_REPLY"			=>272
		,"CONTACT_US_DELETE"			=>273
		,"CONTACT_US_NEW_MESSAGE"	=>274

	);

	// 2d logging constants
	private $log_2d_major_ids=array();
	private $log_2d_file_extension = 'txt';

	public function __construct()
	{
		parent::__construct();

		$date_function=$this->date_function;

		list($y,$m,$d)=explode("-", $date_function("Y-m-d"));
		$this->today_year=$y;
		$this->today_month=$m;
		$this->today_day=$d;

		$this->initialize_logger();
      
      $this->log_this_visit();

      return;
   }

   private function initialize_logger()
   {
   	$this->load->library("core/logger");
   	
   	//we don't want people to access logger directly
      //they should call $CI->log_manager_model
      //one of our criticisms to CI is this work of 
      //1)auto initialization of classes when it loads them
      //2)and then assigning it to the $CI instance as a property
   	$CI=&get_instance();
   	unset($CI->logger);
		
   	$options=array(
			'extension'      => 'txt',
			'dateFormat'     => 'Y/m/d H:i:s',
			'filename'       => $this->get_today_log_file_name(),
			'prefix'         => '',
			'logFormat'      => FALSE,
			'appendContext'  => TRUE,
    	);
   	
		$this->logger=new Logger($this->log_dir,LogLevel::DEBUG,$options);

		return;
   }

   public function get_event_types()
   {
   	return $this->event_types;
   }

   public function get_today_logs()
   {
   	return $this->get_logs(array(
   		"year"=>$this->today_year
   		,"month"=>$this->today_month
   		,"day"=>$this->today_day
   	));
   }

   public function get_logs($filters)
   {
   	$file_path=$this->get_log_file_path($filters['year'],$filters['month'],$filters['day']);
   	$result=array();

   	if(isset($filters['visitor_id']))
   		$visitor_id_pattern="/.*".str_replace(" ", ".*", $filters['visitor_id']).".*/i";
   	
   	if(file_exists($file_path))
   	{
   		$content=file_get_contents($file_path);
   		$content=str_replace(PHP_EOL, ",", trim($content));
   		$res=json_decode("[".$content."]");

   		$count=0;
   		
   		for($i=sizeof($res)-1;$i>=0;$i--)
   		{
   			if(isset($filters['event']))
   				if($res[$i]->event_name != $filters['event'])
   					continue;

   			if(isset($visitor_id_pattern))
   				if(!preg_match($visitor_id_pattern, $res[$i]->visitor_id))
   					continue;

   			$result[$count++]=$res[$i];
   		}

   		$result['total']=$count;
   	}
   	else
   		$result['total']=0;

   	return $result;
   }

   private function get_log_file_path($y,$m,$d)
   {
   	$filename=$this->get_log_file_name($y,$m,$d);
   	return $this->log_dir."/".$filename;
   }

   private function get_today_log_file_name()
   {
   	return $this->get_log_file_name($this->today_year,$this->today_month,$this->today_day);
   }

   private function get_log_file_name($y,$m,$d)
   {
   	return $this->log_prefix.$y."-".$m."-".$d.".".$this->log_extension;
   }

   private function log_this_visit()
   {
   	$CI=&get_instance();
     	$event_props=array(
      	"url"		=> $CI->uri->uri_string
      );
      if(isset($_SERVER['HTTP_USER_AGENT']))
      	$event_props["user_agent"]=$_SERVER['HTTP_USER_AGENT'];
      if(isset($_SERVER['HTTP_REFERER']))
      	$event_props["referer"]=$_SERVER['HTTP_REFERER'];

		$this->info("NEW_VISIT",$event_props);

		return;
	}

	public function install()
	{
		$this->load->helper("init");
		$cdp_message="";

		$dirs=array(LOG_DIR);
		foreach($this->log_2d_major_ids as $mid)
			$dirs[]=LOG_2D_PARENT_DIR."/".$mid;

		foreach($dirs as $dir)
		{
			$result=check_directory_permission($dir, $cdp_message);
			echo $cdp_message;
			if(!$result)
			{
				echo "<h2>Please check the errors, and try again.";
				exit;
			}
		}

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("log","log_manager");
		$this->module_manager_model->add_module_names_from_lang_file("log");
		
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
		$CI->lang->load('ae_log',$lang);		
		
		$data=array();
		$data['logs']=$this->get_today_logs();

		$total=$data['logs']['total'];
		if($total)
		{			
			$data['logs']['start']=1;
			$data['logs']['end']=min(9,$data['logs']['total']);
		}
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("log_dashboard"),$data,TRUE);
		
		return $ret;		
	}

	public function emergency($message, array $context = array())
	{
		$this->log(LogLevel::EMERGENCY, $message, $context);
	}
    
	public function alert($message, array $context = array())
	{
		$this->log(LogLevel::ALERT, $message, $context);
	}

	public function critical($message, array $context = array())
	{
		$this->log(LogLevel::CRITICAL, $message, $context);
	}

	public function error($message, array $context = array())
	{
		$this->log(LogLevel::ERROR, $message, $context);
	}

	public function warning($message, array $context = array())
	{
		$this->log(LogLevel::WARNING, $message, $context);
	}

	public function notice($message, array $context = array())
	{
		$this->log(LogLevel::NOTICE, $message, $context);
	}

	public function info($message, array $context = array())
	{
		$this->log(LogLevel::INFO, $message, $context);
	}

	public function debug($message, array $context = array())
	{
		$this->log(LogLevel::DEBUG, $message, $context);
	}

	//level: level of log
	//message: its message, but we ask people to say the type of event
	//context: other parameters
	private function log($level,$message,$context)
	{
		$event_type=$message;
		if(!isset($this->event_types[$event_type]))
			$event_type="UNKOWN";

		$context["ip"]=$this->input->ip_address();      	
		$context["event_name"]=$message;
		$context["event_id"]=$this->event_types[$event_type];

		$CI=&get_instance();
		if(isset($CI->in_admin_env) && $CI->in_admin_env && $CI->user)
		{
			$context["active_user_id"]=$CI->user->get_id();
			//$context["active_user_code"]=$CI->user->get_id();
			$context["active_user_name"]=$CI->user->get_name();
			//$context["active_user_email"]=$CI->user->get_email();
		}

		$this->logger->log($level,$event_type,$context);
	}

	public function get_visitor_id()
	{
		return $this->logger->getVisitorId();
	}

	////////////////////////////////////////////////////////////////////////////
	// 2d logging

	//$major_id : the name of module/agent requires log for its events, for example, customer/journal/..
	//$minor_id : the id of that module/agent member, for example: customer_id/journal_id/...
	//$log_type_index : type of log as an integer
	//$desc: a index=>val array to be logged

	//The module which uses log_2d is responsible to manage type_indexes
	//It's an integer which is used to store and retrieve log
	public function log_2d($major_id, $minor_id, $log_type_index, $desc)
	{
		if(!in_array($major_id, $this->log_2d_major_ids))
			return FALSE;

		$CI=&get_instance();
		if(isset($CI->in_admin_env) && $CI->in_admin_env)
		{
			$desc["active_user_id"]=$CI->user->get_id();
			$desc["active_user_code"]=$CI->user->get_code();
			$desc["active_user_name"]=$CI->user->get_name();
		}	

		$desc['visitor_ip']=$this->input->ip_address();	
		$desc['visitor_id']=$this->log_manager_model->get_visitor_id();
		$ua=$this->input->user_agent();
		if($ua)
      	$desc["visitor_user_agent"]=$ua;
		
		$log_path=$this->get_log_2d_path($major_id, $minor_id, $log_type_index);

		$string='{"log_type_index":"'.$log_type_index.'"';

		foreach($desc as $index=>$val)
		{
			$index=trim($index);
			$index=preg_replace('/[\\\'\"]+/', "", $index);
			$index=preg_replace('/\s+/', "_", $index);

			$val=trim($val);
			$val=preg_replace('/[\\\'\"]+/', "", $val);
			$val=preg_replace('/\s+/', " ", $val);
			
			$string.=',"'.$index.'":"'.$val.'"';
		}
		$string.="}";

		file_put_contents($log_path, $string);
		
		return;
	}

	//it returns an array with two index, 'results' which specifies  logs
	//and total which indicates the total number of logs 
	public function get_logs_2d($major_id, $minor_id,$filter=array())
	{
		if(!in_array($major_id, $this->log_2d_major_ids))
			return FALSE;

		$dir=$this->get_log_2d_minor_id_directory($major_id, $minor_id);
		$file_names=scandir($dir, SCANDIR_SORT_DESCENDING);

		$logs=array();
		$count=-1;
		$start=0;
		if(isset($filter['start']))
			$start=(int)$filter['start'];
		$length=sizeof($file_names);
		if(isset($filter['length']))
			$length=(int)$filter['length'];

		foreach($file_names as $fn)
		{
			if(!preg_match("/^log-/", $fn))
				continue;

			$tmp=explode(".", $fn);
			list($date_time,$log_type)=explode("#",$tmp[0]);
			list($date,$time)=explode(",",$date_time);
			$time=str_replace("-", ":", $time);
			$date=str_replace(array("log-","-"), array("","/"), $date);
			$date_time=$date." ".$time;

			//now we have timestamp and log_type of this log
			//and we can filter logs we don't want here;
			if(isset($filter['log_types']))
				if(!in_array($log_type ,$filter['log_types']))
					continue;

			$count++;
			if($count < $start)
				continue;
			if($count >= ($start+$length))
				continue;

			//reading log
			$log=json_decode(file_get_contents($dir."/".$fn), TRUE);
			if($log)
				$log['timestamp']=$date_time;
			$logs[]=$log;
		}

		$total=$count+1;

		return  array(
			"results"	=> $logs
			,"total"		=> $total
		);
	}

	private function get_log_2d_path($major_id, $minor_id, $log_type_index)
	{
		$dir=$this->get_log_2d_minor_id_directory($major_id, $minor_id);
		
		$dtf=DATE_FUNCTION;

		$s=0;
		do	
		{
			$dt=$dtf("Y-m-d,H-i-s",time()+$s);	
			
			$ext=$this->log_2d_file_extension;
			$tp=sprintf("%04d",$log_type_index);

			$log_path=$dir."/log-".$dt."#".$tp.".".$ext;
			$s+=1;
		}while(file_exists($log_path));
		
		return $log_path;
	}

	private function get_log_2d_minor_id_directory($major_id, $minor_id)
	{
		$dir=LOG_2D_PARENT_DIR."/".$major_id;

		$dir1=(int)($minor_id/1000);
		$dir2=$minor_id % 1000;
		
		$path1=$dir."/".$dir1;
		if(!file_exists($path1))
			mkdir($path1,0777);

		$path2=$dir."/".$dir1."/".$dir2;
		if(!file_exists($path2))
			mkdir($path2,0777);

		return $path2;
	}

}
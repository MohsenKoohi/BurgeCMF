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
      	"ip"			=> $_SERVER['REMOTE_ADDR']
      	,"url"		=> $CI->uri->uri_string
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
}
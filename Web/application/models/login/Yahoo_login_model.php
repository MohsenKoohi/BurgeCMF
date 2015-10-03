<?php
class Yahoo_login_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		if (!function_exists('curl_exec'))
		{
			log_message('Error','Couldnt initialize curl extension for Yahoo_login_model');
			exit(0);
		}

	}

	var $openid_url_identity;
	var $URLs = array();
	var $error = array();
	var $fields = array();

	function SetOpenIDServer($a)
	{
		$this->URLs['openid_server'] = $a;
	}

	function SetIdentity($a)
	{ 	
		if((strpos($a, 'http://') === false) && (strpos($a, 'https://') === false))
		{
	 		$a = 'http://'.$a;
	 	}
		$this->openid_url_identity = $a;
	}
	
	function GetError()
	{
		$e = $this->error;
		return array('code'=>$e[0],'description'=>$e[1]);
	}

	function ErrorStore($code, $desc = null){
		$errs['OPENID_NOSERVERSFOUND'] = 'Cannot find OpenID Server TAG on Identity page.';
		if ($desc == null){
			$desc = $errs[$code];
		}
	   	$this->error = array($code,$desc);
	}

	function IsError(){
		if (count($this->error) > 0){
			return true;
		}else{
			return false;
		}
	}
	
	function splitResponse($response) {
		$r = array();
		$response = explode("\n", $response);
		foreach($response as $line) {
			$line = trim($line);
			if ($line != "") {
				list($key, $value) = explode(":", $line, 2);
				$r[trim($key)] = trim($value);
			}
		}
	 	return $r;
	}
	
	function array2url($arr){ // converts associated array to URL Query String
		if (!is_array($arr)){
			return false;
		}
		$query="";
		foreach($arr as $key => $value){
			$query .= $key . "=" . $value . "&";
		}
		return $query;
	}
	
	function CURL_Request($url, $method="GET", $params = "") { // Remember, SSL MUST BE SUPPORTED
		if (is_array($params)) $params = $this->array2url($params);

		$curl = curl_init($url . ($method == "GET" && $params != "" ? "?" . $params : ""));
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPGET, ($method == "GET"));
		curl_setopt($curl, CURLOPT_POST, ($method == "POST"));
		
		if ($method == "POST")
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		
		if (curl_errno($curl) == 0)
		{
			$response;
		}
		else
		{
			$this->ErrorStore('OPENID_CURL', curl_error($curl));
		}

		return $response;
	}
	
	function HTML2OpenIDServer($content) {
		$get = array();
	
		preg_match_all('/<link[^>]*rel="openid2.provider"[^>]*href="([^"]+)"[^>]*\/?>/i', $content, $matches1);
		preg_match_all('/<link[^>]*href="([^"]+)"[^>]*rel="openid.server"[^>]*\/?>/i', $content, $matches2);
		$servers = array_merge($matches1[1], $matches2[1]);
		
		preg_match_all('/<link[^>]*rel="openid.delegate"[^>]*href="([^"]+)"[^>]*\/?>/i', $content, $matches1);
		
		preg_match_all('/<link[^>]*href="([^"]+)"[^>]*rel="openid.delegate"[^>]*\/?>/i', $content, $matches2);
		
		$delegates = array_merge($matches1[1], $matches2[1]);
		
		$ret = array($servers, $delegates);
		return $ret;
	}
	
	function GetOpenIDServer(){
		$response = $this->CURL_Request($this->openid_url_identity);
		list($servers, $delegates) = $this->HTML2OpenIDServer($response);
		if (count($servers) == 0){
			$this->ErrorStore('OPENID_NOSERVERSFOUND');
			return false;
		}
		if ((sizeof($delegates)>0) && ($delegates[0] != "")){
			$this->openid_url_identity = $delegates[0];
		}
		$this->SetOpenIDServer($servers[0]);

		return $servers[0];
	}
	
	function ValidateWithServer()
	{
		$params = array(
			'openid.signed' => ($_GET['openid_signed']),
			'openid.sig' => ($_GET['openid_sig'])
		);

		$arr=explode(",",urldecode($params['openid.signed']));
		for ($i=0; $i<count($arr); $i++)
		{
			$s=str_replace(".", "_", $arr[$i]);
			$c=$_GET['openid_' .$s];
			$params['openid.' . $arr[$i]] = urlencode($c);
		}
		$params['openid.mode'] = "check_authentication";
		$openid_server = $this->GetOpenIDServer();
		
		if ($openid_server == false)
			return false;
		
		$response = $this->CURL_Request($openid_server,'GET',$params);
		$data = $this->splitResponse($response);
		if ($data['is_valid'] == "true")
			return true;
		else
			return false;
	}

	public function RedirectToYahooServer($realm_link,$return_link)
	{
		$yahoo_oid_server_link=$this->GetOpenIDServer();
		if(!$yahoo_oid_server_link)
			return false;

		$url=$this->get_yahoo_oid_link($yahoo_oid_server_link,array("email"),$realm_link,$return_link);
		//$this->redirect_to($url,"js");

		return $url;
	}

	private function redirect_to($link,$type="")
	{
		if (($type==="js") || headers_sent())
		{ 
			echo '<script language="JavaScript" type="text/javascript">window.location=\'';
			echo $link;
			echo '\';</script>';
		}
		else
		{	
			header('Location: ' . $link);
		}

		return;
	}

	private function get_yahoo_oid_link($server,$required_params=array(),$realm_link="",$return_link="")
	{
		$params=array();
		$params['openid.ax.mode']="fetch_request";
		$params['openid.claimed_id']=urlencode("http://specs.openid.net/auth/2.0/identifier_select");
		$params['openid.identity']=urlencode("http://specs.openid.net/auth/2.0/identifier_select&");
		$params['openid.mode'] ='checkid_setup';
		$params['openid.ns']=urlencode("http://specs.openid.net/auth/2.0");
		$params['openid.ns.ax']=urlencode("http://openid.net/srv/ax/1.0");
		$params['openid.ns.max_auth_age']="0";
		$params['openid.ns.pape']=urlencode("http://specs.openid.net/extensions/pape/1.0");
		$params['openid.realm']=urlencode($realm_link);
		$params['openid.return_to']=urlencode($return_link);
		$params['openid.lang.pref']="en";

		if ($required_params)
		{
			$params['openid.ax.required']=implode('%2C',$required_params);
			$params['openid.ax.type.country']=urlencode("http://axschema.org/contact/country/home");
			$params['openid.ax.type.email']=urlencode("http://axschema.org/contact/email");
			$params['openid.ax.type.firstname']=urlencode("http://axschema.org/namePerson/first");
			$params['openid.ax.type.language']=urlencode("http://axschema.org/pref/language");
			$params['openid.ax.type.lastname']=urlencode("http://axschema.org/namePerson/last");
		}

		$query="";
		foreach($params as $key => $value)
			$query.= $key."=".$value."&";
		
		return $server."?".$query;
	}

}



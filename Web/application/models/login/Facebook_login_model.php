<?php
class Facebook_login_model extends CI_Model
{
	var $client_id = '';
	var $client_secret = '';
 	var $redirect_uri;

	public function __construct()
	{
		parent::__construct();
		$this->redirect_uri = get_link("facebook_login_page");
	}


	public function getAuthenticationUrl()
	{
		return 'https://www.facebook.com/dialog/oauth?client_id='.$this->client_id."&redirect_uri=".$this->redirect_uri;
	}

	public function verifyUserAndGetEmail()
	{
		$auth_url='https://graph.facebook.com/v2.3/oauth/access_token?';
		$auth_url.='client_id='.$this->client_id;
		$auth_url.='&redirect_uri='.$this->redirect_uri;
		$auth_url.='&client_secret='.$this->client_secret;
		$auth_url.='&code='.$_GET['code'];
	
		$content=@file_get_contents($auth_url);
		if(!$content)
			return false;
		$json_content=json_decode($content);
		if(!isset($json_content->access_token))
			return false;
		$access_token=$json_content->access_token;
		
		$prop_url='https://graph.facebook.com/me?fields=email';
		$prop_url.='&access_token='.$access_token;
		
		$content=@file_get_contents($prop_url);
		if(!$content)
			return false;
		$json_prop=json_decode($content);
		if(!isset($json_prop->email))
			return false;
		$email=urldecode($json_prop->email);
		
		return  $email;
	}	

}
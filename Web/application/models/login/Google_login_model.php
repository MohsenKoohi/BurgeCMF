<?php
class Google_login_model extends CI_Model
{
	var $client_id = '';
	var $client_secret = '';
 	var $redirect_uri;

	public function __construct()
	{
		parent::__construct();

		require_once "google/autoload.php";

		$this->redirect_uri = get_link("google_login_page");
	}


	public function getAuthenticationUrl()
	{
		$client = new Google_Client();
		$client->setClientId($this->client_id);
		$client->setClientSecret($this->client_secret);
		$client->setRedirectUri($this->redirect_uri);
		$client->addScope("openid email");
		$authUrl = $client->createAuthUrl();
		
		return $authUrl;
	}

	public function verifyUserAndGetEmail()
	{
		$client = new Google_Client();
		$client->setClientId($this->client_id);
		$client->setClientSecret($this->client_secret);
		$client->setRedirectUri($this->redirect_uri);
		try
		{
			 $client->authenticate($_GET['code']);
			 $google_oauth =new Google_Service_Oauth2($client);
			 $email = $google_oauth->userinfo->get()->email;
			 
			 return $email;
		}
		catch(Exception $e)
		{
		 	//echo $e->getMessage();
		}

		return false;
	}
	

}



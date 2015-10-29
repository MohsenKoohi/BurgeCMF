<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
$route['default_controller'] = "home";

if(ENVIRONMENT==='development')
{
	$route[ADMIN_URL_FOLDER.'/install']		="setup/install";
	$route[ADMIN_URL_FOLDER.'/uninstall']	="setup/uninstall";
}

$route[ADMIN_URL_FOLDER]						="dashboard";
$route[ADMIN_URL_FOLDER."/dashboard"]		="dashboard";
$route[ADMIN_URL_FOLDER."/user"]				="users";
$route[ADMIN_URL_FOLDER."/login"]			="login";
$route[ADMIN_URL_FOLDER."/change_pass"]	="change_pass";
$route[ADMIN_URL_FOLDER."/logout"]			="logout";
$route[ADMIN_URL_FOLDER."/access"]			="access";
$route[ADMIN_URL_FOLDER."/module"]			="module";
$route[ADMIN_URL_FOLDER."/hit_counter"]	="hit_counter";

$route[ADMIN_URL_FOLDER."/post"]			="post";


$route[urlencode('ثبت')]="register";
$route['register/request_pay/(.*)']="register/request_pay/$1";
$route['register/pay_result/(.*)/(.*)']="register/pay_result/$1/$2";

$route['(((:any)/)*:any)']="home";



/* End of file routes.php */
/* Location: ./application/config/routes.php */
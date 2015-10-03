<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'][]=
	array(
        'class'    => 'Language',
        'function' => 'init',
        'filename' => 'Language.php',
        'filepath' => 'libraries/core',
        'params'   => array()
	);

$hook['post_controller_constructor'][]=
	array(
        'class'    => 'Language',
        'function' => 'init_CI_instance',
        'filename' => 'Language.php',
        'filepath' => 'libraries/core',
        'params'   => array()
	);


/* End of file hooks.php */
/* Location: ./application/config/hooks.php */
<?php

class File_manager_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();

      return;
   }


	public function install()
	{
		$this->load->helper("init");
		$cdp_message="";
		$result=check_directory_permission(UPLOAD_DIR, $cdp_message);
		echo $cdp_message;
		if(!$result)
		{
			echo "<h2>Please check the errors, and try again.";
			exit;
		}

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("file","file_manager");
		$this->module_manager_model->add_module_names_from_lang_file("file");
		
		return;
	}

	public function uninstall()
	{
		return;
	}

	public function get_conf()
	{
		return '
			{
				"FILES_ROOT":          "upload",
				"RETURN_URL_PREFIX":   "",
				"SESSION_PATH_KEY":    "",
				"THUMBS_VIEW_WIDTH":   "140",
				"THUMBS_VIEW_HEIGHT":  "120",
				"PREVIEW_THUMB_WIDTH": "100",
				"PREVIEW_THUMB_HEIGHT":"100",
				"MAX_IMAGE_WIDTH":     "5000",
				"MAX_IMAGE_HEIGHT":    "5000",
				"INTEGRATION":         "custom",
				"DIRLIST":             "dirtree",
				"CREATEDIR":           "createdir",
				"DELETEDIR":           "deletedir",
				"MOVEDIR":             "movedir",
				"COPYDIR":             "copydir",
				"RENAMEDIR":           "renamedir",
				"FILESLIST":           "fileslist",
				"UPLOAD":              "upload",
				"DOWNLOAD":            "download",
				"DOWNLOADDIR":         "downloaddir",
				"DELETEFILE":          "deletefile",
				"MOVEFILE":            "movefile",
				"COPYFILE":            "copyfile",
				"RENAMEFILE":          "renamefile",
				"GENERATETHUMB":       "thumb",
				"DEFAULTVIEW":         "thumb",
				"FORBIDDEN_UPLOADS":   "zip js jsp jsb asp aspx mhtml mht xhtml xht php phtml php3 php4 php5 phps shtml jhtml pl sh py cgi exe application gadget hta cpl msc jar vb jse ws wsf wsc wsh ps1 ps2 psc1 psc2 msh msh1 msh2 inf reg scf msp scr dll msi vbs bat com pif cmd vxd cpl htpasswd htaccess",
				"ALLOWED_UPLOADS":     "",
				"FILEPERMISSIONS":     "0644",
				"DIRPERMISSIONS":      "0755",
				"LANG":                "auto",
				"DATEFORMAT":          "dd/MM/yyyy HH:mm",
				"OPEN_LAST_DIR":       "yes"
			}
		';
	}

	public function initialize_roxy()
	{

		//from system.inc.php
		define('BASE_PATH', HOME_DIR);
		date_default_timezone_set('UTC');
		mb_internal_encoding("UTF-8");
		mb_regex_encoding(mb_internal_encoding());

		//loading php/functions.inc.php
		$this->load->helper("roxy");

		//end of php/functions.inc.php which should be done here ;)
		$tmp = json_decode($this->get_conf(), true);
		if($tmp){
		  foreach ($tmp as $k=>$v)
		    define($k, $v);
		}
		else
		  die('Error parsing configuration');
		$FilesRoot = fixPath(getFilesPath());
		if(!is_dir($FilesRoot))
		  @mkdir($FilesRoot, octdec(DIRPERMISSIONS));

		return;
	}
}
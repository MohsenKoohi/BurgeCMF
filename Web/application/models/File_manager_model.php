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
				"FILES_ROOT":          "Uploads",
				"RETURN_URL_PREFIX":   "",
				"SESSION_PATH_KEY":    "",
				"THUMBS_VIEW_WIDTH":   "140",
				"THUMBS_VIEW_HEIGHT":  "120",
				"PREVIEW_THUMB_WIDTH": "100",
				"PREVIEW_THUMB_HEIGHT":"100",
				"MAX_IMAGE_WIDTH":     "1000",
				"MAX_IMAGE_HEIGHT":    "1000",
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
				"DEFAULTVIEW":         "list",
				"FORBIDDEN_UPLOADS":   "zip js jsp jsb mhtml mht xhtml xht php phtml php3 php4 php5 phps shtml jhtml pl sh py cgi exe application gadget hta cpl msc jar vb jse ws wsf wsc wsh ps1 ps2 psc1 psc2 msh msh1 msh2 inf reg scf msp scr dll msi vbs bat com pif cmd vxd cpl htpasswd htaccess",
				"ALLOWED_UPLOADS":     "",
				"FILEPERMISSIONS":     "0644",
				"DIRPERMISSIONS":      "0755",
				"LANG":                "auto",
				"DATEFORMAT":          "dd/MM/yyyy HH:mm",
				"OPEN_LAST_DIR":       "yes"
			}
		';
	}
}
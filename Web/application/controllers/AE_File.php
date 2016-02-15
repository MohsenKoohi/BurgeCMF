<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_File extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model("file_manager_model");
	}

	public function index()
	{
		
		$this->data['message']=get_message();

		$this->lang->load('ae_file',$this->selected_lang);
	
		$this->data['lang_pages']=get_lang_pages(get_link("admin_file",TRUE));
		$this->data['header_title']=$this->lang->line("files");
		
		$this->send_admin_output("file");
	
		return;		
	}

	public function inline()
	{
		$this->load->library('parser');
		$this->data["parent_function"]=$this->input->get("parent_function");
		
		$this->parser->parse($this->get_admin_view_file("file_inline"),$this->data);
		
	}

	public function conf()
	{
		$this->output->set_content_type('application/json');
    	$this->output->set_output($this->file_manager_model->get_conf());

		return;
	}

	private function lang($lang)
	{

		echo file_get_contents(SCRIPTS_DIR."/roxy/lang/".$lang.".json");

		return;
	}

	private function image($link1,$link2="",$link3="")
	{
		$path=SCRIPTS_DIR."/roxy/images/".$link1;
		if($link2)
			$path.="/".$link2;
		if($link3)
			$path.="/".$link3;
		
		$extension=pathinfo($path, PATHINFO_EXTENSION);
		$this->output->set_content_type($extension);
		$this->output->set_output(file_get_contents($path));

		return;
	}

	public function action($action)
	{
		$this->file_manager_model->initialize_roxy();

		switch($action)
		{
			case 'dirtree':
				return $this->dirtree();

			case 'fileslist':
				return $this->fileslist();

			case 'thumb':
				return $this->thumb();

			case 'createdir':
				return $this->createdir();

			case 'deletedir':
				return $this->deletedir();

			case 'copydir':
				return $this->copydir();

			case 'movedir':
				return $this->movedir();

			case 'renamedir':
				return $this->renamedir();

			case 'deletefile':
				return $this->deletefile();

			case 'downloaddir':
				return $this->downloaddir();

			case 'copyfile':
				return $this->copyfile();

			case 'movefile':
				return $this->movefile();

			case 'renamefile':
				return $this->renamefile();

			case 'download':
				return $this->download();

			case 'upload':
				return $this->upload();
		}
	}

	private function log($type,$from_path,$to_path)
	{
		$path=array();
		if($from_path)
			$path['from_path']=$from_path;
		if($to_path)
			$path['to_path']=$to_path;

		$this->log_manager_model->info($type,$path);

		return;
	}

	private function downloaddir()
	{
		$path = trim($_GET['d']);
		
		$this->log("FILE_DIR_DOWNLOAD",$path,NULL);

		verifyPath($path);
		$path = fixPath($path);

		if(!class_exists('ZipArchive')){
		  echo '<script>alert("Cannot create zip archive - ZipArchive class is missing. Check your PHP version and configuration");</script>';
		}
		else{
		  try{
		    $filename = basename($path);
		    $zipFile = $filename.'.zip';
		    $zipPath = sys_get_temp_dir().'/'.$zipFile;
		    RoxyFile::ZipDir($path, $zipPath);

		    header('Content-Disposition: attachment; filename="'.$zipFile.'"');
		    header('Content-Type: application/force-download');
		    readfile($zipPath);
		    function deleteTmp($zipPath){
		      @unlink($zipPath);
		    }
		    register_shutdown_function('deleteTmp', $zipPath);
		  }
		  catch(Exception $ex){
		    echo '<script>alert("'.  addslashes(t('E_CreateArchive')).'");</script>';
		  }
		}

		return;
	}
	
	private function upload()
	{
		$isAjax = (isset($_POST['method']) && $_POST['method'] == 'ajax');
		$path = trim(empty($_POST['d'])?getFilesPath():$_POST['d']);
		verifyPath($path);
		$fileNames=array();
		$res = '';
		if(is_dir(fixPath($path))){
		  if(!empty($_FILES['files']) && is_array($_FILES['files']['tmp_name'])){
		    $errors = $errorsExt = array();
		    foreach($_FILES['files']['tmp_name'] as $k=>$v){
		      $filename = $_FILES['files']['name'][$k];
		      $filename = RoxyFile::MakeUniqueFilename(fixPath($path), $filename);
		      $filePath = fixPath($path).'/'.$filename;
		      $isUploaded = true;
		      if(!RoxyFile::CanUploadFile($filename)){
		        $errorsExt[] = $filename;
		        $isUploaded = false;
		      }
		      elseif(!move_uploaded_file($v, $filePath)){
		         $errors[] = $filename; 
		         $isUploaded = false;
		      }
		      if($isUploaded)
		      	$fileNames[]=$path."/".$filename;
		      if(is_file($filePath)){
		         @chmod ($filePath, octdec(FILEPERMISSIONS));
		      }
		      if($isUploaded && RoxyFile::IsImage($filename) && (intval(MAX_IMAGE_WIDTH) > 0 || intval(MAX_IMAGE_HEIGHT) > 0)){
		        RoxyImage::Resize($filePath, $filePath, intval(MAX_IMAGE_WIDTH), intval(MAX_IMAGE_HEIGHT));
		      }
		    }
		    if($errors && $errorsExt)
		      $res = getSuccessRes(t('E_UploadNotAll').' '.t('E_FileExtensionForbidden'));
		    elseif($errorsExt)
		      $res = getSuccessRes(t('E_FileExtensionForbidden'));
		    elseif($errors)
		      $res = getSuccessRes(t('E_UploadNotAll'));
		    else
		      $res = getSuccessRes();
		  }
		  else
		    $res = getErrorRes(t('E_UploadNoFiles'));
		}
		else
		  $res = getErrorRes(t('E_UploadInvalidPath'));

		if($isAjax){
		  if($errors || $errorsExt)
		    $res = getErrorRes(t('E_UploadNotAll'));
		  echo $res;
		}
		else{
		  echo '
			<script>
			parent.fileUploaded('.$res.');
			</script>';
		}

		$this->log("FILE_FILE_UPLOAD",NULL,implode("<br>", $fileNames));
		bprint_r($fileNames);

		return;
	}

	private function download()
	{
		$path = trim($_GET['f']);
		verifyPath($path);

		if(is_file(fixPath($path))){
		  $file = urldecode(basename($path));
		  header('Content-Disposition: attachment; filename="'.$file.'"');
		  header('Content-Type: application/force-download');
		  readfile(fixPath($path));
		}

		return;	
	}

	private function deletefile()
	{
		$path = trim($_POST['f']);

		$this->log("FILE_FILE_DELETE",$path,NULL);

		verifyPath($path);

		if(is_file(fixPath($path))){
		if(unlink(fixPath($path)))
		  echo getSuccessRes();
		else
		  echo getErrorRes(t('E_DeletеFile').' '.basename($path));
		}
		else
			echo getErrorRes(t('E_DeleteFileInvalidPath'));

		return;		
	}

	private function movefile()
	{	
		$path = trim(empty($_POST['f'])?'':$_POST['f']);
		$newPath = trim(empty($_POST['n'])?'':$_POST['n']);

		if(!$newPath)
		  $newPath = getFilesPath();

		$this->log("FILE_FILE_DLETE",$path,$newPath);

		verifyPath($path);
		verifyPath($newPath);

		if(is_file(fixPath($path))){
		  if(file_exists(fixPath($newPath)))
		    echo getErrorRes(t('E_MoveFileAlreadyExists').' '.basename($newPath));
		  elseif(rename(fixPath($path), fixPath($newPath)))
		    echo getSuccessRes();
		  else
		    echo getErrorRes(t('E_MoveFile').' '.basename($path));
		}
		else
		  echo getErrorRes(t('E_MoveFileInvalisPath'));

		return;
	}

	private function renamefile()
	{		
		$path = trim(empty($_POST['f'])?'':$_POST['f']);
		$name = trim(empty($_POST['n'])?'':$_POST['n']);

		$this->log("FILE_FILE_RENAME",$path,$name);

		verifyPath($path);

		if(is_file(fixPath($path))){
		  if(!RoxyFile::CanUploadFile($name))
		    echo getErrorRes(t('E_FileExtensionForbidden').' ".'.RoxyFile::GetExtension($name).'"');
		  elseif(rename(fixPath($path), dirname(fixPath($path)).'/'.$name))
		    echo getSuccessRes();
		  else
		    echo getErrorRes(t('E_RenameFile').' '.basename($path));
		}
		else
		  echo getErrorRes(t('E_RenameFileInvalidPath'));

		return;
	}

	private function copyfile()
	{
		$path = trim(empty($_POST['f'])?'':$_POST['f']);
		$newPath = trim(empty($_POST['n'])?'':$_POST['n']);
		if(!$newPath)
		  $newPath = getFilesPath();

		$this->log("FILE_FILE_COPY",$path,$newPath);

		verifyPath($path);
		verifyPath($newPath);

		if(is_file(fixPath($path))){
		  $newPath = $newPath.'/'.RoxyFile::MakeUniqueFilename(fixPath($newPath), basename($path));
		  if(copy(fixPath($path), fixPath($newPath)))
		    echo getSuccessRes();
		  else
		    echo getErrorRes(t('E_CopyFile'));
		}
		else
		  echo getErrorRes(t('E_CopyFileInvalisPath'));
			
		return;
	}

	private function renamedir()
	{
		$path = trim(empty($_POST['d'])? '': $_POST['d']);
		$name = trim(empty($_POST['n'])? '': $_POST['n']);

		$this->log("FILE_DIR_RENAME",$path,$name);


		verifyPath($path);

		if(is_dir(fixPath($path))){
		  if(fixPath($path.'/') == fixPath(getFilesPath().'/'))
		    echo getErrorRes(t('E_CannotRenameRoot'));
		  elseif(rename(fixPath($path), dirname(fixPath($path)).'/'.$name))
		    echo getSuccessRes();
		  else
		    echo getErrorRes(t('E_RenameDir').' '.basename($path));
		}
		else
		  echo getErrorRes(t('E_RenameDirInvalidPath'));

		return;
	}

	private function copydir()
	{
		$path = trim(empty($_POST['d'])?'':$_POST['d']);
		$newPath = trim(empty($_POST['n'])?'':$_POST['n']);

		$this->log("FILE_DIR_COPY",$path,$newPath);

		verifyPath($path);
		verifyPath($newPath);

		if(is_dir(fixPath($path))){
		  copyDir(fixPath($path.'/'), fixPath($newPath.'/'.basename($path)));
		  echo getSuccessRes();
		}
		else
		  echo getErrorRes(t('E_CopyDirInvalidPath'));

		return;
	}

	private function movedir()
	{				
		$path = trim(empty($_GET['d'])?'':$_GET['d']);
		$newPath = trim(empty($_GET['n'])?'':$_GET['n']);

		$this->log("FILE_DIR_MOVE",$path,$newPath);

		verifyPath($path);
		verifyPath($newPath);

		if(is_dir(fixPath($path))){
		  if(mb_strpos($newPath, $path) === 0)
		    echo getErrorRes(t('E_CannotMoveDirToChild'));
		  elseif(file_exists(fixPath($newPath).'/'.basename($path)))
		    echo getErrorRes(t('E_DirAlreadyExists'));
		  elseif(rename(fixPath($path), fixPath($newPath).'/'.basename($path)))
		    echo getSuccessRes();
		  else
		    echo getErrorRes(t('E_MoveDir').' '.basename($path));
		}
		else
		  echo getErrorRes(t('E_MoveDirInvalisPath'));

		return;
	}

	private function deletedir()
	{
		$path = trim(empty($_GET['d'])?'':$_GET['d']);

		$this->log("FILE_DIR_DELETE",$path,NULL);

		verifyPath($path);

		if(is_dir(fixPath($path))){
		  if(fixPath($path.'/') == fixPath(getFilesPath().'/'))
		    echo getErrorRes(t('E_CannotDeleteRoot'));
		  elseif(count(glob(fixPath($path)."/*")))
		    echo getErrorRes(t('E_DeleteNonEmpty'));
		  elseif(rmdir(fixPath($path)))
		    echo getSuccessRes();
		  else
		    echo getErrorRes(t('E_CannotDeleteDir').' '.basename($path));
		}
		else
		  echo getErrorRes(t('E_DeleteDirInvalidPath').' '.$path);

		return;
	}

	private function createdir()
	{
		$path = trim(empty($_POST['d'])?'':$_POST['d']);
		$name = trim(empty($_POST['n'])?'':$_POST['n']);

		$this->log("FILE_DIR_CREATE",$path,$name);

		verifyPath($path);

		if(is_dir(fixPath($path))){
		  if(mkdir(fixPath($path).'/'.$name, octdec(DIRPERMISSIONS)))
		    echo getSuccessRes();
		  else
		    echo getErrorRes(t('E_CreateDirFailed').' '.basename($path));
		}
		else
		  echo  getErrorRes(t('E_CreateDirInvalidPath'));

		return;
	}

	private function thumb()
	{
		header("Pragma: cache");
		header("Cache-Control: max-age=3600");

		$path = urldecode(empty($_GET['f'])?'':$_GET['f']);
		verifyPath($path);

		@chmod(fixPath(dirname($path)), octdec(DIRPERMISSIONS));
		@chmod(fixPath($path), octdec(FILEPERMISSIONS));

		$w = intval(empty($_GET['width'])?'100':$_GET['width']);
		$h = intval(empty($_GET['height'])?'0':$_GET['height']);

		header('Content-type: '.RoxyFile::GetMIMEType(basename($path)));
		if($w && $h)
		  RoxyImage::CropCenter(fixPath($path), null, $w, $h);
		else 
		  RoxyImage::Resize(fixPath($path), null, $w, $h);
	}

	private function fileslist()
	{		
		$path = (empty($_POST['d'])? getFilesPath(): $_POST['d']);
		$type = (empty($_POST['type'])?'':strtolower($_POST['type']));
		if($type != 'image' && $type != 'flash')
		  $type = '';
		verifyPath($path);

		$files = listDirectory(fixPath($path), 0);
		natcasesort($files);
		$str = '';
		echo '[';
		foreach ($files as $f){
		  $fullPath = $path.'/'.$f;
		  if(!is_file(fixPath($fullPath)) || ($type == 'image' && !RoxyFile::IsImage($f)) || ($type == 'flash' && !RoxyFile::IsFlash($f)))
		    continue;
		  $size = filesize(fixPath($fullPath));
		  $time = filemtime(fixPath($fullPath));
		  $w = 0;
		  $h = 0;
		  if(RoxyFile::IsImage($f)){
		    $tmp = @getimagesize(fixPath($fullPath));
		    if($tmp){
		      $w = $tmp[0];
		      $h = $tmp[1];
		    }
		  }
		  $str .= '{"p":"'.mb_ereg_replace('"', '\\"', $fullPath).'","s":"'.$size.'","t":"'.$time.'","w":"'.$w.'","h":"'.$h.'"},';
		}
		$str = mb_substr($str, 0, -1);
		echo $str;
		echo ']';
	}

	private function dirtree()
	{	
		$type = (empty($_GET['type'])?'':strtolower($_GET['type']));
		if($type != 'image' && $type != 'flash')
		  $type = '';

		echo "[\n";
		$tmp = getFilesNumber(fixPath(getFilesPath()), $type);
		echo '{"p":"'.  mb_ereg_replace('"', '\\"', getFilesPath()).'","f":"'.$tmp['files'].'","d":"'.$tmp['dirs'].'"}';
		GetDirs(getFilesPath(), $type);
		echo "\n]";
	}

}
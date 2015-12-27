<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CE_Watermark extends Burge_CMF_Controller {
	protected $hit_level = -1;
	private $pw="WaterMarkMe";
	function __construct()
	{
		parent::__construct();

		return;
	}

	public function index()
	{
		if($this->input->get("pw")!==$this->pw)
			return redirect(get_link("home_url"));

		if(isset($_FILES['image']))
			return $this->watermark();

		echo form_open_multipart("watermark?pw=".$this->pw,array());
		echo "<input type='file' name='image'/>";
		echo "<input type='submit' />";

		echo "</form>";
	}


	private function watermark()
	{
		if(!isset($_FILES['image']))
			return;

		$file_name=$_FILES['image']['name'];
		$file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
		$temp_path=$_FILES['image']['tmp_name'];

		burge_cmf_watermark($temp_path);

		$extension=pathinfo($temp_path, PATHINFO_EXTENSION);
		$this->output->set_content_type($extension);
		$this->output->set_output(file_get_contents($temp_path));

		//move_uploaded_file($image_path, "images/watermark/new.jpg");
	}
}
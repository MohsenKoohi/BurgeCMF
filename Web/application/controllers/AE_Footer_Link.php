<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Footer_Link extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->lang->load('ae_footer_link',$this->selected_lang);
		$this->load->model("footer_link_manager_model");

	}

	public function index()
	{
		//$this->set_footer();

		if($this->input->post("post_type")==="set_links")
			return $this->set_links();

		$this->data['message']=get_message();
		$this->data['links']=$this->footer_link_manager_model->get_links();

		$this->data['raw_page_url']=get_link("admin_footer_link");
		$this->data['lang_pages']=get_lang_pages(get_link("admin_footer_link",TRUE));
		$this->data['header_title']=$this->lang->line("footer_link");

		$this->send_admin_output("footer_link");

		return;	 
	}

	private function set_footer()
	{
		foreach($this->language->get_languages() as $lang_id => $lang_name)
		{
			$footer_view_file=HOME_DIR."/application/views/".$lang_id."/customer/footer_tpl.php";
			if(!file_exists($footer_view_file))
				continue;

			$content=file_get_contents($footer_view_file);
			$links=$this->footer_link_manager_model->get_links($lang_id);
			$footer_part=$this->create_footer_part($links);
			$content=str_replace("{footer_template_place}", $footer_part, $content);

			$footer_view_file=HOME_DIR."/application/views/".$lang_id."/customer/footer.php";
			file_put_contents($footer_view_file, $content);
		}

		return;
	}

	private function create_footer_part($links)
	{
		$ret='<ul>';
		if($links)
			foreach($links[0]['children'] as $l)
			{
				if($l['link'])
					$ret.="<li><a href='".$l['link']."'>".$l['title']."</a>";
				else
					$ret.="<li>".$l['title'];

				if($l['children'])
				{
					$ret.="<ul>";
					foreach($l['children'] as $c)
						$ret.="<li><a href='".$c['link']."'>".$c['title']."</a></li>";
					$ret.="</ul>";
				}

				$ret.="</li>";
			}

		$ret.="</ul>";

		return $ret ;
	}

	private function set_links()
	{
		$ins=array();
		$links=$this->input->post("links");

		foreach($links as $id => $l)
			$ins[]=array(
				'fl_id'				=> $id
				,'fl_parent_id'	=> $l['parent_id']
				,'fl_title'			=> $l['title']
				,'fl_link'			=> $l['link']
			);

		$this->footer_link_manager_model->set_links($ins);

		set_message($this->lang->line("set_successfully"));

		$this->set_footer();

		redirect(get_link("admin_footer_link"));

		return;
	}
}
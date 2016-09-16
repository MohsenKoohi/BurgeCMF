<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Category extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model("category_manager_model");
		$this->lang->load('ae_category',$this->selected_lang);

	}

	public function index()
	{
		if($this->input->post("post_type")==="add_category")
			return $this->add_category();

		if($this->input->post("post_type")==="resort")
			return $this->resort();

		$this->data['message']=get_message();

		$this->data['categories']=$this->category_manager_model->get_all();
		$this->data['lang_pages']=get_lang_pages(get_link("admin_category",TRUE));
		$this->data['header_title']=$this->lang->line("categories");
		
		$this->send_admin_output("category");

		return;
	}

	public function organize()
	{
		$this->category_manager_model->organize();

		set_message($this->lang->line("categoies_organized_successfully"));

		return redirect(get_link("admin_category"));
	}

	private function resort()
	{
		$ids=$this->input->post("ids");
		$ids=explode(",",$ids);

		$this->category_manager_model->sort_categories($ids);

		set_message($this->lang->line("category_sorted_successfully"));

		return redirect(get_link("admin_category"));
	}

	private function add_category($parent_id=0)
	{
		$id=$this->category_manager_model->add($parent_id);

		set_message($this->lang->line("category_added_successfully"));

		return redirect(get_admin_category_details_link($id));
	}

	public function details($category_id)
	{
		if($this->input->post("post_type")==="edit_category")
			return $this->edit_category($category_id);

		if($this->input->post("post_type")==="delete_category")
			return $this->delete_category($category_id);

		if($this->input->post("post_type")==="add_sub_category")
			return $this->add_category($category_id);

		$this->data['message']=get_message();
		$this->data['categories']=$this->category_manager_model->get_hierarchy("radio",$this->selected_lang,array($category_id));
		
		$info=$this->category_manager_model->get_info((int)$category_id);

		foreach($info as &$row)
		{
			$info[$row['cd_lang_id']]=&$row;
			$row['lang']=$this->all_langs[$row['cd_lang_id']];
		}

		$this->data['info']=array();
		if($info)
			foreach($this->all_langs as $lang => $lang_name)
				$this->data['info'][$lang]=$info[$lang];

		$this->data['category_url_first_part']=trim(get_customer_category_details_link($category_id,$info[$this->selected_lang]['category_hash'],"",""),"/")."/";
		
		$this->data['category_id']=$category_id;
		$this->data['lang_pages']=get_lang_pages(get_admin_category_details_link($category_id,TRUE));
		if($info)
			$this->data['header_title']=$this->data['info'][$this->selected_lang]['cd_name'];
		else
			$this->data['header_title']=$this->lang->line("not_found");

		$this->send_admin_output("category_details");
	}

	private function edit_category($category_id)
	{
		$props=array();

		$props['descriptions']=array();

		$props['category_parent_id']=$this->input->post("category_parent_id");
		$props['category_show_in_list']=($this->input->post("category_show_in_list")==="on");

		foreach($this->language->get_languages() as $lang=>$name)
		{
			$category_content=$this->input->post($lang);
			$category_content['cd_description']=$_POST[$lang]['cd_description'];
			$category_content['cd_lang_id']=$lang;

			$props['descriptions'][]=$category_content;
		}

		$this->category_manager_model->set_props($category_id,$props);

		set_message($this->lang->line("edited_successfully"));

		return redirect(get_admin_category_details_link($category_id));
	}

	private function delete_category($category_id)
	{
		$this->category_manager_model->delete($category_id);

		set_message($this->lang->line("deleted_successfully"));

		return redirect(get_link("admin_category"));
	}
}
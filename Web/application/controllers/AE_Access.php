 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Access extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index($access_id)
	{
		$this->load->model(array(
			"access_manager_model"
			,"user_manager_model"
			,"module_manager_model"
		));

		$access_id=(int)$access_id;
		$this->data['access_id']=$access_id;

		$this->data['access_type']="";
		if($access_id>0)
		{
			$user_group_info=$this->user_manager_model->get_user_group($access_id);
			if(!$user_group_info)
				return redirect(get_link("admin_access"));

			$this->data['access_type']="user_group";	
		}
			
		if($access_id<0)
		{
			$user_info=$this->user_manager_model->get_user(-$access_id);
			if(!$user_info)
				return redirect(get_link("admin_access"));	

			$this->data['access_type']="user";
		}

		$this->lang->load('ae_access',$this->selected_lang);

		$this->data['message']=get_message();
		$this->data['users_info']=$this->user_manager_model->get_all_users_info();
		$this->data['user_groups_info']=$this->user_manager_model->get_all_user_groups();
		$this->data['modules_info']=$this->module_manager_model->get_all_modules_info($this->selected_lang);
		
		if($this->input->post())
		{
			$this->lang->load('error',$this->selected_lang);

			if($this->input->post('post_type') === "set_modules_access")
				return $this->set_modules_access($access_id);
		}
		
		$this->data['modules_have_access_to']=$this->access_manager_model->get_modules($access_id);
		
		$this->data['form_submit_link']=get_admin_access_details_link($access_id);
		$this->data['lang_pages']=get_lang_pages(get_admin_access_details_link($access_id,TRUE));
		$this->data['header_title']=$this->lang->line("access_levels");

		$this->send_admin_output("access");

		return;	 
	}

	private function set_modules_access($access_id)
	{
		if(!$access_id)
		{
			set_message($this->lang->line("select_user"));
			return redirect(get_link("admin_access"));
		}

		$module_ids=$this->input->post("module_ids");

		$this->access_manager_model->set_modules($access_id, $module_ids);

		set_message($this->lang->line("changed_successfully"));
		redirect(get_admin_access_details_link($access_id));

		return;
	}

}
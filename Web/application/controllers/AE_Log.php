<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Log extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		
		$this->lang->load('ae_log',$this->selected_lang);

		$this->set_logs();

		$this->data['current_date']=get_current_date();
		
		$this->data['event_types']=$this->log_manager_model->get_event_types();
		$this->data['lang_pages']=get_lang_pages(get_link("admin_log",TRUE));
		$this->data['header_title']=$this->lang->line("log");
		
		$this->send_admin_output("log");

		return;	 
	}

	private function set_logs()
	{
		$filters=array();

		$this->data['raw_page_url']=get_link("admin_log");
		
		$this->initialize_filters($filters);
				
		$logs=$this->log_manager_model->get_logs($filters);

		$this->data['logs']=&$logs;

		$total=$this->data['logs']['total'];
		if($total)
		{
			$per_page=30;
			$page=1;
			if($this->input->get("page"))
				$page=(int)$this->input->get("page");
						
			$start=($page-1)*$per_page;
			$logs['start']=$start;
			$logs['end']=$start+min($per_page,$logs['total']-$per_page*($page-1));

			$this->data['logs_current_page']=$page;
			$this->data['logs_total_pages']=ceil($total/$per_page);
			$this->data['logs_total']=$total;
			$this->data['logs_start']=$start+1;
			$this->data['logs_end']=$logs['end'];		
		}
		else
		{
			$this->data['logs_current_page']=0;
			$this->data['logs_total_pages']=0;
			$this->data['logs_total']=$total;
			$this->data['logs_start']=0;
			$this->data['logs_end']=0;
		}

		unset($filters['year'],$filters['month'],$filters['day']);
		$this->data['filter']=$filters;
	}

	private function initialize_filters(&$filter)
	{
		$date=get_current_date();
		if($this->input->get("date"))
		{
			$date=$this->input->get("date");
			$date=persian_normalize($date);
		}
		list($filter['year'],$filter['month'],$filter['day'])=explode("/", $date);
		$filter['date']=$date;

		if($this->input->get("event"))
			$filter['event']=$this->input->get("event");

		if($this->input->get("visitor_id"))
			$filter['visitor_id']=$this->input->get("visitor_id");

	}
}
<?php
class Footer_link_manager_model extends CI_Model
{
	private $footer_link_table_name="footer_link";
	
	public function __construct()
	{
		parent::__construct();

		return;
	}

	public function install()
	{
		$tbl=$this->db->dbprefix($this->footer_link_table_name); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $tbl (
				`fl_id` INT  NOT NULL AUTO_INCREMENT
				,`fl_lang_id` CHAR(2)
				,`fl_parent_id` INT
				,`fl_title` VARCHAR(511)
				,`fl_link` VARCHAR(2047)
				,PRIMARY KEY (fl_id)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("footer_link","footer_link_manager");
		$this->module_manager_model->add_module_names_from_lang_file("footer_link");
		
		return;
	}

	public function uninstall()
	{
		return;
	}
	
	public function get_links($lang_id=NULL)
	{
		$this->db
			->select("*")
			->from($this->footer_link_table_name);

		if($lang_id)
			$this->db->where("fl_lang_id", $lang_id);

		$result=$this->db
			->order_by("fl_id")
			->get()
			->result_array();

		$ret=array();
		foreach($result as $r)
		{
			$lang_id=$r['fl_lang_id'];
			if(!isset($ret[$lang_id]))
				$ret[$lang_id]=array();

			$parent_id=$r['fl_parent_id'];
			if(!isset($ret[$lang_id][$parent_id]))
				$ret[$lang_id][$parent_id]=array(
					'title'			=> ''
					,'link'			=> ''
					,'children'		=> array()
				);
			$parent_node=&$ret[$lang_id][$parent_id];

			$id=$r['fl_id'];
			$ret[$lang_id][$id]=array(
				'title'			=> $r['fl_title']
				,'link'			=> $r['fl_link']
				,'children'		=> array()
			);

			$parent_node['children'][$id]=&$ret[$lang_id][$id];
		}

		//bprint_r($ret);exit;

		return $ret;
	}

	public function set_links($ins)
	{
		$this->db
			->where("fl_id >=",0)
			->delete($this->footer_link_table_name);

		if($ins)
			$this->db->insert_batch($this->footer_link_table_name, $ins);
		
		return;
	}
}

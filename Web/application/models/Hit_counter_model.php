
<?php
class Hit_counter_model extends CI_Model
{
	private $home_del="#home";
	private $hit_counter_table_name="hit_counter";
	private $current_year;
	private $current_month;

	public function __construct()
	{
		parent::__construct();

		eval('$res= '.DATE_FUNCTION.'("Y m");');
		list($year,$month)=explode(' ', $res);
		$this->year=$year;
		$this->month=$month;
		
		return;
	}

	public function install()
	{
		$hit_counter_table=$this->db->dbprefix('hit_counter'); 
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS $hit_counter_table (
				`ht_url` varchar(1000) NOT NULL,
				`ht_url_md5` char(16) NOT NULL,
				`ht_year` char(4) NOT NULL,
				`ht_month` char(2) NOT NULL,
				`ht_count` bigint DEFAULT 1,
				PRIMARY KEY (ht_url_md5, ht_year, ht_month)	
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->load->model("module_manager_model");

		$this->module_manager_model->add_module("hit_counter","hit_counter");
		$this->module_manager_model->add_module_names_from_lang_file("hit_counter");
		
		return;
	}

	public function uninstall()
	{
	
		return;
	}

	//$hit_level specifies how many indexes of $parts has value.
	//0 means all indexes of $parts has value;
	//negative means none

	public function count($parts,$hit_level=0)
	{
		if($hit_level<0)
			return;

		if($hit_level)
		{
			$new_parts=array();
			for($i=1;$i<min($hit_level,1+sizeof($parts));$i++)
				$new_parts[]=$parts[$i-1];
			$parts=$new_parts;
		}
		
		if(!sizeof($parts))
			$parts[]="";

		if(!$hit_level || ($hit_level == 1))
		{
			if($parts[sizeof($parts)-1])
				$parts[]="";
			$parts[sizeof($parts)-1]=$this->home_del;
		}

		$all_parts="";
		$query_parts=array("/");
		foreach($parts as $part)
		{
			$all_parts.="/".urldecode($part);
			$query_parts[]=$all_parts;
		}
		
		$sql="INSERT INTO `".$this->db->dbprefix($this->hit_counter_table_name)."` (ht_url,ht_url_md5,ht_year,ht_month) VALUES"; 
		$i=0;
		foreach ($query_parts as $part)
			$sql.=(($i++==0)?"":",").
				" ( 
				 ".$this->db->escape($part)." 
				, ".$this->db->escape(md5($part))."
				, '".$this->year."'
				, '".$this->month."'
				)";
			
		$sql.='  ON DUPLICATE KEY UPDATE ht_count = ht_count + 1 ';
		$this->db->query($sql);
		
		return;	
	}

	public function get_all_counts()
	{
		$tbl="`".$this->db->dbprefix($this->hit_counter_table_name)."`";
		$year=$this->year;
		$month=$this->month;
		$del="/".$this->home_del;

		$sql=" 
			SELECT mc.ht_url AS url, mc.ht_count AS month_count, year_count, total_count FROM $tbl mc
			LEFT JOIN (SELECT *,SUM(ht_count) AS year_count FROM $tbl WHERE ht_year = $year GROUP BY ht_url_md5)  yc
				ON mc.ht_url_md5 = yc.ht_url_md5
			LEFT JOIN (SELECT *,SUM(ht_count) AS total_count FROM $tbl GROUP BY ht_url_md5)  tc
				ON mc.ht_url_md5 = tc.ht_url_md5
			WHERE 
				mc.ht_year = $year 
				AND mc.ht_month = $month
			GROUP BY (CONCAT(month_count,'*',year_count,'*',total_count,'*',REPLACE(mc.ht_url,'$del','')))
			ORDER BY url ASC, mc.ht_count DESC 
		";
		$result=$this->db->query($sql);
		
		return $result->result_array();
	}

	public function get_dashboard_info()
	{
		$CI=& get_instance();
		$lang=$CI->language->get();
		$CI->lang->load('ae_hit_counter',$lang);		
		
		$data=array();
		$data['month_text']=$CI->lang->line("monthly_visit");
		$data['year_text']=$CI->lang->line("yearly_visit");
		$data['total_text']=$CI->lang->line("total_visit");

		$counts=$this->get_all_counts();
		$data['total_count']=$counts[0]['total_count'];
		$data['year_count']=$counts[0]['year_count'];
		$data['month_count']=$counts[0]['month_count'];
		
		$CI->load->library('parser');
		$ret=$CI->parser->parse($CI->get_admin_view_file("hit_counter_dashboard"),$data,TRUE);
		
		return $ret;		
	}
}
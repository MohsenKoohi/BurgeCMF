<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{		
		echo "Hello<h1>We are now in our home page ;-)</h1>";
		echo $this->language->get();
		
		return;
		$data=get_initialized_data();
		$data['header_description']='یه اتاق، ثبت و جستجوی اتاق برای اجاره کوتاه مدت و تعطیلات';
		$data['header_keywords']='یه اتقا,جستجوی اتاق,ثبت اتاق,اجاره اتاق,اجاره ویلا,اجاره منزل,تعطیلات,منزل مسکونی,اجاره چند روزه';
		$data['header_canonical_url']=HOME_URL;
		$data['header_title']='یه اتاق';

		//$this->db->cache_delete("default","index");
		//$this->db->cache_on();
		//$this->db->insert('123', array("id"=>"123!@3"));
		//$query = $this->db->get("123");
		//$this->db->cache_off();

		$this->load->model('home_model');

		$search_fields=array();
		$search_fields['status']="finalized";
		$today=jdate("Y/m/d");
		$search_fields['end_date_be']=$today;
		$search_fields['start_date_le']=$today;
			
		$search_fields['limit_length']=20;
		$search_fields['limit_offset']=0;
		$data['search_type']="all";
		$data['homes']=$this->home_model->search($search_fields);
		foreach ($data['homes'] as &$home)
		{
			$home['hm_header']=$home['hm_province']."، ".$home['hm_city'];
			if($home['hm_sub_city'])
				$home['hm_header'].="، ".$home['hm_sub_city'];
			if($home['hm_subject'])
				$home['hm_header'].="، ".$home['hm_subject'];

			if($home['hm_images'][0])
				$home['hm_image']=$home['hm_images']['0']; 
			else
				$home['hm_image']=$data['default_home_image'];

			$home['hm_accepts']="";
			$acc=0;
			if($home['hm_single'])
			{
				$home['hm_accepts'].=" مهمان‌های مجرد";
				$acc++;
			}
			if($home['hm_family'])
			{
				if($acc==0)
					$home['hm_accepts'].=" خانواده‌ها";
				else
					$home['hm_accepts'].=" و خانواده‌ها";

				if(!$home['hm_children'])
					$home['hm_accepts'].=" (بدون کودکان)";
				$acc++;
			}
			
			$home['hm_page_link']=get_home_public_page_link($home['hm_province'],$home['hm_city'],$home['hm_id'],$home['hm_subject']);
		}

		$this->load->library('parser');
		$this->parser->parse('header',$data);
		$this->parser->parse('home',$data);

		//$this->parser->parse('news/index',$data);
		$this->parser->parse('footer',$data);				 
	}

	public function rules()
	{
								
		$data=get_initialized_data();
		$data['header_description']='قوانین یه اتاق';
		$data['header_keywords']='قوانین یه اتقا,راهنما یه اتاق';
		$data['header_canonical_url']=get_link("rules_page");
		$data['header_title'].=' | قوانین';

		//$this->db->cache_delete("default","index");
		//$this->db->cache_on();
		//$this->db->insert('123', array("id"=>"123!@3"));
		//$query = $this->db->get("123");
		//$this->db->cache_off();

		$this->load->library('parser');
		$this->parser->parse('header',$data);
		$this->parser->parse('rules',$data);

		//$this->parser->parse('news/index',$data);
		$this->parser->parse('footer',$data);

		return;				 
	}

	public function prices()
	{
								
		$data=get_initialized_data();
		$data['header_description']='تعرفه‌ها';
		$data['header_keywords']='تعرفه‌های اتقا';
		$data['header_canonical_url']=get_link("prices_page");
		$data['header_title'].='| تعرفه‌ها';

		$data['agent_discount']=User::get_agent_discount_description();
		$data['agent_discount_td_width']=(100/sizeof($data['agent_discount']['headers']));

		$this->load->library('parser');
		$this->parser->parse('header',$data);
		$this->parser->parse('prices',$data);

		//$this->parser->parse('news/index',$data);
		$this->parser->parse('footer',$data);

		return;				 
	}

	public function contact()
	{

		$data=get_initialized_data();
		$data['header_description']='تماس با یه اتاق';
		$data['header_keywords']='تماس با یه اتاق';
		$data['header_canonical_url']=get_link("contact_us_page");
		$data['header_title']='تماس با یه اتاق';


		if($this->input->post())
		{
			$data['message']=array();

			if($this->input->post("captcha") !== $this->session->flashdata("captcha"))
			{
				$data['message'][]='حروف تصویر را به درستی وارد نکرده اید.';
			}
			else
			{
				$this->load->library('form_validation');
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
				$this->form_validation->set_rules('message', 'Message', 'required|min_length[1]');
				$this->form_validation->set_error_delimiters('', '<br>');
				if($this->form_validation->run())
				{
					$this->load->model('contact_messages_model');
					$ref_id=$this->contact_messages_model->add($this->input->post('email'),$this->input->post('message'));
					
					set_message("پیام شما با شماره پیگیری ".$ref_id." ثبت شد. با شما تماس می گیریم.");
					yeotagh_send_mail($this->input->post('email'),"تماس با یه‌اتاق | شماره پیگیری ".$ref_id,"
						کاربر گرامی<br>
						پیام شما در سیستم ما با شماره پیگیری ".$ref_id." ثبت شد.<br>
						یکی از همکاران ما در اولین فرصت با شما تماس خواهد گرفت.<br>
						از تماس شما متشکریم.<br>
					");
					
					redirect(get_link("contact_us_page"));
					return;
				}
			}
			if(form_error('email'))
				$data['message'][]="پست الکترونیکی ثبت شده معتبر نیست.";
			if(form_error('message'))
				$data['message'][]="لطفا فیلد پیغام را تکمیل نمایید.";
			if($data['message'])
				$data['message']=implode("<br>", $data['message']);


			$data['input_email']=$this->input->post('email');
			$data['input_message']=$this->input->post('message');
		}
		else
		{
			$data['input_email']=$this->user_manager_model->get_logged_user_email();
			$data['input_message']="";
		}
		
		$this->load->helper('captcha');
		$vals = array(
	    'word' => random_string("alnum",rand(3,5)),
	    'img_path' => CAPTCHA_DIR."/",
	    'img_url' => CAPTCHA_URL."/",
	    'font_path' => HOME_DIR.'/system/fonts/f'.rand(1,5).'.ttf',
	    'img_width' => '150',
	    'img_height' => 50,
	    'expiration' => 100
	    );
		$cap = create_captcha($vals);
		$this->session->set_flashdata("captcha",$cap['word']);
		$data['captcha']=$cap['image'];

		$this->load->library('parser');
		$this->parser->parse('header',$data);
		$this->parser->parse('contact',$data);
		$this->parser->parse('footer',$data);

		return;				 
	}
}
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function &get_links($just_common=FALSE)
{
	global $LINKS,$LINKS_COMMON;
	if(!$LINKS)
	{
		//we separated links sent to parser which is named $LINKS_COMMON
		//and all links used by get_link which is named $LINKS
		//this action has been done to prevent get_initialized_data()
		//to send our admin urls to parser ;)

		$LINKS_COMMON=array(
			'home_url'				=>	HOME_URL_LANG
			,'home_surl'			=> HOME_SURL_LANG

			,'images_url'			=>	IMAGES_URL
			,'no_image_url'		=> IMAGES_URL."/no-image.png"
			,'styles_url'			=> STYLES_URL
			,'scripts_url'			=> SCRIPTS_URL
			,'upload_url'			=> UPLOAD_URL
		);

		$LINKS=array_merge($LINKS_COMMON, array(
			'admin_url'				=> ADMIN_URL_LANG
			,'admin_surl'				=> ADMIN_SURL_LANG
			,'admin_no_access'		=>	HOME_URL_LANG
			,'admin_login'				=> ADMIN_SURL_LANG."/login"
			,'admin_logout'			=> ADMIN_SURL_LANG."/logout"
			,'admin_dashboard'		=> ADMIN_SURL_LANG."/dashboard"
			,'admin_change_pass'		=> ADMIN_SURL_LANG."/change_pass"
			,'admin_access'			=> ADMIN_SURL_LANG."/access"
			,'admin_user'				=> ADMIN_SURL_LANG."/user"
			,'admin_module'			=> ADMIN_SURL_LANG."/module"
			,'admin_hit_counter'		=> ADMIN_SURL_LANG."/hit_counter"
			,'admin_log'				=> ADMIN_SURL_LANG."/log"
			,'admin_constant'			=> ADMIN_SURL_LANG."/constant"
			
			,'admin_post'									=> ADMIN_SURL_LANG."/post"
			,'admin_post_details_format'				=> ADMIN_SURL_LANG."/post/post_id"
			,'customer_post_details_format'			=> HOME_URL_LANG."/post-post_id/post_name"

			,'admin_file'									=> ADMIN_SURL_LANG."/file"
			,'admin_file_inline'							=> ADMIN_SURL_LANG."/file/inline"
			
			,'admin_category'								=> ADMIN_SURL_LANG."/category"
			,'admin_category_details_format'			=> ADMIN_SURL_LANG."/category/category_id"
			,'customer_category_details_format'		=> HOME_URL_LANG."/category-category_id/category_name/category_page"
		));
	}

	if($just_common)
		return $LINKS_COMMON;
	
	return $LINKS;
}

function get_link($page,$do_not_set_lang=FALSE)
{
	$links=&get_links();
	
	if(isset($links[$page]))
		$ret_link=$links[$page];
	else
		$ret_link=$links['home_url'];

	if($do_not_set_lang)
		return $ret_link;

	static $lang;
	if(!isset($lang))
	{
		$lang_obj=& Language::get_instance();
		$lang=$lang_obj->get();
		if($lang_obj->get_default_language() === $lang)
			$lang="";
	}

	if("" === $lang)
		$lang_pattern="/".URL_LANGUAGE_PATTERN;
	else
		$lang_pattern=URL_LANGUAGE_PATTERN;

	return str_replace($lang_pattern,$lang, $ret_link);
}

function get_admin_post_details_link($post_id,$do_not_set_lang=FALSE)
{
	return str_replace("post_id",$post_id,get_link("admin_post_details_format",$do_not_set_lang));	
}

function get_customer_post_details_link($post_id,$post_name,$do_not_set_lang=FALSE)
{
	$post_name=linkenize($post_name);
	return str_replace(
		array("post_id","post_name")
		,array($post_id,$post_name)
		,get_link("customer_post_details_format",$do_not_set_lang)
	);	
}

function get_admin_category_details_link($category_id,$do_not_set_lang=FALSE)
{
	return str_replace("category_id",$category_id,get_link("admin_category_details_format",$do_not_set_lang));	
}

function get_customer_category_details_link($category_id,$category_name,$page=1,$do_not_set_lang=FALSE)
{
	$search=array("category_id","category_name");
	$replace=array($category_id,$category_name);
	if($page==1)
	{
		$search[]="/category_page";
		$replace[]="";
	}
	else
	{
		$search[]="category_page";
		$replace[]=$page;	
	}

	$ret=str_replace($search,$replace,get_link("customer_category_details_format",$do_not_set_lang));	

	return $ret;
}


//we have created an initialization for data array sent to parser
//so if we wanted to add an index, we can do it without changing all controller. 
//however, it seems it is useless
//if we want to add an index we should set its value for different pages
//...
//OK, we keep it 
function get_initialized_data($CUSOMER_ENV=TRUE)
{
	$links=&get_links(TRUE);
	
	$data=array();
	
	//its not so wise to send all links for the possibility of usage
	//its not also secure
	foreach ($links as $key => $link) 
		$data[$key]=get_link($key);

	if(!$CUSOMER_ENV)
		return $data;
	
	//
	//now we are in customer env

	$data['header_description']='';
	$data['header_keywords']='';
	$data['header_canonical_url']='';
	$data['header_title']='';

	return $data;
}

function get_lang_pages($pattern)
{
	$CI=&get_instance();
	$langs=$CI->language->get_languages();	
	$def=$CI->language->get_default_language();
	$cur=$CI->language->get();

	$ret=array();
	foreach ($langs as $key => $value)
	{
		$selected=($key === $cur);

		if($key === $def)
			$lang_index="";
		else
			$lang_index=$key;

		if("" === $lang_index)
			$lang_pattern="/".URL_LANGUAGE_PATTERN;
		else
			$lang_pattern=URL_LANGUAGE_PATTERN;

		$ret[$value]=array(
			"link"=> str_replace($lang_pattern,$lang_index, $pattern)
			,"selected"=>$selected
			,"lang_abbr"=>$key
		);
	}

	return $ret;
}

function linkenize($name){
  $pattern_page = explode(' ',"+ , - ' \" & ! ? : ; # ~ = / $ £ ^ ( ) _ < > ؟ » « ) ( ْ ٌ ٍ ً ُ ِ َ ّ ] [ } { ؛ ٔ ٓ ، × ٪ ﷼ ٫ ÷ |");
  $name=str_replace($pattern_page,'', $name);
  $name=trim(preg_replace('/[\s\.]+/', ' ',$name));
  $link=str_replace(" ","-",$name);
 
  return $link;
}

function get_current_time()
{
	$tf=DATE_FUNCTION;
	$time=$tf("Y/m/d H:i:s");
	return $time;
}

function get_template_dir($lang)
{
	return $lang;
}


function set_message($message)
{
	$CI=&get_instance();
	$CI->session->set_userdata("message",$message);

	return;
}

function get_message()
{
	$CI=&get_instance();
	$message=$CI->session->userdata("message");
	$CI->session->unset_userdata("message");
	return $message;
}

function price_separator($val)
{
	$val="".$val;
	$newVal="";
	$j=0;
	for($i=strlen($val)-1;$i>=0;$i--,$j++)
	{
		$newVal=$val[$i].$newVal;
		if($j%3==2 && $j!=(strlen($val)-1))
			$newVal=",".$newVal;
	}

	return $newVal;
}

function persian_normalize(&$data)
{
	if(is_array($data))
	{
		foreach ($data as &$value)
			$value=persian_normalize_word($value);
		
		return;
	}
	
	if(is_string($data))
	{
		$data=persian_normalize_word($data);
		return $data;
	}

	return;
}

function persian_normalize_word($word)
{
	if(!$word || !is_string($word) || !strlen($word))
		return $word;
	$search=array("ي","ئ","ك","۰","۱","۲","۳","۴","۵","۶","۷","۸","۹");
	$replace=array("ی","ی","ک","0","1","2","3","4","5","6","7","8","9");
	
	return strip_tags(str_replace($search, $replace, $word));
}


function rial_to_toman($value)
{
	$ret="";
	$milion=floor($value/10000000);
	if($milion>0)
		$ret.="<span class='value'>".$milion."</span> میلیون ";
	$value=$value%10000000;
	$hezar=floor($value/10000);
	if($hezar>0)
	{
		if($ret)
			$ret.=" و ";
		$ret.="<span class='value'>".$hezar."</span> هزار";
	}
	else
		if(!$ret)
			$ret=" 0 ";

	$ret.=" تومان";

	return $ret;
}

function bprint_r($var)
{
	if(!isset($var))
	{
		echo "nothing to print.<br>";
		return;
		
	}
	echo "<pre>";
	print_r($var);
	echo "</pre>";

	return;
}

function validate_persian_date(&$date)
{
	$date=explode("/",$date);
	if(sizeof($date)!=3)
		return false;

	foreach ($date as &$value)
		$value=intval(trim($value));
	
	if($date[0]>1500 || $date[0]<1300)
		return false;

	if($date[1]>12 || $date[1]<1)
		return false;

	if($date[2]>31 || $date[2]<1)
		return false;

	if($date[1]<10)
		$date[1]="0".$date[1];

	if($date[2]<10)
		$date[2]="0".$date[2];

	$date=implode("/", $date);

	return true;
}

function validate_mobile(&$mobile)
{
	$mobile=(int)$mobile;
	$mobile="".$mobile;
	if(strlen($mobile)!=10)
		return false;

	$pre=(int)substr($mobile,0,3);
	if($pre<=900 || $pre>940)
		return false;

	$mobile="0".$mobile;

	return true;
}

function get_random_word($length)
{
	//$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$pool = '123456789ABCDEFGHIJLMNPQRST';

	$str = '';
	for ($i = 0; $i < $length; $i++)
	{
		$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
	}

	$word = $str;
   
   return $word;
}

function get_captcha($length=0)
{
	if(!$length)
		$length=rand(4,5);
	$vals = array(
    'word' => get_random_word($length),
    'img_path' => CAPTCHA_DIR."/",
    'img_url' => CAPTCHA_URL."/",
    'font_path' => HOME_DIR.'/system/fonts/f'.rand(3,4).'.ttf',
    'font_size' => rand(20,25),
    'img_width' => '150',
    'img_height' => 50,
    'expiration' => 10
    );

	$cap = create_captcha($vals);

	$CI=&get_instance();
	$CI->session->set_flashdata("captcha",$cap['word']);
	
	return $cap['image'];	
}

function verify_captcha($val)
{
	$CI=&get_instance();
	$captcha=$CI->session->flashdata("captcha");
	if(!$captcha || (strtolower($captcha) !== strtolower($val)))
		return FALSE;

	return TRUE;
}

/**
 * Create CAPTCHA
 *
 * @param	array	$data		data for the CAPTCHA
 * @param	string	$img_path	path to create the image in
 * @param	string	$img_url	URL to the CAPTCHA image folder
 * @param	string	$font_path	server path to font
 * @return	string
 */
function create_captcha($data = '', $img_path = '', $img_url = '', $font_path = '')
{
	$defaults = array(
		'word'		=> '',
		'img_path'	=> '',
		'img_url'	=> '',
		'img_width'	=> '150',
		'img_height'	=> '30',
		'font_path'	=> '',
		'expiration'	=> 7200,
		'word_length'	=> 8,
		'font_size'	=> 16,
		'img_id'	=> '',
		'pool'		=> '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
		'colors'	=> array(
			'background'	=> array(255,255,255),
			'border'	=> array(153,102,102),
			'text'		=>array(rand(50,250),rand(100,150),rand(100,150)),// array(204,153,153),
			'grid'		=> array(255,182,182)
		)
	);

	foreach ($defaults as $key => $val)
	{
		if ( ! is_array($data) && empty($$key))
		{
			$$key = $val;
		}
		else
		{
			$$key = isset($data[$key]) ? $data[$key] : $val;
		}
	}

	if ($img_path === '' OR $img_url === ''
		OR ! is_dir($img_path) OR ! is_really_writable($img_path)
		OR ! extension_loaded('gd'))
	{
		return FALSE;
	}

	// -----------------------------------
	// Remove old images
	// -----------------------------------

	$now = microtime(TRUE);

	$current_dir = @opendir($img_path);
	while ($filename = @readdir($current_dir))
	{
		if (substr($filename, -4) === '.jpg' && (str_replace('.jpg', '', $filename) + $expiration) < $now)
		{
			@unlink($img_path.$filename);
		}
	}

	@closedir($current_dir);

	// -----------------------------------
	// Do we have a "word" yet?
	// -----------------------------------

	if (empty($word))
	{
		$word = '';
		for ($i = 0, $mt_rand_max = strlen($pool) - 1; $i < $word_length; $i++)
		{
			$word .= $pool[mt_rand(0, $mt_rand_max)];
		}
	}
	elseif ( ! is_string($word))
	{
		$word = (string) $word;
	}

	// -----------------------------------
	// Determine angle and position
	// -----------------------------------
	$length	= strlen($word);
	$angle	= ($length >= 6) ? mt_rand(-($length-6), ($length-6)) : 0;
	$x_axis	= mt_rand(6, (360/$length)-16);
	$y_axis = ($angle >= 0) ? mt_rand($img_height, $img_width) : mt_rand(6, $img_height);

	// Create image
	// PHP.net recommends imagecreatetruecolor(), but it isn't always available
	$im = function_exists('imagecreatetruecolor')
		? imagecreatetruecolor($img_width, $img_height)
		: imagecreate($img_width, $img_height);

	// -----------------------------------
	//  Assign colors
	// ----------------------------------

	is_array($colors) OR $colors = $defaults['colors'];

	foreach (array_keys($defaults['colors']) as $key)
	{
		// Check for a possible missing value
		is_array($colors[$key]) OR $colors[$key] = $defaults['colors'][$key];
		$colors[$key] = imagecolorallocate($im, $colors[$key][0], $colors[$key][1], $colors[$key][2]);
	}

	// Create the rectangle
	ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $colors['background']);

	// -----------------------------------
	//  Create the spiral pattern
	// -----------------------------------
	$theta		= 1;
	$thetac		= 7;
	$radius		= 16;
	$circles	= 20;
	$points		= 32;

	for ($i = 0, $cp = ($circles * $points) - 1; $i < $cp; $i++)
	{
		$color=array(rand(50,150),rand(150,200),rand(100,200));
		$color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
		
		$theta += $thetac;
		$rad = $radius * ($i / $points);
		$x = ($rad * cos($theta)) + $x_axis;
		$y = ($rad * sin($theta)) + $y_axis;
		$theta += $thetac;
		$rad1 = $radius * (($i + 1) / $points);
		$x1 = ($rad1 * cos($theta)) + $x_axis;
		$y1 = ($rad1 * sin($theta)) + $y_axis;
		imageline($im, $x, $y, $x1, $y1, $color);
		$theta -= $thetac;
	}

	// -----------------------------------
	//  Write the text
	// -----------------------------------

	$use_font = ($font_path !== '' && file_exists($font_path) && function_exists('imagettftext'));
	if ($use_font === FALSE)
	{
		($font_size > 5) && $font_size = 5;
		$x = mt_rand(0, $img_width / ($length / 3));
		$y = 0;
	}
	else
	{
		$font_size2=$font_size+rand(0,10)-5;
		($font_size2 > 30) && $font_size2 = 30;
		$x = mt_rand(0, $img_width / ($length / 1.5));
		$y = $font_size2 + 2;
	}

	for ($i = 0; $i < $length; $i++)
	{
		if ($use_font === TRUE)
		{
			$font_size2=$font_size+rand(0,10)-5;
			($font_size2 > 30) && $font_size2 = 30;
		}

		$color=array(rand(50,250),rand(100,150),rand(100,150));
		$color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
		if ($use_font === FALSE)
		{
			$y = mt_rand(0 , $img_height / 2);
			imagestring($im, $font_size2, $x, $y, $word[$i], $color);
			$x += ($font_size2 * 2);
		}
		else
		{
			$y = mt_rand($img_height / 2, $img_height - 3);
			imagettftext($im, $font_size2, $angle, $x, $y, $color, $font_path, $word[$i]);
			$x += $font_size2;
		}
	}

	// Create the border
	imagerectangle($im, 0, 0, $img_width - 1, $img_height - 1, $colors['border']);

	// -----------------------------------
	//  Generate the image
	// -----------------------------------
	$img_url = rtrim($img_url, '/').'/';

	if (function_exists('imagejpeg'))
	{
		$img_filename = $now.'.jpg';
		imagejpeg($im, $img_path.$img_filename);
	}
	elseif (function_exists('imagepng'))
	{
		$img_filename = $now.'.png';
		imagepng($im, $img_path.$img_filename);
	}
	else
	{
		return FALSE;
	}

	$img = '<img class="captcha" '.($img_id === '' ? '' : 'id="'.$img_id.'"').' src="'.$img_url.$img_filename.'" style="width: '.$img_width.'; height: '.$img_height .'; border: 0;" alt=" " />';
	ImageDestroy($im);

	return array('word' => $word, 'time' => $now, 'image' => $img, 'filename' => $img_filename);
}

//returns 2=> exists and writable
//returns 1=> made and writable
//returns -1=> exists and not writable
//returns -2=> doesn't not exist and can't be made

function make_dir_and_check_permission($dir)
{
	if(file_exists($dir))
	{
		if(is_writable($dir))
			return 2;
		else
			return -1;
	}
	else
		if(!@mkdir($dir,0777))
			return -2;
		

	return 1;
}

//selects indexes and values from the $cand arrya
//for indexes that are in $main_indexes
function select_allowed_elements($cand ,$main_indexes)
{
	$result=array();
	foreach ($main_indexes as $index) 
		if(array_key_exists($index,$cand))
			$result[$index]=$cand[$index];

	return $result;
}

function delete_prefix_of_indexes($props,$prefix)
{
	$new_props=array();

	foreach($props as $index => $value)
	{
		$new_index=preg_replace("/^".$prefix."/", "", $index);
		$new_props[$new_index]=$props[$index];
	}

	return $new_props;
}

function burge_cmf_watermark(
	$image_path
	,$watermark_ratio=0
	,$watermark_path=NULL
	,$ver_align="top"
	,$hor_aling="right"
)
{
	$CI=&get_instance();
	if(!$watermark_path)
		$watermark_path="images/watermark.png";
	if(!$watermark_ratio)
		$watermark_ratio=1/4;

	$CI->load->library("image_lib");

	list($wx,$wy)=getimagesize($watermark_path);
	list($ix,$iy)=getimagesize($image_path);
	//echo $wx." ".$wy." ".$ix." ".$iy."<br>";

	$y_ratio=$wy/$iy;
	$x_ratio=$wx/$ix;
	//echo $y_ratio." ".$x_ratio."<br>";

	if(max($y_ratio,$x_ratio) > $watermark_ratio)
	{
		$watermark_temp = sys_get_temp_dir()."/".time().".png";
		file_put_contents($watermark_temp,file_get_contents($watermark_path));

		$config['source_image'] = $watermark_temp;
		$config['maintain_ratio'] = TRUE;
	
		if($y_ratio > $x_ratio)
		{	
			$new_wy=$watermark_ratio*$iy;
			$new_wx=$wx*($new_wy/$wy);
		}
		else
		{
			$new_wx=$watermark_ratio*$ix;
			$new_wy=$wy*($new_wx/$wx);
		}
		$config['height'] = $new_wy;	
		$config['width'] = $new_wx;

		$CI->image_lib->clear();			
		$CI->image_lib->initialize($config);
		$result=$CI->image_lib->resize();
		if(!$result)
			return FALSE;

		$watermark_path=$watermark_temp;
	}

	//echo $watermark_path;
	//exit();

	$CI->image_lib->clear();

	$config['source_image']=$image_path;
	$config['wm_type']='overlay';
	$config['quality']="100%";
	$config['wm_vrt_alignment']=$ver_align;
	$config['wm_hor_alignment']=$hor_aling;
	$config['wm_overlay_path']=$watermark_path;
	$config['wm_opacity']="100";

	$CI->image_lib->initialize($config);
	$result=$CI->image_lib->watermark();

	if(isset($watermark_temp))
		unlink($watermark_temp);

	return $result;
}

function burge_cmf_send_mail($receiver,$subject,$content)
{
	$CI=&get_instance();
	$CI->load->library("email");
	$CI->email->initialize(array(
		"protocol"=>"smtp",
		"smtp_host"=>"",
		"smtp_port"=>25,
		"smtp_user"=>"",
		"smtp_pass"=>"",
		"mailtype"=>"html"
		));
	
	$CI->email->from('from@email.com', 'Your Name');
	$CI->email->to($receiver);
	$CI->email->bcc('bcc@email.com');
	$CI->email->subject($subject);
	$CI->email->message('
		<!DOCTYPE html>
		<html dir="rtl" lang="fa">
		<head>
			<meta charset="UTF-8" />
		</head>
		<body style="direction:rtl;font-family:b koodak, koodak,b mitra, mitra, tahoma;">
			<div style=";height:150px;display:block;text-align:center;direction:rtl;">
				 <a title="" alt="" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/logo-text-fa.png"  style="height:130px" ></a>
			 <a title="" alt="" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/logo-notext.png"  style="height:130px"></a>		    
			</div>

			<div class="main" style="direction:rtl;font-size:1.3em;direction:rtl;font-family:b koodak, koodak, b mitra, mitra, tahoma;text-align:justify;line-height:1.8em;background:white;min-height:200px">'
			.$content.'
			</div>		 
			<br>
			<div style="direction:rtl;text-align:center;font-family:b koodak, koodak, b mitra, mitra, tahoma;display:block;font-size:1em;color:darkgreen;">Your<br><span style="width:50px;display:inline-block"></span>Slogan</div> 
		</body>
		</html>');

	$CI->email->send();

	return;
}

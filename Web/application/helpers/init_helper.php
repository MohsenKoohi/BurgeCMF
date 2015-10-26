<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function &get_links()
{
	global $LINKS;
	if(!$LINKS)
		$LINKS=array(
		'home_url'				=>	HOME_URL_LANG
		,'home_surl'			=> HOME_SURL_LANG

		,'images_url'			=>	IMAGES_URL
		,'styles_url'			=> STYLES_URL
		,'scripts_url'			=> SCRIPTS_URL


		,'admin_url'				=> ADMIN_URL_LANG
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
		,'admin_post'				=> ADMIN_SURL_LANG."/post"


	);
	
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

function get_initialized_data()
{
	$links=&get_links();
	
	$data=array();
	
	foreach ($links as $key => $link) 
		$data[$key]=get_link($key);

	$data['header_description']='';
	$data['header_keywords']='';
	$data['header_canonical_url']='';
	$data['header_title']='';

	//do it yourself ;-)
	//$data['message']=get_message();

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
		);
	}

	return $ret;
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

function burge_cmf_send_mail($receiver,$subject,$content)
{
	$CI=&get_instance();
	$CI->load->library("email");
	$CI->email->initialize(array(
		"protocol"=>"smtp",
		"smtp_host"=>"mail.lonex.com",
		"smtp_port"=>2525,
		"smtp_user"=>"admin@yeotagh.com",
		"smtp_pass"=>"j4788dxS3m4N32zS=32>2zagg)",
		"mailtype"=>"html"
		));
	
	$CI->email->from('admin@yeotagh.com', 'یه اتاق');
	$CI->email->to($receiver);
	$CI->email->bcc('admin@yeotagh.com');
	$CI->email->subject($subject);
	$CI->email->message('
		<!DOCTYPE html>
		<html dir="rtl" lang="fa">
		<head>
			<meta charset="UTF-8" />
		</head>
		<body style="direction:rtl;font-family:b koodak, koodak, OnLineKoodak, b mitra, mitra, tahoma;">
		  <div style=";height:150px;display:block;text-align:center;direction:rtl;">
		  	 <a title="یه‌اتاق" alt="یه‌اتاق" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/yo-logo-bg-2.png"  style="" ></a>
		    <a title="یه‌اتاق" alt="یه‌اتاق" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/yo-logo-v2-bg-2.png"  style=""></a>		    
		  </div>

		  <div class="main" style="direction:rtl;font-size:1.3em;direction:rtl;font-family:b koodak, koodak, b mitra, mitra, tahoma;text-align:justify;line-height:1.8em;background:white;min-height:200px">'
		  .$content.'
		  </div>		 
		  <br>
		  <div style="direction:rtl;text-align:center;font-family:b koodak, koodak, b mitra, mitra, tahoma;display:block;font-size:.9em;color:darkgreen;">سفر، یک زندگی جدید، هرچند کوتاه</div> 
		</body>
		</html>');

	$CI->email->send();

	return;
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

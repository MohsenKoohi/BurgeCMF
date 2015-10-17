<?php

//It's true that Language class is a Manager,
//but since we want to call it before every initialization,
//it can't be depend on CI_Model.
//Thus we don't put it on models folder as a manager./core ;)
class Language
{
	static public $languages=NULL;
	static public $selected_language=NULL;
	static private $default_language=NULL;
	static private $instance=NULL;

	public function __construct()
	{
		
	}

	public function init()
	{
		if(self::$languages)
			return;

		self::$languages=LANGUAGES();
		if(!self::$languages)
		{
			log_message("error","Language array hasn't been defined");
			return ;
		}

		$index=substr_count(HOME_URL, '/')-2;
		$parts=explode("/",trim($_SERVER['REQUEST_URI'],"/"));
		if(sizeof($parts)==$index)
			$parts[]="";
		$parts[$index]=strtolower($parts[$index]);
		
		$i=0;
		foreach (self::$languages as $key => $lang)
		{
			if(!$i++)
				self::$default_language=$key;

			if($key === $parts[$index])
			{
				self::$selected_language=$key;
				break;
			}
		}

		if(!self::$selected_language)
		{
			self::$selected_language=self::$default_language;
		}
		else
		{
			$new_uri="";
			for($i=0;$i<sizeof($parts);$i++)
				if($i!=$index)
					$new_uri.="/".$parts[$i];

			$_SERVER['REQUEST_URI']=$new_uri;
		}

		//echo self::$selected_language."<br>".$_SERVER['REQUEST_URI']."<br>";
		self::$instance=& $this;
		
		return;
	}

	static public function get()
	{
		return self::$selected_language;
	}

	static public function get_default_language()
	{
		return self::$default_language;
	}

	static public function get_languages()
	{
		return self::$languages;
	}

	static public function &get_instance()
	{
		return self::$instance;
	}

	public function init_CI_instance()
	{
		$CI=get_instance();
		$CI->language=&$this;

		return;
	}

}


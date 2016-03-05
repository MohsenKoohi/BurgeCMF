<?php 

$lang['comma']=",";
$lang['header_separator']=" | ";
$lang['main_name']="BurgeCMF";
$lang['header_title']="BurgeCMF";
$lang['slogan']="BurgeCMF";
$lang['header_meta_keywords']="BurgeCMF, Burge Content Management Framework, Open-Source MVC CMF";
$lang['header_meta_description']="";

$lang['read_more']="More";
$lang['page']="Page";
$lang['contact_us']="Contact us";

$lang['email_template']='
	<!DOCTYPE html>
	<html dir="ltr" lang="en">
	<head>
		<meta charset="UTF-8" />
	</head>
	<body style="direction:ltr;font-family:Times new roman, Arial;">
		<div style=";height:150px;display:block;text-align:center;direction:ltr;">
			 <a title="" alt="" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/logo-text.png"  style="height:130px" ></a>
		 <a title="" alt="" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/logo-notext.png"  style="height:130px"></a>		    
		</div>
		<div class="main" style="direction:ltr;font-size:1.3em;font-family:Time new roman,Arial;line-height:1.8em;background:white;min-height:200px">
		$content
		</div>		 
		<br>
		<div style="direction:ltr;text-align:center;font-family:Times new roman, Arial;display:block;font-size:1em;color:#024240;">'.$lang['slogan'].'</div>
	</body>
	</html>';
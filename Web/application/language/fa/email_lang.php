<?php 

$lang['response_to']="پاسخ به پیام شما:";
$lang['email_template']='
	<!DOCTYPE html>
	<html dir="rtl" lang="fa">
	<head>
		<meta charset="UTF-8" />
	</head>
	<body style="direction:rtl;font-family:b koodak, koodak,b mitra, mitra, tahoma;">
		<div style=";height:150px;display:block;text-align:center;direction:rtl;">
			 <a title="" alt="" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/logo-text-fa.png"  style="max-height:130px" ></a>
		 <a title="" alt="" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/logo-notext.png"  style="max-height:130px"></a>		    
		</div>
		<div class="main" style="direction:rtl;font-size:1.3em;font-family:b koodak, koodak, b mitra, mitra, tahoma;text-align:justify;line-height:1.8em;background:white;min-height:200px">
		$content
		</div>		 
		<br>
		<div style="direction:rtl;text-align:center;font-family:b koodak, koodak, b mitra, mitra, tahoma;display:block;font-size:1em;color:#0C7B77;">$slogan</div>
		<hr>
		<br>
		$response_to
	</body>
	</html>';
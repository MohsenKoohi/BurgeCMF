<?php 

$lang['response_to']="Response to:";
$lang['email_template']='
	<!DOCTYPE html>
	<html dir="ltr" lang="en">
	<head>
		<meta charset="UTF-8" />
	</head>
	<body style="direction:ltr;font-family:Times new roman, Arial;">
		<div style=";height:150px;display:block;text-align:center;direction:ltr;">
			 <a title="" alt="" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/logo-text.png"  style="max-height:130px" ></a>
		 <a title="" alt="" href="'.HOME_URL.'"><img src="'.IMAGES_URL.'/logo-notext.png"  style="max-height:130px"></a>		    
		</div>
		<div class="main" style="direction:ltr;font-size:1.3em;font-family:Times new roman,Arial;line-height:1.8em;background:white;min-height:200px">
		$content
		</div>		 
		<br>
		<div style="direction:ltr;text-align:center;font-family:Times new roman, Arial;display:block;font-size:1em;color:#0C7B77;">$slogan</div>
		<hr>
		<br>
		$response_to		
	</body>
	</html>';
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="description" content="{header_description}"/>
	<meta name="keywords" content="{header_keywords}"/>
  <meta name="viewport" content="width=device-width,initial-scale=1, user-scalable=yes">
	<title>{header_title}</title>
  <link rel="canonical" href="{header_canonical_url}"/>
	<link rel="shortcut icon" href="{images_url}/favicon.png"/> 

  <link rel="stylesheet" type="text/css" href="{styles_url}/jquery-ui.min.css" />
  <link rel="stylesheet" type="text/css" href="{styles_url}/colorbox.css" />
  <link rel="stylesheet" type="text/css" href="{styles_url}/skeleton.css" />  
  <link rel="stylesheet" type="text/css" href="{styles_url}/style-ltr.css" />  
  <link rel="stylesheet" type="text/css" href="{styles_url}/admin.css" />
  
  <!--[if ! lte IE 8]>-->
    <script src="{scripts_url}/jquery-2.1.3.min.js"></script>
  <!--<![endif]-->
  
  <!--[if lte IE 8]>
    <script src="{scripts_url}/jquery-1.11.1.min.js"></script>
  <![endif]-->  
  <script src="{scripts_url}/jquery-ui.min.js"></script>
  <script src="{scripts_url}/admin_common.js"></script>
  <script src="{scripts_url}/colorbox.js"></script>
  <script src="{scripts_url}/scripts.js"></script>

  
</head>
<body class="ltr" style="height:100%;">
  <div class="header">

    <div class="logo">
      <a href="<?php echo get_link('admin_surl');?>" class="logo-img"><img src="{images_url}/logo-notext.png"></a>
      <a href="<?php echo get_link('admin_surl');?>" class="logo-text"><img src="{images_url}/logo-text.png"></a>
    </div>
    
    <div class="top-menu">  
      <ul>
        <!--
          <li>
            <a  href=""></a>
          </li>
        -->
        <?php if(sizeof($lang_pages)>1) { ?>
          <li class="has-sub lang-li">
            <a class="lang"></a>
            <ul>
              <?php foreach($lang_pages as $lang => $spec ) { ?>
                <li>
                  <a
                    <?php
                      $class="lang-".$spec['lang_abbr'];
                      if($spec['selected'])
                        $class.=" selected";
                      echo "class='$class'";
                    ?>  
                    href="<?php echo $spec['link']; ?>">
                      <?php echo $lang;?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>
      </ul>
    </div>

  </div>
  <div class="content">
    <div>
      <?php if(isset($user_logged_in) && $user_logged_in) { ?>
        <div class="side-menu">
          <div class="mobile">
            <img src="{images_url}/logo-text.png"/>
            <div class="click">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
          </div>
           <ul class="side-menu-ul">
            <?php 
              if(isset($side_menu_modules))
                foreach ($side_menu_modules as $mod) 
                  echo "<li><a href='".$mod['link']."'>".$mod['name']."</a></li>\n";
            ?>
          </ul>
        </div><?php } ?>

      <div class="message-main">
        <?php if(isset($message) && strlen($message)>0)
          echo '<div class="message">'.$message.'</div>';
        ?>
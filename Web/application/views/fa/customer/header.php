<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
	<meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1, user-scalable=yes">
  <meta name="keywords" content="{header_meta_keywords}"/>
  <meta name="description" content="{header_meta_description}"/>
  <?php if(isset($header_meta_robots)) {?> 
    <meta name="robots" content="{header_meta_robots}" />
  <?php } ?>
	<title>{header_title}</title>
  <?php if(isset($header_canonical_url) && $header_canonical_url) {?> 
    <link rel="canonical" href="{header_canonical_url}"/>
  <?php } ?>
  <?php if(isset($header_next_url)) {?> 
    <link rel="next" href="{header_next_url}"/>
  <?php } ?>
  <?php if(isset($header_prev_url)) {?> 
    <link rel="prev" href="{header_prev_url}"/>
  <?php } ?>
  <?php 
    if(isset($lang_pages))
    { if(sizeof($lang_pages)>1) 
        foreach($lang_pages as $lp)
        {
          $abbr=$lp['lang_abbr'];
          $link=$lp['link'];
          echo '<link rel="alternate" hreflang="'.$abbr.'" href="'.$link.'" />'."\n";
        }
      else
      {
        $langs=array_keys($lang_pages);
        $lang=$langs[0];
        echo '<link rel="alternate" hreflang="x-default" href="'.$lang_pages[$lang]['link'].'" />'."\n";
      }
    }
  ?>

	<link rel="shortcut icon" href="{images_url}/favicon.png"/> 
  <link rel="apple-touch-icon-precomposed" sizes="64x64" href="{images_url}/favicon.png" />
  <link rel="apple-touch-icon" sizes="64x64" href="{images_url}/favicon.png" />


  <link rel="stylesheet" type="text/css" href="{styles_url}/skeleton.css" />  
  <link rel="stylesheet" type="text/css" href="{styles_url}/style-rtl.css" />  
  <link rel="stylesheet" type="text/css" href="{styles_url}/customer.css" />

  <?php if(isset($page_main_image)) { ?>
    <meta property="og:image" content="{page_main_image}" >
    <meta property="og:title" content="{header_title}">
    <meta property="og:description" content="{header_description}">
    <meta property="og:url" content="{header_canonical_url}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{main_name_text}">
  <?php } ?>
  
  <!--[if ! lte IE 8]>-->
    <script src="{scripts_url}/jquery-2.1.3.min.js"></script>
  <!--<![endif]-->
  
  <!--[if lte IE 8]>
    <script src="{scripts_url}/jquery-1.11.1.min.js"></script>
  <![endif]-->  
  <script src="{scripts_url}/customer_common.js"></script>
  <script src="{scripts_url}/scripts.js"></script>
  <!--[if lte IE 9]>
    <link rel="stylesheet" type="text/css" href="{styles_url}/style-ie.css" />
  <![endif]-->
  
</head>
<body class="rtl customer-env" style="height:100%;">
  <div class="header">

    <div class="logo">
      <a href="<?php echo get_link('home_url')?>" class="logo-img"><img src="{images_url}/logo-notext.png"/></a>
      <a href="<?php echo get_link('home_url')?>" class="logo-text"><img src="{images_url}/logo-text-fa.png" /></a>
    </div>
    
    <div class="top-menu">  
      <ul>
        <!--
          <li>
            <a  href=""></a>
          </li>
        -->
        <?php if(isset($lang_pages) && sizeof($lang_pages)>1) { ?>
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
      <div class="side-menu-container">
        <div class="side-menu">
          <div class="mobile">
            <img src="{images_url}/logo-text-fa.png"/>
            <div class="click">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
          </div>
          <ul class="side-menu-ul">
            <?php 
              foreach($categories as $cat)
              {
                if(!$cat['show_in_list'])
                  continue;

                $id=$cat['id'];
                $name=$cat['names'][$selected_lang];
                $link=get_customer_category_details_link($id,$cat['hash'],$cat['urls'][$selected_lang]);
                echo "<li><a href='$link'>$name</a>\n";

                if($cat['children'])
                {
                  echo "<ul>\n";
                  foreach($cat['children'] as $child)
                  {
                    if(!$child['show_in_list'])
                      continue;
                    
                    $id=$child['id'];
                    $name=$child['names'][$selected_lang];
                    $link=get_customer_category_details_link($id,$child['hash'],$child['urls'][$selected_lang]);
                    echo "<li><a href='$link'>$name</a>\n";
                  }
                  echo "</ul>\n";
                }
                echo "</li>\n";
              }
            ?>
            <li><a href='<?php echo get_link("customer_contact_us");?>'>{contact_us_text}</a>
          </ul>
        </div>
      </div>

      <div class="message-main">
        <?php if(isset($message) && strlen($message)>0)
          echo '<div class="message">'.$message.'</div>';
        ?>
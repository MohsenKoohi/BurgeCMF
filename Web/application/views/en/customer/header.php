<!DOCTYPE html>
<html dir="rtl" lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="keywords" content="{header_keywords}"/>
  <meta name="description" content="{header_description}"/>
  <meta name="viewport" content="width=device-width,initial-scale=1, user-scalable=yes">
  <title>{header_title}</title>
  <link rel="canonical" href="{header_canonical_url}"/>
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

  <link rel="stylesheet" type="text/css" href="{styles_url}/jquery-ui.min.css" />
  <link rel="stylesheet" type="text/css" href="{styles_url}/colorbox.css" />
  <link rel="stylesheet" type="text/css" href="{styles_url}/skeleton.css" />  
  <link rel="stylesheet" type="text/css" href="{styles_url}/customer/style-common.css" />
  <link rel="stylesheet" type="text/css" href="{styles_url}/customer/style-ltr.css" />  
  
  <!--[if ! lte IE 8]>-->
    <script src="{scripts_url}/jquery-2.1.3.min.js"></script>
  <!--<![endif]-->
  
  <!--[if lte IE 8]>
    <script src="{scripts_url}/jquery-1.11.1.min.js"></script>
  <![endif]-->  
  <script src="{scripts_url}/jquery-ui.min.js"></script>
  <script src="{scripts_url}/customer_common.js"></script>
  <script src="{scripts_url}/colorbox.js"></script>
  <script src="{scripts_url}/scripts.js"></script>
  <!--[if lte IE 9]>
    <link rel="stylesheet" type="text/css" href="{styles_url}/style-ie.css" />
  <![endif]-->
  
</head>
<body class="ltr" style="height:100%;">
  <div class="header">

    <div class="logo">
      <a href="<?php echo get_link('home_url')?>" class="logo-img"><img src="{images_url}/logo-notext.png"/></a>
      <a href="<?php echo get_link('home_url')?>" class="logo-text"><img src="{images_url}/logo-text.png" /></a>
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
                <li><a <?php if($spec['selected']) echo "class='selected'";?>
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
    <?php if(isset($message) && strlen($message)>0)
      echo '<div class="message">'.$message.'</div>';
    ?>

    <div>
      <?php if(TRUE || isset($side_menu_modules)) { ?>
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
           <ul>
            <li>Item1</li>
            <li>Item2</li>
            <li>Item3</li>
            <?php 
              if(isset($side_menu_modules))
                foreach ($side_menu_modules as $mod) 
                  echo "<li><a href='".$mod['link']."'>".$mod['name']."</a></li>";
            ?>
          </ul>
        </div><?php } ?>
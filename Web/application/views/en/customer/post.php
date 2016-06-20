<link rel="stylesheet" type="text/css" href="{styles_url}/colorbox.css" />
<script src="{scripts_url}/colorbox.js"></script>
  
<div class="main">
	<div class="container">
		<h1><?php echo $post_info['pc_title'];?></h1>
		<div class="post-date"><?php echo str_replace("-","/",$post_info['post_date']);?></div>
		<div class="row">
			<?php if($post_info['pc_image']) { ?>
				<div class="post-img" style="background-image:url('<?php echo $post_info['pc_image'];?>')">
				</div>
				<br><br>
			<?php } ?>
		</div>
		<div class="row">
			<div class="twelve columns post-content">
				<?php echo $post_info['pc_content'] ?>
			</div>
		</div>
		<div class="row post-gallery">
			<?php foreach($post_gallery as $img) { ?>
				<div class="four columns img-div" title="<?php echo $img['text'];?>"  href="{post_gallery_url}/<?php echo $img['image'];?>" >
					<div class="img"  style="background-image:url({post_gallery_url}/<?php echo $img['image'];?>)">
					</div>
					<div class="text">
						<?php echo $img['text'];?>
					</div>
				</div>
			<?php } ?>

			<script type="text/javascript">

				$(window).on("resize",setColorBox);
				$(window).load(function()
				{
					$("body").addClass("post-page");
					setColorBox();
				});

				function setColorBox()
				{
					$.colorbox.remove() ;
					
					var whp="90%";
					if($(window).width() > 600)
						whp="75%";

					$(".img-div").colorbox({
						rel:"group"
						,iframe:false
						,width:whp
						,height:whp
						,opacity:.4
						,fixed:true
						,current:"{image_text} {current} {from_text} {total}" 

					});
				}
			</script>
		</div>
	</div>
</div>
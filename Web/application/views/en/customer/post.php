<link rel="stylesheet" type="text/css" href="{styles_url}/colorbox.css" />
<script src="{scripts_url}/colorbox.js"></script>
  
<div class="main">
	<div class="container">
		<div class="row post-cats">
			<?php foreach($post_categories as $cat) { 
				if($cat['is_hidden'])
					continue;
			?>
				<div class="">
					<a href="<?php echo $cat['url'];?>">
						<?php echo $cat['name'];?>
					</a>
				</div>
			<?php } ?>
		</div>

		<h1><?php echo $post_info['pc_title'];?></h1>			
		<div class="post-date"><?php echo str_replace("-","/",$post_info['post_date']);?></div>
		<div class="row">
			<?php if($post_info['pc_image']) { ?>
				<div class="post-img">
					<img class="lazy-load" data-ll-type="src"
						data-ll-url="<?php echo $post_info['pc_image'];?>"
					/>
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
			<?php 
				if($post_gallery)
					foreach($post_gallery as $img)
					{ 
			?>
				<div class="four columns img-div" title="<?php echo $img['text'];?>"  href="{post_gallery_url}/<?php echo $img['image'];?>" >
					<div class="img lazy-load"  data-ll-url="{post_gallery_url}/<?php echo $img['image'];?>"
					 data-ll-type="background-image" >
					</div>
					<div class="text">
						<?php echo $img['text'];?>
					</div>
				</div>
			<?php } ?>

			<script type="text/javascript">

				$(window).load(function()
				{
					$("body").addClass("post-page");
					$(window).on("resize",setColorBox);
					setColorBox();
				});

				function setColorBox()
				{
					$.colorbox.remove();
					$(".img-div").unbind("click");

					if($(window).width() < 600)
						$(".img-div").click(function(event)
						{
							window.open($(event.target).parent().attr("href"));
						});
					else
						$(".img-div").colorbox({
							rel:"group"
							,iframe:false
							,width:"80%"
							,height:"80%"
							,opacity:.4
							,fixed:true
							,current:"{image_text} {current} {from_text} {total}" 

						});
				}
			</script>
		</div>
	</div>
</div>
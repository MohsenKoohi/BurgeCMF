<style type="text/css">
	.post-gallery  > div
	{
		padding:5px; 
		line-height: 0;
	}
	.post-gallery img
	{
		width: 100%;
	}

	.post-gallery .text
	{
		background-color: #555;
		color:white;
		padding: 5px;
		line-height: 1em;
		
	}
</style>
<div class="main">
	<div class="container category">
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
				<div class="four columns">
					<img src="{post_gallery_url}/<?php echo $img['image'];?>"/>
					<div class="text">
						<?php echo $img['text'];?>
					</div>
				</div>
			<?php } ?>
	</div>
</div>
<div class="main">
	<div class="container category">
		<h1><?php echo $category_info['cd_name'];?></h1>
		<div class="row">
			<div class="twelve columns">
				<?php if($category_info['cd_image']) { ?>
					<div class="post-img">
						<img class="lazy-load" data-ll-type="src"
					 		data-ll-url="<?php echo $category_info['cd_image'];?>"
					 	/>
					</div>
				<?php } ?>
				<br>
				<div class="post-short-desc">
					<?php echo nl2br($category_info['cd_description']); ?>
				</div>
			</div>
			<?php if($total_pages>1) { ?>
				&nbsp;<br>
				<div class="three columns results-page-select">
					<select class="full-width" onchange="document.location=$(this).val();">
						<?php 
							for($i=1;$i<=$total_pages;$i++)
							{
								$sel="";
								if($i==$current_page)
									$sel="selected";
								$link=str_replace("page_number",$i,$pages_format);
								echo "<option $sel value='$link'>$page_text $i</option>\n";
							}
						?>
					</select>
				</div>
			<?php }?>
		</div>
		<br>

		<?php
			foreach($posts as $post)
			{
		?>
				<div class="row">
					<div class="twelve columns">
						<a href="<?php echo get_customer_post_details_link($post['post_id'],$post['pc_title']);?>" >
							<h2><?php echo $post['pc_title'];?></h2>
							<div class="post-date"><?php echo str_replace("-","/",$post['post_date']);?></div>
							<?php if($post['pc_image']) { ?>
								<div class="post-img">
									<img class="lazy-load" data-ll-type="src"
										data-ll-url="<?php echo $post['pc_image'];?>"
									/>
								</div>
							<?php } ?>
							<br>
							<div class="post-short-desc">
								<?php 
									$content=$post['pc_content'];
									$content=preg_replace("/\s*<br\s*\/?>\s*/","\n",$content);
									$content=str_replace("&nbsp;"," ", $content);
									$content=strip_tags($content);
									$content=mb_substr($content,0,100);								
									$content=preg_replace("/(\s*\n+\s*)+/", "<br/>", $content);
									
									echo $content."...";
								?>	
							</div>
							<br>
							<div class="read-more">{read_more_text}</div>
						</a>
					</div>
					
				</div>
		<?php
			}
		?>
	</div>
</div>

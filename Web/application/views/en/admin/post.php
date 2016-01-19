<div class="main">
	<div class="container">
		<h1>{posts_text}</h1>

		<div class="row general-buttons">
			<div class="three columns">
				<?php echo form_open(get_link("admin_post"),array());?>
					<input type="hidden" name="post_type" value="add_post"/>
					<input type="submit" class="button button-primary full-width" value="{add_post_text}"/>
				</form>
			</div>
		</div>
		<br><br>
		<div class="container">
			<?php $i=1;foreach($posts_info as $post) { ?>
				<a target="_blank" href="<?php echo get_admin_post_details_link($post['post_id']);?>">
					<div class="row even-odd-bg" >
						<div class="nine columns">
							<span>
								<?php echo $i++;?>)
								<?php 
									if($post['pc_title']) 
										echo $post['pc_title'];
									else
										echo $no_title_text;
								?>
							</span>
						</div>
					</div>
				</a>
			<?php } ?>
		</div>

	</div>
</div>
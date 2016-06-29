<div class="main">
	<style type="text/css">
		.cat-sign
		{
			font-family: tahoma;	
			color:#0C7B77	;
		}

		.cat-name
		{
			font-size:1.1em;
		}

		.even-odd-bg span.cat-parent
		{
			font-size: .8em;
		}
	</style>
	<div class="container">
		<h1>{categories_text}</h1>
		<div class="row general-buttons">
			<div class="three columns">
				<?php echo form_open(get_link("admin_category"),array()); ?>
					<input name="post_type" value="add_category" type="hidden"/>
					<input class="button button-primary full-width" 
						value="{add_category_text}" type="submit"/>
				</form>
			</div>
		</div>
		<br><br>
		<div class="container">
			<?php 
				$i=1;
				foreach($categories as $cat) { 
					if(!$cat['id'])
						continue;
					$par_names=array();
					for($j=sizeof($cat['parents'])-1;$j>=0;$j--)
					{
						$par_id=$cat['parents'][$j];
						if($par_id)
							$par_names[]=$categories[$par_id]['names'][$selected_lang];
					}
					$par_names[]=" ";
					//bprint_r($par_names);
					$par_name=implode($next_category_sign_text, $par_names);	
			?>				
				<div class="row even-odd-bg dont-magnify" >
					<div class="two columns counter">
						#<?php echo $cat['id'];?>
					</div>
					<div class="nine columns">
						<span class="cat-parent"><?php echo $par_name; ?></span>
						<a target="_blank" class="cat-name" href="<?php echo get_admin_category_details_link($cat['id']);?>">
							<?php 
								if($cat['names'][$selected_lang]) 
									echo $cat['names'][$selected_lang];
								else
									echo $no_title_text;
							?>
						
						</a>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
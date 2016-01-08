<div class="main">
	<div class="container">
		<h1>{post_details_text} {post_id}
			<?php 
			if($post_info && $post_info[0]['pc_title']) 
				echo $comma_text." ".$post_info[0]['pc_title'];
			?>
		</h1>
		
		<?php 
			if(!$post_info) {
		?>
			<h4>{not_found_text}</h4>
		<?php 
			}else{ 
		?>
			<div class="container separated">
				<h2>{constants_list_text}</h2>		
					<?php echo form_open(get_link("admin_constant"),array()); ?>
						<input type="hidden" name="post_type" value="constants_list" />
						<?php if(0)foreach($constants as $cons) {?>
							<div class="row even-odd-bg" >
								<div class="three columns">
									<label>{name_text}</label>
									<?php echo $cons['constant_key'];?>
								</div>
								<div class="six columns">
									<label>{value_text}</label>
									<input name="value_<?php echo $cons['constant_key']?>" 
										value="<?php echo $cons['constant_value']?>" type="text" class="full-width"
										onkeypress="valueChanged(this);"
									/>
									<input name="changed_<?php echo $cons['constant_key']?>" type="checkbox" style="display:none"/>
								</div>
								<div class="three columns">
									<label>{delete_text}</label>
									<input name="delete_<?php echo $cons['constant_key']?>" type="checkbox" class="graphical" />
								</div>
							</div>
						<?php } ?>
						<br><br>
						<div class="row">
								<div class="four columns">&nbsp;</div>
								<input type="submit" class=" button-primary four columns" value="{submit_text}"/>
						</div>				
					</form>
			</div>
		<?php 
			}
		?>
		



	</div>
</div>
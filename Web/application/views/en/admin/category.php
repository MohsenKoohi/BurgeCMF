<div class="main">
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
	</div>
</div>
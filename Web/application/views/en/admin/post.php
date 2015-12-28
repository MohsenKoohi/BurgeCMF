<div class="main">
	<div class="container">
		<h1>{posts_text}</h1>

		<div class="row">
			<div class="three columns">
				<?php echo form_open(get_link("admin_post"),array());?>
					<input type="hidden" name="post_type" value="add_post"/>
					<input type="submit" class="button button-primary full-width" value="{add_post_text}"/>
				</form>
			</div>
		</div>


		<div class="container">
			<div class="row even-odd-bg" >
				<div class="three columns">
					<label>{module_id_text}</label>
				</div>
				<div class="three columns">
					<label>{module_name_text}</label>
				</div>
			</div>
			
		</div>

	</div>
</div>
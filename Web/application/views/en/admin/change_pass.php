<div class="main">
	<div class="container">
		<h1>{change_pass_text}</h1>
		<div class="container width-400 no-center">
			<?php echo form_open(get_link("admin_change_pass"),array()); ?>
				<div class="col12 columns mrg-btn-20">
					<label>{prev_pass_text}</label>
					<input class="ltr full-width" type="password" name="prev_pass" />
				</div>
				<div class="col12 columns mrg-btn-20">
					<label>{new_pass_text}</label>
					<input class="ltr full-width" type="password" name="new_pass"/>
				</div>
				<div class="col12 columns mrg-btn-20">
					<label>{repeat_pass_text}</label>
					<input class="ltr full-width" type="password" name="repeat_pass"/>
				</div>
			
				<div class="col12 columns">				
					<input class="full-width button-primary" type="submit"  value="{change_text}" />
				</div>
			</form>
		</div>
	</div>
</div>
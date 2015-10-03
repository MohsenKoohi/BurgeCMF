<div class="main">
	<div class="container width-400">
		<h1>{login_text}</h1>
		<?php echo form_open(get_link("admin_login"),array()); ?>
			<div class="col12 columns mrg-btn-20">
				<label>{email_text}</label>
				<input class="ltr full-width" type="text" name="email" />
			</div>
			<div class="col12 columns mrg-btn-20">
				<label>{password_text}</label>
				<input class="ltr full-width" type="password" name="pass"/>
			</div>
			<div class="col12 columns mrg-btn-40">
				<labeL>{captcha}</label>
				<input class="ltr full-width" type="text" name="captcha" />
			</div>
		
			<div class="12 columns">				
				<input class="full-width button-primary" type="submit"  value="{sign-in_text}" />
			</div>
		</form>
	</div>
</div>
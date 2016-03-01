<div class="main">
	<div class="container">
		<h1>{contact_us_text}</h1>

		<div class="container">
			<?php echo form_open(get_link("customer_contact_us"),array()); ?>
				<div class="row">
					<div class="three columns">
						<label>{name_text}</label>
					</div>
					<div class="nine columns">
						<input name="name" type="text" class="full-width"/>
					</div>
				</div>
				<div class="row">
					<div class="three columns">
						<label>{email_text}</label>
					</div>
					<div class="nine columns">
						<input name="email" type="email" class="full-width eng ltr"/>
					</div>
				</div>
				<?php if(isset($department)) { ?>
					<div class="row">
						<div class="three columns">
							<label>{department_text}</label>
						</div>
						<div class="nine columns">
							<input name="department" class="full-width"/>
						</div>
					</div>
				<?php } ?>
				<div class="row">
					<div class="three columns">
						<label>{subject_text}</label>
					</div>
					<div class="nine columns">
						<input name="subject" class="full-width"/>
					</div>
				</div>
				<div class="row">
					<div class="three columns">
						<label>{content_text}</label>
					</div>
					<div class="nine columns">
						<textarea name="content" class="full-width" rows="5"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="three columns">
						{captcha}
					</div>
					<div class="nine columns">
						<input name="captcha" class="ltr eng"/>
					</div>
				</div>
				<div class="row">
					<div class="six columns">&nbsp;</div>
					<input type="submit" class=" button-primary three columns" value="{submit_text}"/>
				</div>
			</form>
		</div>
	</div>
</div>
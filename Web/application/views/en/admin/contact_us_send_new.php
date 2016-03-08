<div class="main">
	<div class="container">
		<h1>{send_new_message_text}</h1>		
		<?php echo form_open(get_link("admin_contact_us_send_new"),array()); ?>
			<input type="hidden" name="post_type" value="send_message" />			
			<div class="row even-odd-bg">
				<div class="three columns">
					<span>{receiver_email_text}</span>
				</div>
				<div class="nine columns">
					<textarea name="receivers"  class="full-width lang-en" rows="2"></textarea>
					{split_emails_by_enter_or_semicolumn_text}
				</div>
			</div>
			<div class="row even-odd-bg">
				<div class="three columns">
					<span>{language_text}</span>
				</div>
				<div class="three columns">
					<select name="language" class="full-width" onchange="langChanged(this);">
						<?php
							foreach($all_langs as $key => $val)
							{
								$sel="";
								if($key===$selected_lang)
									$sel="selected";

								echo "<option $sel value='$key'>$val</option>";
							}
						?>
					</select>
					<script type="text/javascript">
						var langSelectVal;

						function langChanged(el)
						{
							if(langSelectVal)
							{
								$("#subject-in").toggleClass(langSelectVal);
								$("#content-ta").toggleClass(langSelectVal);
							}

							langSelectVal="lang-"+""+$(el).val();
							
							$("#subject-in").toggleClass(langSelectVal);
							$("#content-ta").toggleClass(langSelectVal);
						}

						$(function()
						{
							$("select[name='language']").trigger("change");
						});
					</script>
				</div>
			</div>

			<div class="row even-odd-bg">
				<div class="three columns">
					<span>{subject_text}</span>
				</div>
				<div class="nine columns">
					<input id="subject-in" name="subject"  class="full-width" />
				</div>
			</div>

			<div class="row even-odd-bg">
				<div class="three columns">
					<span>{content_text}</span>
				</div>
				<div class="nine columns">
					<textarea id="content-ta" name="content" class="full-width" rows="15"></textarea>
				</div>
			</div>
			<br><br>
			<div class="row">
				<div class="four columns">&nbsp;</div>
				<input type="submit" class=" button-primary four columns" value="{send_text}"/>
			</div>
		</form>				
	</div>
</div>
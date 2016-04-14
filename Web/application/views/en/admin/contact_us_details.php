<div class="main">
	<div class="container">
		<h1>{contact_us_text}{comma_text} {message_text} {message_id}
			<?php 
			if($info && $info['cu_message_subject']) 
				echo $comma_text." ".$info['cu_message_subject']
			?>
		</h1>		
		<?php 
			if(!$info) {
		?>
			<h4>{not_found_text}</h4>
		<?php 
			}else{ 
		?>
			<div class="container">
				<div class="row general-buttons">
					<div class="two columns button sub-primary button-type2" onclick="deleteMessage()">
						{delete_text}
					</div>
				</div>
				<br><br>
				<div class="separated">
					<h2>{message_text}</h2>
					<div class="row even-odd-bg">
						<div class="three columns">
							<span>{ref_id_text}</span>
						</div>
						<div class="nine columns">
							<?php echo $info['cu_ref_id'];?>
						</div>
					</div>
					<div class="row even-odd-bg">
						<div class="three columns">
							<span>{received_time_text}</span>
						</div>
						<div class="nine columns">
							<span style="display:inline-block" class="ltr">
								<?php echo $info['cu_message_time'];?>
							</span>
						</div>
					</div>
					<div class="row even-odd-bg">
						<div class="three columns">
							<span>{sender_text}</span>
						</div>
						<div class="nine columns">
							<?php echo $info['cu_sender_name']."<br>".$info['cu_sender_email']; ?>
						</div>
					</div>
					<div class="row even-odd-bg">
						<div class="three columns">
							<span>{subject_text}</span>
						</div>
						<div class="nine columns">
							<?php 
								if($info['cu_message_department'])
									echo $info['cu_message_department']."<br>";
								echo $info['cu_message_subject'];
							?>
						</div>
					</div>
					<div class="row even-odd-bg">
						<div class="three columns">
							<span>{content_text}</span>
						</div>
						<?php
							if(preg_match("/[ابپتثجچحخدذرز]/",$info['cu_message_content']))
								$lang="fa";
							else
								$lang="en";
						?>
						<div class="nine columns lang-<?php echo $lang;?>">
							<?php 
								echo nl2br($info['cu_message_content']);
							?>
						</div>
					</div>
				</div>
				<?php 
					if($info['cu_response_time']) {
				?>
					<div class="separated">
						<h2>{the_last_response_text}</h2>
						<div class="row even-odd-bg">
							<div class="three columns">
								<span>{response_time_text}</span>
							</div>
							<div class="nine columns">
								<span style="display:inline-block" class="ltr">
									<?php echo $info['cu_response_time'];?>
								</span>
							</div>
						</div>
						<div class="row even-odd-bg">
							<div class="three columns">
								<span>{user_text}</span>
							</div>
							<div class="nine columns">
								{code_text} <?php echo $info['user_code']." - ".$info['user_name'];?>
							</div>
						</div>
						<div class="row even-odd-bg">
							<div class="three columns">
								<span>{response_content_text}</span>
							</div>
							<?php
								if(preg_match("/[ابپتثجچحخدذرز]/",$info['cu_response']))
									$lang="fa";
								else
									$lang="en";
							?>
							<div class="nine columns lang-<?php echo $lang;?>">
								<?php echo nl2br($info['cu_response']);?>
							</div>
						</div>
					</div>
				<?php 
					}
				?>
				<div class="separated">
					<?php echo form_open(get_admin_contact_us_message_details_link($message_id),array()); ?>
					<input type="hidden" name="post_type" value="send_response" />			
						<h2>{response_text}</h2>						
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
								<input id="subject-in" name="subject"  class="full-width" 
									value="<?php echo $info['cu_message_subject'];?>"
								/>
							</div>
						</div>

						<div class="row even-odd-bg">
							<div class="three columns">
								<span>{response_content_text}</span>
							</div>
							<div class="nine columns">
								<textarea id="content-ta" name="content" class="full-width" rows="5"></textarea>
							</div>
						</div>
						<br><br>
						<div class="row">
							<div class="four columns">&nbsp;</div>
							<input type="submit" class=" button-primary four columns" value="{send_text}"/>
						</div>
					</form>
				</div>

				<div style="display:none">
					<?php echo form_open(get_admin_contact_us_message_details_link($message_id),array("id"=>"delete")); ?>
						<input type="hidden" name="post_type" value="delete_message"/>
						<input type="hidden" name="post_id" value="{message_id}"/>
					</form>

					<script type="text/javascript">
						
	              	function deleteMessage()
						{
							if(!confirm("{are_you_sure_to_delete_this_message_text}"))
								return;

							$("form#delete").submit();
						}

					</script>
				</div>
			</div>
		<?php 
			}
		?>
	</div>
</div>
<div class="main">
	<div class="container">
		<h1>{access_levels_text}</h1>

		<div class="row">
			<div class="three columns">
				<select name="acccess_type" onchange="typeChanged(this);" class="full-width">
					<option value="user">{user_text}</option>
					<option value="user_group">{user_group_text}</option>
				</select>
			</div>

			<div class="one columns">
				&nbsp;
			</div>

			<div class="four columns">
				<select name="users" onchange="" class="full-width">
					<option value="">&nbsp;</option>
					<?php
						foreach($users_info as $u)
						{
							$uname= $u['user_name']." ($code_text ".$u['user_code'].")";
							echo "<option value='-".$u['user_id']."'>$uname</option>";
						}
					?>
				</select>
			</div>

			<div class="four columns">
				<select name="user_groups" onchange="" class="full-width">
					<option value="">&nbsp;</option>
					<?php
						foreach($user_groups_info as $g)
							echo "<option value='".$g['ug_id']."'>".$g['ug_name']."</option>";
					?>
				</select>
			</div>
			<script type="text/javascript">
				function typeChanged(el)
				{
					$("select[name=user_groups]").hide();
					$("select[name=users]").hide();

					$("select[name="+$(el).val()+"s]").show();
				}

				$(window).load(
					function()
					{
						$("select[name=acccess_type]").trigger("change");
					}
				);
			</script>
		</div>

		<div class="tab-container">
			<ul class="tabs">
				<li><a href="#modules">{modules_text}</a></li>
			</ul>
			<script type="text/javascript">
				$(function(){
				   $('ul.tabs').each(function(){
						var $active, $content, $links = $(this).find('a');
						$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
						$active.addClass('active');

						$content = $($active[0].hash);

						$links.not($active).each(function () {
						   $(this.hash).hide();
						});

						$(this).on('click', 'a', function(e){
						   $active.removeClass('active');
						   $content.hide();

						   $active = $(this);
						   $content = $(this.hash);

						   $active.addClass('active');

						   $content.show();						   	

						   e.preventDefault();
						   
						});
					});
				});
			</script>
			
			<div class="tab" id="modules">
			</div>
		</div>

		<div class="container separated user-div" style="display:none">
			<h2>{acess_level_for_a_user_text}</h2>		
			<?php echo form_open(get_link("admin_access"),array("onsubmit"=>"return userFormSubmitted()")); ?>
				<input type="hidden" name="post_type" value="user_access" />
				<div class="row">
					<div class="three columns"><label>{user_name_text}</label></div>
					<div class="three columns">
						<select class="full-width ltr eng" name="user_id">
							<option value="">{select_text}</option>
							<?php 
								foreach ($users_info as $user)
								{
									echo "<option value='".$user['user_id']."'>".$user['user_email']."</option>";
								}
							?>
						</select>
					</div>
				</div>
				
				<br><br>
				<div class="row">
						<div class="four columns">&nbsp;</div>
						<input type="submit" class=" button-primary four columns" value="{submit_text}"/>
				</div>				
			</form>
		</div>



	</div>
</div>
<div class="main">
	<div class="container">
		<h1>{access_levels_text}</h1>

		<div class="row">
			<div class="three columns">
				<select name="access_type" onchange="typeChanged(this);" class="full-width">
					<option value="user">{user_text}</option>
					<option value="user_group">{user_group_text}</option>
				</select>
			</div>

			<div class="one columns">
				&nbsp;
			</div>

			<div class="four columns">
				<select name="users" class="full-width" onchange="locationChanged(this);">
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
				<select name="user_groups" class="full-width" onchange="locationChanged(this);">
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

				var changeLocation="<?php echo get_admin_access_details_link('access_id');?>";

				function locationChanged(el)
				{
					var val=$(el).val();
					if(val)
						document.location=changeLocation.replace("access_id",val);
				}

				$(window).load(
					function()
					{
						if("{access_type}")
							$("select[name=access_type]").val("{access_type}");
						$("select[name=access_type]").trigger("change");
						$("select[name=user_groups],select[name=users]").val("{access_id}");
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
				<h2>{modules_text}</h2>
				<?php 
					if($access_id)
					{
				?>
						<?php echo form_open($form_submit_link); ?>
							<input type="hidden" name="post_type" value="set_modules_access" />

							<?php foreach($modules_info as $module){ ?>
								<div class="row even-odd-bg">
									<div class="three columns">
										<span><?php echo $module['module_name']?></span>
									</div>
									<div class="three columns">
										<input type='checkbox' class='graphical' name='module_ids[]'
											value='<?php echo $module['module_id'];?>' 
											<?php if(in_array($module['module_id'],$modules_have_access_to)) echo 'checked'; ?>
										/>
									</div>
								</div>
							<?php } ?>
							<br><br>
							<div class="row">
									<div class="four columns">&nbsp;</div>
									<input type="submit" class=" button-primary four columns" value="{submit_text}"/>
							</div>
						</form>
				<?php 
					}
				?>
			</div>
		</div>




	</div>
</div>
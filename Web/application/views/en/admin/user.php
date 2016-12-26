<div class="main">
	<div class="container">
		<h1>{users_text}</h1>

		<div class="tab-container">
			<ul class="tabs">
				<li><a href="#users_list">{users_list_text}</a></li>
				<li><a href="#add_user">{add_user_text}</a></li>
				<li><a href="#user_groups_list">{user_groups_list_text}</a></li>
				<li><a href="#add_user_group">{add_user_group_text}</a></li>
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
			
			<div class="tab" id="users_list">
				<h2>{users_list_text}</h2>		
				<div class="row" style="">
					<div class="three columns">
						<b>{user_text}</b>
					</div>
					<div class="six columns">
						<select class="full-width" 
							onchange="document.location='<?php echo get_admin_user_details_link('user_id');?>'.replace('user_id',$(this).val());"
						>
							<option value="0">&nbsp;</option>
							<?php 
								$user_info=NULL;

								foreach($users_info as $user)
								{
									$uid= $user['user_id'];
									$uname= $user['user_name']." ($code_text ".$user['user_code'].")";
									$sel="";
									if($uid == $user_id)
									{
										$sel='selected';
										$user_info=$user;
									}

									echo "<option $sel value='$uid'>$uname</option>";
								}
							?>
						</select>
					</div>
				</div>
				<br><br>
				<?php 
					if($user_info)
					{
						echo form_open(get_admin_user_details_link($user_id),array()); 
				?>
						<input type="hidden" name="post_type" value="modify_users" />
						
						<div class="row even-odd-bg" >
							<div class="three columns">
								{email_text}
							</div>
							<div class="six columns">
								<?php echo $user_info['user_email'];?>
							</div>
						</div>
						<div class="row even-odd-bg" >
							<div class="three columns">
								{new_password_text}
							</div>
							<div class="six columns">
								<input name="password" type="password" class="ltr eng full-width"/>
							</div>
						</div>
						<div class="row even-odd-bg" >
							<div class="three columns">
								{name_text}
							</div>
							<div class="six columns">
								<input value="<?php echo $user_info['user_name']?>" name="name"  class="full-width"/>
							</div>
						</div>
						<div class="row even-odd-bg" >
							<div class="three columns">
								{code_text}
							</div>
							<div class="six columns">
								<input value="<?php echo $user_info['user_code']?>" name="code"  class="full-width"/>
							</div>
						</div>
						<div class="row even-odd-bg" >
							<div class="three columns">
								{user_group_text}
							</div>
							<div class="six columns">
								<select type="text" name="group_id" class="full-width">
									<option value="0">&nbsp;</option>
									<?php
										foreach($user_groups as $ug)
										{
											$sel='';
											if($ug['ug_id']==$user_info['user_group_id'])
												$sel='selected';
											echo "<option $sel value='".$ug['ug_id']."'>".$ug['ug_name']."</option>";
										}
									?>
								</select>
							</div>
						</div>
						<div class="row even-odd-bg" >
							<div class="three columns">
								{delete_text}
							</div>
							<div class="six columns">
								<input name="delete" type="checkbox" class="graphical" />
							</div>
						</div>
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

			<div class="tab" id="add_user">
				<h2>{add_user_text}</h2>	
				<?php echo form_open(get_link("admin_user"),array()); ?>
					<input type="hidden" name="post_type" value="add_user" />	
					<div class="row even-odd-bg" >
						<div class="four columns">
							<label>{email_text}</label>
							<input type="text" name="email" class="ltr eng full-width" />
						</div>
						<div class="four columns">
							<label>{password_text}</label>
							<input type="password" name="password" class="ltr eng full-width"/>
						</div>
						<div class="four columns">
							<label>{name_text}</label>
							<input type="text" name="name" class="full-width"/>
						</div>
						<div class="four columns">
							<label>{code_text}</label>
							<input type="text" name="code" class="full-width"/>
						</div>
						<div class="four columns">
							<label>{user_group_text}</label>
							<select type="text" name="group_id" class="full-width">
								<option value="0">&nbsp;</option>
								<?php 
									foreach($user_groups as $ug)
										echo "<option value='".$ug['ug_id']."'>".$ug['ug_name']."</option>";
								?>
							</select>
						</div>
					</div>
					<br><br>
					<div class="row">
							<div class="four columns">&nbsp;</div>
							<input type="submit" class=" button-primary four columns" value="{add_text}"/>
					</div>				
				</form>
			</div>

			<div class="tab" id="user_groups_list">
				<h2>{user_groups_list_text}</h2>		
				<?php echo form_open(get_link("admin_user"),array()); ?>
					<input type="hidden" name="post_type" value="modify_user_groups" />
					<?php foreach($user_groups as $ug) {?>
						<div class="row even-odd-bg" >
							<div class="six columns">
								<label>{name_text}</label>
								<input type='text' class='full-width' value='<?php echo $ug['ug_name'];?>' name='ug_name_id_<?php echo $ug['ug_id'];?>'/>
							</div>
							<div class="two columns">
							</div>
							<div class="two columns">
								<label>{delete_text} </label>
								<input name="delete_user_group_id_<?php echo $ug['ug_id']?>" type="checkbox" class="graphical" />
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

			<div class="tab" id="add_user_group">
				<h2>{add_user_group_text}</h2>	
				<?php echo form_open(get_link("admin_user"),array()); ?>
					<input type="hidden" name="post_type" value="add_user_group" />	
					<div class="row even-odd-bg" >
						<div class="six columns">
							<label>{name_text}</label>
							<input type="text" name="name" class="full-width"/>
						</div>
					</div>
					<br><br>
					<div class="row">
							<div class="four columns">&nbsp;</div>
							<input type="submit" class=" button-primary four columns" value="{add_text}"/>
					</div>				
				</form>
			</div>
		</div>

		<div class="container separated">
			
		</div>

	</div>
</div>
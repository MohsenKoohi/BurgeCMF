<div class="main">
	<div class="container">
		<h1>{access_levels_text}</h1>

		<div class="container separated user-div">
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
				<?php foreach($modules_info as $mod) {?>
					<div class="row even-odd-bg" >
						<div class="three columns">
							<label><?php echo $mod['module_name']?></label>
						</div>
						<div class="three columns">
							<input class="graphical" name="module_id_<?php echo $mod['module_id']?>" type="checkbox" />
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


		<div class="container separated module-div">
			<h2>{access_level_for_a_module_text}</h2>		
			<?php echo form_open(get_link("admin_access"),array("onsubmit"=>"return moduleFormSubmitted()")); ?>
				<input type="hidden" name="post_type" value="module_access" />
				<div class="row">
					<div class="three columns"><label>{module_name_text}</label></div>
					<div class="three columns">
						<select class="full-width" name="module_id">
							<option value="">{select_text}</option>
							<?php 
								foreach ($modules_info as $mod)
								{
									echo "<option value='".$mod['module_id']."'>".$mod['module_name']."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<?php foreach($users_info as $user) {?>
					<div class="row even-odd-bg" >
						<div class="three columns">
							<label><?php echo $user['user_email']?></label>
						</div>
						<div class="three columns">
							<input class="graphical" name="user_id_<?php echo $user['user_id']?>" type="checkbox" />
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


<script type="text/javascript">
	var access_info=JSON.parse('<?php echo	json_encode($access_info);?>');
	var selected_user_id="{selected_user_id}";
	var selected_module_id="{selected_module_id}";

	$(function()
	{
		$(".user-div select").change(userChanged);
		$(".module-div select").change(moduleChanged);

		$(".user-div select").val(selected_user_id)
		setTimeout(userChanged,100);
		$(".module-div select").val(selected_module_id);
		setTimeout(moduleChanged,100);

	})

	function userChanged()
	{
		$(".user-div input[type='checkbox']").prop("checked","");
		var user_id=$(".user-div select").val();
		if(user_id)
			for(i in access_info[user_id])
			{
				$(".user-div input[type='checkbox'][name='module_id_"+i+"']").prop("checked","checked");		
			}
	}

	function userFormSubmitted()
	{
		var user_id=$(".user-div select").val();
		
		if(!user_id)
			return false;
		
		return true;
	}

	function moduleChanged()
	{
		$(".module-div input[type='checkbox']").prop("checked","");
		var module_id=$(".module-div select").val();
		if(module_id)
			for(i in access_info)
			{

				if(access_info[i][module_id])
					$(".module-div input[type='checkbox'][name='user_id_"+i+"']").prop("checked","checked");		
			}
	}

	function moduleFormSubmitted()
	{
		var module_id=$(".module-div select").val();
		
		if(!module_id)
			return false;
		
		return true;
	}

</script>


	</div>
</div>
{total_text}: <?php echo sizeof($users); ?><br>
<ul class="dash-ul" style="padding:10px">
	<?php
		$i=1;
		foreach($users as $us)
		{
			echo "<li>".$us['user_name']."</li>";
			if($i++>5)
				break;
		}
	?>
</ul>
<h4>{user_groups_text}</h4>
{total_text}: <?php echo sizeof($user_groups); ?><br>
<ul class="dash-ul" style="padding:10px">
	<?php
		$i=1;
		foreach($user_groups as $ug)
		{
			echo "<li>".$ug['ug_name']."</li>";
			if($i++>5)
				break;
		}
	?>
</ul>
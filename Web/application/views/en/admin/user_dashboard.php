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
{total_text}: <?php echo sizeof($modules); ?><br>
<ul class="dash-ul" style="padding:10px">
	<?php
		$i=1;
		foreach($modules as $md)
		{
			echo "<li>".$md['module_name']."</li>";
		}
	?>
</ul>
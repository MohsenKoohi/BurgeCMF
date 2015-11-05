<ul class="dash-ul eng ltr" style="padding:10px">
	<?php 
		foreach($logs as $log)
		{
			echo "<li class='eng ltr'>";
			$j=0;
			foreach ($log as $key => $value)
			{
				if($key==="visitor_id")
					continue;

				if($j++)
					echo ", ";
				echo $key.": ".$value;
			}
			echo "</li><br>";
		}
	?>	
</ul>

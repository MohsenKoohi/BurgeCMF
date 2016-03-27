<ul class="dash-ul eng ltr" style="padding:10px">
	<?php 
		for($i=$logs['start']; $i<$logs['end'];$i++)
		{
			$log=$logs[$i];

			echo "<li class='en ltr'>";
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

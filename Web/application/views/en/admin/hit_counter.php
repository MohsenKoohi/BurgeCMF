<div class="main">
	<div class="container">
		<h1>{visiting_counter_text}</h1>

		<div class="container">
			<?php $i=1;foreach($counters_info as $counter) {?>
				<div class="row even-odd-bg" >
					<div class="one column">
						<label><?php echo $i++;?></label>
					</div>
					<div class="two columns">
						{url_text}
						<label><?php echo $counter['url'];?></label>
					</div>
					<div class="two columns">
						{monthly_visit_text}
						<label><?php echo $counter['month_count']?></label>
					</div>
					<div class="two columns">
						{yearly_visit_text}
						<label><?php echo $counter['year_count']?></label>
					</div>
					<div class="two columns">
						{total_visit_text}
						<label><?php echo $counter['total_count']?></label>
					</div>
				</div>
			<?php } ?>
		</div>

	</div>
</div>
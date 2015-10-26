<div class="main">
	<style type="text/css">
		body.ltr label.url
		{
			margin-right:20px;
		}

		body.rtl label.url
		{
			margin-left: 20px;
		}

		.row.even-odd-bg span
		{
			font-size: .8em;
			//color:#555;
		}

	</style>
	<div class="container">
		<h1>{visiting_counter_text}</h1>
			<?php $i=1;foreach($counters_info as $counter) {?>
				<div class="row even-odd-bg" >
					<div class="one column">
						<label><?php echo $i++;?></label>
					</div>
					<div class="five columns">
						<span>{url_text}</span>
						<label class="ltr eng url"><?php echo $counter['url'];?></label>
					</div>
					<div class="two columns">
						<span>{monthly_visit_text}</span>
						<label><?php echo $counter['month_count']?></label>
					</div>
					<div class="two columns">
						<span>{yearly_visit_text}</span>
						<label><?php echo $counter['year_count']?></label>
					</div>
					<div class="two columns">
						<span>{total_visit_text}</span>
						<label><?php echo $counter['total_count']?></label>
					</div>
				</div>
			<?php } ?>
	</div>
</div>
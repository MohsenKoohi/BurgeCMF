<div class="main">
	<style type="text/css">
		.row.even-odd-bg span
		{
			font-size: .8em;
			//color:#555;
		}

		.row.even-odd-bg div label
		{
			overflow:hidden;
			text-overflow: ellipsis;
		}

		.row.even-odd-bg div:first-child label
		{
			font-size: 2em;
			color:#0C7B77;
		}

		.row.even-odd-bg:nth-child(2n+2) div:first-child label
		{
			color:#C63672;
		}

	</style>
	<div class="container">
		<h1>{log_text}</h1>
		<div class="container separated">
			<h2>{toaday_last_logs_text}</h2>
			<?php $i=1;foreach($logs as $log) { ?>
				<div class="row even-odd-bg" >
					<div class="three columns">
						<label>#<?php echo $i++;?></label>
					</div>
					<?php foreach ($log as $key => $value) { 
					?>
						<div class="three columns eng ltr">
							<span><?php echo $key;?></span>
							<label class="eng ltr"><?php echo $value;?></label>
						</div>
					<?php } ?>				
				</div>
			<?php } ?>
		</div>
	</div>
	<script type="text/javascript">
		$(function()
		{
			$(".row.even-odd-bg div label").each(
				function(index,el)
				{
					$(el).prop("title",$(el).text());
				}
			);
		});
	</script>
</div>
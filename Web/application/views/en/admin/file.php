<div class="main">
	<div class="container">
		<h1>{files_text}</h1>

		<div class="containter separated">
			<iframe id="inline" style="border:0;width:100%" ></iframe>
		</div>
	</div>
	<script type="text/javascript">
		$(function()
		{
			$("#inline")
				.prop("src","<?php echo get_link('admin_file_inline');?>")
				.css("height",$(window).height()-100);
		});
	</script>
</div>
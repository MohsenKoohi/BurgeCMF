<div class="main">
	<div class="container">
		<h1>{modules_text}</h1>

		<div class="container">
			<?php foreach($modules_info as $mod) {?>
				<div class="row even-odd-bg" >
					<div class="three columns">
						<label>{module_id_text}</label>
						<?php echo $mod['module_id'];?>
					</div>
					<div class="three columns">
						<label>{module_name_text}</label>
						<?php echo $mod['module_name']?>
					</div>
				</div>
			<?php } ?>
		</div>

	</div>
</div>
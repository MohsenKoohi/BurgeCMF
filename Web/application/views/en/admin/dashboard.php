<div class="main">
	<div class="container dashboard">
		<h1>{dashboard_text}</h1>
		<?php foreach($modules as $md){ ?>
			<div class="four columns separated module">
				<h4>
					<a class="hov-col-changed" href="<?php echo $md['link'];?>">
						<?php echo $md['name'];?>
					</a>
				</h4>
				<p class="p-<?php echo $md['id']?>">
					<?php echo $md['text']?>
				</p>
			</div>
		<?php } ?>
	</div>
</div>

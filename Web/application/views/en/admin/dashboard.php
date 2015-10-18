<div class="main">

<style type="text/css">
.main .container .module
{
	height:200px;
	overflow: auto;
}

@media (min-width: 550px) {
	.main .container .module
	{
		width:calc(33.33% - 10px);
		margin:0 0 15px 0px;
	}

}
body.ltr .main .container .module:not(:nth-child(3n + 4))
{
	margin-right: 15px;
}


body.rtl .main .container .module:not(:nth-child(3n + 4))
{
	margin-left: 15px;
}
</style>
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

<div class="main">
	<div class="container">
		<h1>{modules_text}</h1>
		<link rel="stylesheet" type="text/css" href="{styles_url}/jquery-ui.min.css" />  
		<script src="{scripts_url}/jquery-ui.min.js"></script>
		<div class="container  category-list">
			<div class="row" >
				<?php foreach($modules_info as $mod) {?>
					<div class="three columns cat"  data-id="<?php echo $mod['module_id'];?>">
						<div class="row">
							<div class="id four columns">
								<label>{module_id_text}</label>
								<?php echo $mod['module_id'];?>
							</div>
							<div class="eight columns">
								<label>{module_name_text}</label>
								<div class="name"><a><?php echo $mod['module_name']?></a></div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>

			<?php echo form_open(get_link("admin_module"),array("id"=>"resort")); ?>
				<input type="hidden" name="post_type" value="resort"/>
				<input type="hidden" name="ids" value=""/>
			</form>
			<br><br>
			<div class="row">
				<div class="four columns">&nbsp;</div>
				<div class="button sub-primary button-type2 four columns" onclick="submitSort()">
					{submit_sort_text}
				</div>
			</div>
			<script type="text/javascript">
				$(window).load(function()
				{
					$( ".category-list > .row" ).sortable();
				})

				function submitSort()
				{
					if(!confirm("{are_you_sure_to_resort_text}"))
						return;
					
					var ids=[];
					$(".category-list .cat").each(function(index,el)
					{
						ids.push($(el).data("id"));
					});

					$("form#resort input[name=ids]").val(ids.join(','));

					$("form#resort").submit();
				}

			</script>
		</div>

	</div>
</div>
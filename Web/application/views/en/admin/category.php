<div class="main">
	<div class="container">
		<h1>{categories_text}</h1>
		<link rel="stylesheet" type="text/css" href="{styles_url}/jquery-ui.min.css" />  
		<script src="{scripts_url}/jquery-ui.min.js"></script>
		<div class="row general-buttons">
			<div class="three columns">
				<?php echo form_open(get_link("admin_category"),array()); ?>
					<input name="post_type" value="add_category" type="hidden"/>
					<input class="button button-primary sub-primary button-type1 full-width " 
						value="{add_category_text}" type="submit"/>
				</form>
			</div>
		</div>
		<br><br>
		<div class="container category-list">
			<div class="row" >
				<?php 
					foreach($categories as $cat) { 
						//don't show root category
						if(!$cat['id'])
							continue;
						$par_names=array();
						for($j=sizeof($cat['parents'])-1;$j>=0;$j--)
						{
							$par_id=$cat['parents'][$j];
							if($par_id)
								$par_names[]=$categories[$par_id]['names'][$selected_lang];
						}
						$par_names[]=" ";
						$par_name=implode($next_category_sign_text, $par_names);	
				?>				
					<div class="three columns cat" data-cat-id="<?php echo $cat['id'];?>" >
						<div>
							<div class="id">
								#<?php echo $cat['id'];?>
							</div>
							<div class="parent">
								&nbsp;<?php echo $par_name; ?>
							</div>
							<div class="name">
								<a target="_blank" href="<?php echo get_admin_category_details_link($cat['id']);?>">
									<b>
										<?php 
											if($cat['names'][$selected_lang]) 
												echo $cat['names'][$selected_lang];
											else
												echo $no_title_text;
										?>
									</b>
								</a>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
			<?php echo form_open(get_link("admin_category"),array("id"=>"resort")); ?>
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
					$( ".category-list .row" ).sortable();
				})

				function submitSort()
				{
					if(!confirm("{are_you_sure_to_resort_text}"))
						return;
					
					var ids=[];
					$(".category-list .cat").each(function(index,el)
					{
						ids.push($(el).data("cat-id"));
					});

					$("form#resort input[name=ids]").val(ids.join(','));

					$("form#resort").submit();
				}

			</script>
		</div>
	</div>
</div>
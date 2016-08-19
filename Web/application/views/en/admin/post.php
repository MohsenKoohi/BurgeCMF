<div class="main">
	<div class="container">
		<h1>{posts_text}</h1>
		<div class="row general-buttons">
			<div class="three columns">
				<?php echo form_open(get_link("admin_post"),array());?>
					<input type="hidden" name="post_type" value="add_post"/>
					<input type="submit" class="button button-primary full-width" value="{add_post_text}"/>
				</form>
			</div>
		</div>
		<br><br>
		<div class="container separated">
			<div class="row filter">
				<div class="three columns">
					<label>{title_text}</label>
					<input name="title" type="text" class="full-width" value=""/>
				</div>
				<div class="three columns half-col-margin">
					<label>{start_date_text}</label>
					<input name="post_date_ge" type="text" class="full-width ltr" value=""/>
				</div>
				<div class="three columns half-col-margin">
					<label>{end_date_text}</label>
					<input name="post_date_le" type="text" class="full-width ltr" value=""/>
				</div>
				<div class="three columns">
					<label>{category_text}</label>
					<select name="category_id" type="text" class="full-width">
						<option value="">&nbsp;</option>
						<?php
							foreach($categories as $category)
								if($category['id'])
									echo "<option value='".$category['id']."'>".$category['names'][$selected_lang]."</option>\n";
								else
									echo "<option value='".$category['id']."'>".$root_text."</option>\n";
						?>
					</select>
				</div>
				
				<div class="two columns results-search-again half-col-margin">
					<label></label>
					<input type="button" onclick="searchAgain()" value="{search_again_text}" class="full-width button-primary" />
				</div>
				
			</div>

			<div class="row results-count" >
				<div class="six columns">
					<label>
						{results_text} {posts_start} {to_text} {posts_end} - {total_results_text}: {posts_total}
					</label>
				</div>
				<div class="three columns results-page-select">
					<select class="full-width" onchange="pageChanged($(this).val());">
						<?php 
							for($i=1;$i<=$posts_total_pages;$i++)
							{
								$sel="";
								if($i == $posts_current_page)
									$sel="selected";

								echo "<option value='$i' $sel>$page_text $i</option>";
							}
						?>
					</select>
				</div>
			</div>

			<script type="text/javascript">
				var initialFilters=[];
				<?php
					foreach($filter as $key => $val)
						echo 'initialFilters["'.$key.'"]="'.$val.'";';
				?>
				var rawPageUrl="{raw_page_url}";

				$(function()
				{
					$(".filter input, .filter select").keypress(function(ev)
					{
						if(13 != ev.keyCode)
							return;

						searchAgain();
					});

					for(i in initialFilters)
						$(".filter [name='"+i+"']").val(initialFilters[i]);
				});

				function searchAgain()
				{
					document.location=getCustomerSearchUrl(getSearchConditions());
				}

				function getSearchConditions()
				{
					var conds=[];

					$(".filter input, .filter select").each(
						function(index,el)
						{
							var el=$(el);

							if(el.prop("type")=="button")
								return;

							if(el.val())
								conds[el.prop("name")]=el.val();

						}
					);
					
					return conds;
				}

				function getCustomerSearchUrl(filters)
				{
					var ret=rawPageUrl+"?";
					for(i in filters)
						ret+="&"+i+"="+encodeURIComponent(filters[i].trim().replace(/\s+/g," "));
					return ret;
				}

				function pageChanged(pageNumber)
				{
					document.location=getCustomerSearchUrl(initialFilters)+"&page="+pageNumber;
				}
			</script>
		</div>
		<br>
		<div class="container">
			<?php 
				$i=1;
				if(isset($posts_info))
					foreach($posts_info as $post)
					{ 
			?>
						<a target="_blank" href="<?php echo get_admin_post_details_link($post['post_id']);?>">
							<div class="row even-odd-bg" >
								<div class="nine columns">
									<span>
										<?php echo $post['post_id'];?>)
										<?php 
											if($post['pc_title']) 
												echo $post['pc_title'];
											else
												echo $no_title_text;
										?>
									</span>
								</div>
							</div>
						</a>
			<?php
					}
			?>
		</div>

	</div>
</div>
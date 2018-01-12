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

		<div class="tab-container">
			<ul class="tabs">
				<li><a href="#posts">{posts_text}</a></li>
				<li><a href="#comments">{comments_text}</a></li>
			</ul>
			<script type="text/javascript">
				$(function(){
				   $('ul.tabs').each(function(){
						var $active, $content, $links = $(this).find('a');
						$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
						$active.addClass('active');

						$content = $($active[0].hash);

						$links.not($active).each(function () {
						   $(this.hash).hide();
						});

						$(this).on('click', 'a', function(e){
						   $active.removeClass('active');
						   $content.hide();

						   $active = $(this);
						   $content = $(this.hash);

						   $active.addClass('active');

						   $content.show();						   	

						   e.preventDefault();
						   
						});
					});
				});
			</script>
			<div class="tab" id="posts">
					
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

			<div class='tab' id='comments'>
				<div class="container separated">
					<div class="row comments-filter">
						<div class="three columns">
							<label>{post_text}</label>
							<input name="comment_post" type="text" class="full-width" value=""/>
						</div>
						<div class="three columns half-col-margin">
							<label>{writer_name_text}</label>
							<input name="comment_writer_name" type="text" class="full-width" value=""/>
						</div>
						<div class="three columns half-col-margin">
							<label>{status_text}</label>
							<select class='full-width' name="comment_status">
								<option value=''>&nbsp;</option>
								<?php 
									foreach($comments_statuses as $s)
									{
										$sname=${"post_comment_status_".$s."_text"};
										echo "<option value='$s'>$sname</option>";
									}
								?>
							</select>
						</div>
						<div class="three columns">
							<label>IP</label>
							<input name="comment_ip" type="text" class="full-width lang-en ltr" value=""/>
						</div>
						<div class="three columns half-col-margin">
							<label>{start_date_text}</label>
							<input name="comment_date_ge" type="text" class="full-width ltr" value=""/>
						</div>
						<div class="three columns half-col-margin">
							<label>{end_date_text}</label>
							<input name="comment_date_le" type="text" class="full-width ltr" value=""/>
						</div>
						
						
						<div class="two columns results-search-again half-col-margin">
							<label></label>
							<input type="button" onclick="commentsSearchAgain()" value="{search_again_text}" class="full-width button-primary" />
						</div>
						
					</div>

					<div class="row results-count" >
						<div class="six columns">
							<label>
								{results_text} {comments_start} {to_text} {comments_end} - {total_results_text}: {comments_total}
							</label>
						</div>
						<div class="three columns results-page-select">
							<select class="full-width" onchange="commentsPageChanged($(this).val());">
								<?php 
									for($i=1;$i<=$comments_total_pages;$i++)
									{
										$sel="";
										if($i == $comments_current_page)
											$sel="selected";

										echo "<option value='$i' $sel>$page_text $i</option>";
									}
								?>
							</select>
						</div>
					</div>

					<script type="text/javascript">
						var commentsInitialFilters=[];
						<?php
							foreach($comments_filter as $key => $val)
								echo 'commentsInitialFilters["'.$key.'"]="'.$val.'";';
						?>
						//var rawPageUrl="{raw_page_url}";

						$(function()
						{
							$(".comments-filter input, .comments-filter select").keypress(function(ev)
							{
								if(13 != ev.keyCode)
									return;

								commentsSearchAgain();
							});

							for(i in commentsInitialFilters)
								$(".comments-filter [name='"+i+"']").val(commentsInitialFilters[i]);
						});

						function commentsSearchAgain()
						{
							document.location=getCommentsSearchUrl(getCommentsSearchConditions());
						}

						function getCommentsSearchConditions()
						{
							var conds=[];

							$(".comments-filter input, .comments-filter select").each(
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

						function getCommentsSearchUrl(filters)
						{
							var ret=rawPageUrl+"?";
							for(i in filters)
								ret+="&"+i+"="+encodeURIComponent(filters[i].trim().replace(/\s+/g," "));

							ret+="#comments";

							return ret;
						}

						function pageChanged(pageNumber)
						{
							document.location=getCustomerSearchUrl(initialFilters)+"&page="+pageNumber;
						}
					</script>
				</div>
				<br>
				<?php echo form_open("")?>
					<input type='hidden' name='post_type' value='edit_comments'/>
					<?php foreach($comments_info as $c){ ?>
						<div class='row even-odd-bg'>
							<input type='hidden' name='pcom_ids[]' value='<?php echo $c['pcom_id'];?>'/>
							<div class='three columns'>
								<label>{name_text}</label>
								<?php echo $c['pcom_visitor_name'];?>
							</div>


							<div class='three columns'>
								<label>IP</label>
								<span class='lang-en ip'><?php echo $c['pcom_visitor_ip'];?></span>
							</div>

							<div class='three columns'>
								<label>{date_text}</label>
								<span class='date'><?php echo $c['pcom_date'];?></span>
							</div>

							<div class='three columns'>
								<label>{status_text}</label>
								<select class='full-width' name="pcom_status[<?php echo $c['pcom_id'];?>]">
									<?php 
										foreach($comments_statuses as $s)
										{
											$sel='';
											if($s == $c['pcom_status'])
												$sel='selected';
											$sname=${"post_comment_status_".$s."_text"};
											echo "<option value='$s' $sel>$sname</option>";
										}
									?>
								</select>
							</div>

							<div class='nine columns'>
								<label>{comment_text}</label>
								<textarea class='full-width' name='pcom_text[<?php echo $c['pcom_id'];?>]' rows=4
									><?php echo $c['pcom_text'];?></textarea>
							</div>

							<div class='three columns'>
								<label>{delete_text}</label>
								<input type='checkbox' class='graphical' name="deleted_comment_ids[]" 
									value='<?php echo $c['pcom_id'];?>' />
							</div>

						</div>
					<?php } ?>

					<div class="row">
						<div class="four columns">&nbsp;</div>
						<input type="submit" class="button-primary four columns" value="{submit_text}"/>
					</div>				
				<?php echo form_close();?>

				</div>
				
			</div>
		</div>
	</div>
</div>
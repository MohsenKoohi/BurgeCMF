<div class="main">
	<div class="container">
		<h1>{contact_us_text}</h1>
		<div class="row general-buttons">
			<a href="<?php echo get_link("admin_contact_us_send_new");?>">
				<div class="two columns button sub-primary button-type1 half-col-margin">
					{send_new_message_text}
				</div>
			</a>
		</div>
		<br><br>
		<div class="container">
			<div class="container separated">
				<div class="row filter">
					<div class="three columns">
						<label>{ref_id_text}</label>
						<input name="ref_id" type="text" class="full-width ltr"/>
					</div>
					<div class="three columns half-col-margin">
						<label>{sender_text}</label>
						<input name="sender" type="text" class="full-width"/>
					</div>
					<div class="three columns half-col-margin">
						<label>{time_text}</label>
						<input name="time" type="text" class="full-width ltr"/>
					</div>
					<div class="three columns">
						<label>{subject_text}</label>
						<input name="subject" type="text" class="full-width"/>
					</div>
					<div class="three columns half-col-margin">
						<label>{status_text}</label>
						<select name="status" class="full-width">
							<option value=""></option>
							<option value="responded">{responded_text}</option>
							<option value="not_responded">{not_responded_text}</option>
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
							{results_text} {messages_start} {to_text} {messages_end} - {total_results_text}: {messages_total}
						</label>
					</div>
					<div class="three columns results-page-select">
						<select class="full-width" onchange="pageChanged($(this).val());">
							<?php 
								for($i=1;$i<=$messages_total_pages;$i++)
								{
									$sel="";
									if($i == $messages_current_page)
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

			<?php 
				$i=$messages_start;
				if($messages_total)
					foreach($messages_info as $mess) { 
			?>
				<div class="row even-odd-bg" >
					<div class="one columns counter">
						<label >#<?php echo $i++;?></label>
					</div>
					<div class="three columns">
						<label>{sender_text}</label>
						<span>
							<?php echo $mess['cu_sender_name']."<br>".$mess['cu_sender_email'];?>
						</span>
					</div>
					<div class="three columns">
						<label>{subject_text}</label>
						<span>
							<?php 
								if($mess['cu_message_department'])
									echo $mess['cu_message_department']."<br>";
								echo $mess['cu_message_subject'];
							?>
						</span>
					</div>
					<div class="three columns">
						<label>{time_text}</label>
						<span>
							{received_text}: <span style="display:inline-block" class="ltr"> <?php echo $mess['cu_message_time'];?></span><br>
							<?php if($mess['cu_response_time']) { ?>
								{response_text}: <span style="display:inline-block" class="ltr"><?php  echo $mess['cu_response_time'];?> </span>
							<?php } ?>
						</span>
					</div>
					<div class="two columns">
						<label>{details_text}</label>
						<a target="_blank" class="button button-type2 sub-primary twelve columns" href="<?php echo get_admin_contact_us_message_details_link($mess['cu_id']);?>">
							{view_text}
						</a>
					</div>
				</div>
			<?php } ?>
		</div>

	</div>
</div>
<div class="main">
	<style type="text/css">
		.row.even-odd-bg span
		{
			font-size: .8em;
			//color:#555;
		}

		.row.even-odd-bg div label
		{
			color:black;
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
			<div class="row filter">
				<div class="three columns">
					<label>{date_text}</label>
					<input name="date" type="text" class="full-width ltr" value="{current_date}"/>
				</div>
				<div class="three columns half-col-margin">
					<label>{event_text}</label>
					<select name="event" type="text" class="full-width lang-en">
						<option value=""></option>
						<?php
							foreach($event_types as $name => $index)
								echo "<option value='$name'>$name</option>\n";
						?>
					</select>
				</div>
				<div class="three columns half-col-margin">
					<label>{visitor_id_text}</label>
					<input name="visitor_id" type="text" class="full-width lang-en"/>
				</div>
				<div class="two columns results-search-again half-col-margin">
					<label></label>
					<input type="button" onclick="searchAgain()" value="{search_again_text}" class="full-width button-primary" />
				</div>
				
			</div>

			<div class="row results-count" >
				<div class="six columns">
					<label>
						{results_text} {logs_start} {to_text} {logs_end} - {total_results_text}: {logs_total}
					</label>
				</div>
				<div class="three columns results-page-select">
					<select class="full-width" onchange="pageChanged($(this).val());">
						<?php 
							for($i=1;$i<=$logs_total_pages;$i++)
							{
								$sel="";
								if($i == $logs_current_page)
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
				if($logs['total'])
					for($i=$logs['start'];$i<$logs['end'];$i++)
					{ 
						$log=$logs[$i];
			?>
						<div class="row even-odd-bg" style="display:flex;flex-wrap:wrap">
							<div class="three columns">
								<label>#<?php echo 1+$i;?></label>
							</div>
							<?php foreach ($log as $key => $value) { 
							?>
								<div class="three columns lang-en">
									<span><?php echo $key;?></span>
									<label class="lang-en" data-value-type=<?php echo $key;?>>
										<?php echo $value;?>
									</label>
								</div>
							<?php } ?>				
						</div>
			<?php 
					}
			?>
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

			$(".row.even-odd-bg div.three.columns label[data-value-type=ip]").mouseover(function(event)
			{
				var el=$(event.target);
				if(el.data('ip-queried'))
					return;

				el.data('ip-queried',1);
				url="http://ip-api.com/json/"+el.html();
				$.get(url,function(info)
				{
					var newVal=el.html()
						+"<br>"+info.country
						+"<br>"+info.city
						+"<br>"+info.isp
						+"<br>"+info.as;
					el.html(newVal);

				});

				return;
			});
		});
	</script>
</div>
<div class="main">
	<div class="container">
		<h1>{posts_text}</h1>
		<div class="tab-container">
			<ul class="tabs">
				<li><a href="#search">Search</a></li>		
				<li><a href="#details">Details</a></li>				
			</ul>
			<script type="text/javascript">
				$(function(){
					$(".tab-iframe").css("height",$(window).height()-200);
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
						 
						   setupMovingHeader();
						});
					});
				});

				function show_post_details(postId)
				{
					var post_details_link="<?php echo get_link('admin_post_details_format');?>";
					$(".tabs a[href='#details']").trigger("click").focus();
					$("#details > iframe").prop("src",post_details_link.replace("post_id",postId));

					return;
				}
			</script>

			<div class="tab" id="search" style="">
				<iframe class="tab-iframe" src="<?php echo get_link("admin_post_search");?>">
				</iframe>
			</div>

			<div class="tab" id="details">
				<iframe class="tab-iframe">
				</iframe>
			</div>
		</div>

		<div class="container">
			<div class="row even-odd-bg" >
				<div class="three columns">
					<label>{module_id_text}</label>
				</div>
				<div class="three columns">
					<label>{module_name_text}</label>
				</div>
			</div>
			
		</div>

	</div>
</div>
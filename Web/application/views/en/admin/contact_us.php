<div class="main">
	<div class="container">
		<h1>{contact_us_text}</h1>
		<div class="container">
			<?php $i=1;foreach($messages_info as $mess) { ?>
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
							{received_time_text}: <span style="display:inline-block" class="ltr"> <?php echo $mess['cu_message_time'];?></span><br>
							<?php if($mess['cu_response_time']) { ?>
								{response_time_text}: <span style="display:inline-block" class="ltr"> echo $mess['cu_response_time'];?> </span>
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
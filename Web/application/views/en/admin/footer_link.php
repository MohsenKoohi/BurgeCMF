<div class="main">
	<div class="container">
		<h1>{footer_link_text}</h1>
		<style type="text/css">
			.even-odd-bg
			{
				margin-bottom: 10px;
			}
			h2
			{
				margin-bottom: 0;
			}
		</style>		
		<div class="container">
			<?php echo form_open(""); ?>
				<input type="hidden" name="post_type" value="set_links" />				
				<?php 
					$tid=1;
					$scount=1;
					if($links)
						foreach($links[0]['children'] as $l)
						{
							$id=$tid++;
				?>
							<div class='row even-odd-bg'>
								<h2>{set_text} <?php echo $scount++;?></h2>
								<div class='row'>
									<input type='button' class='anti-float two columns button button-primary sub-primary button-type2' value='{delete_set_text}'
										onclick='$(this).parent().parent().remove();'
									/>
								</div>
								<br><br>
								<input type='hidden' value='0' name='links[<?php echo $id;?>][parent_id]'/>
								<div class='three columns'>
									{title_text}
								</div>
								<div class='nine columns'>
									<input type='text' value='<?php echo $l['title'];?>' class='full-width'
										name='links[<?php echo $id;?>][title]'
									/>
								</div>
								<div class='three columns'>
									{link_text}
								</div>
								<div class='nine columns'>
									<input type='text' value='<?php echo $l['link'];?>' class='full-width lang-en'
										name='links[<?php echo $id;?>][link]'
									/>
								</div>

								<div class='three columns'>
									{sub_links_text}
								</div>
								<div class='nine columns separated'>
									<?php
										foreach($l['children'] as $c)
										{ 
											$cid=$tid++;
									?>
											<div class='row'>
												<input type='hidden' value='<?php echo $id;?>' name='links[<?php echo $cid;?>][parent_id]'/>
												<div class='four columns'>
													<lable>{title_text}</lable>
													<input type='text' value='<?php echo $c['title'];?>' class='full-width'
														name='links[<?php echo $cid;?>][title]'
													/>
												</div>
												<div class='four columns half-col-margin'>
													<lable>{link_text}</lable>
													<input type='text' value='<?php echo $c['link'];?>' class='full-width lang-en'
														name='links[<?php echo $cid;?>][link]'
													/>
												</div>
												<div class='two columns anti-float'>
													<lable>&nbsp;</lable>
													<input type='button' class='full-width button button-primary sub-primary button-type2' value='{delete_text}'
														onclick='$(this).parent().parent().remove();'
													/>
												</div>
											</div>
									<?php 
										}
									 ?>

									<div class="row">
										<input type="button" class="button-primary sub-primary button-type1 four columns"
										 value="{add_new_row_text}" onclick='addRow(this,<?php echo $id;?>);'/>
									</div>
								</div>

							</div>
				<?php 
						}
				?>

				<div class="row">
					<input type="button" class="button-primary sub-primary button-type1 four columns"
					 value="{add_new_set_text}" onclick='addSet(this);'/>
				</div>

				<br><br><br>
				<div class="row">
					<div class="four columns">&nbsp;</div>
					<input type="submit" class="button-primary four columns" value="{submit_text}"/>
				</div>
			</form>

			<script type="text/javascript">
				var lastId=<?php echo $tid;?>;

				function addSet(el)
				{
					var html= ""
						+"<div class='row even-odd-bg'>"
							+"<h2>{set_text}</h2>"
							+"<div class='row'>"
									+"<input type='button' class='anti-float two columns button button-primary sub-primary button-type2' value='{delete_set_text}'"
										+"onclick='$(this).parent().parent().remove();'/>"
							+"</div>"
							+"<br><br>"
							+"<input type='hidden' value='0' name='links["+lastId+"][parent_id]'/>"
							+"<div class='three columns'>"
								+"{title_text}"
							+"</div>"
							+"<div class='nine columns'>"
								+"<input type='text' value='' class='full-width'"
									+"name='links["+lastId+"][title]' />"
							+"</div>"
							+"<div class='three columns'>"
								+"{link_text}"
							+"</div>"
							+"<div class='nine columns'>"
								+"<input type='text' value='' class='full-width lang-en'"
									+"name='links["+lastId+"][link]'/>"
							+"</div>"
							+"<div class='three columns'>"
								+"{sub_links_text}"
							+"</div>"
							+"<div class='nine columns separated'>"
								+"<div class='row'>"
									+"<input type='button' class='button-primary sub-primary button-type1 four columns' "
									+"value='{add_new_row_text}' onclick='addRow(this,"+lastId+");'/>"
								+"</div>"
							+"</div>"
						+"</div>";

						$(el).parent().before(html);
						lastId++;
				}

				function addRow(el, parentId)
				{
					var html=""
						+"<div class='row'>"
							+"<input type='hidden' value='"+parentId+"' name='links["+lastId+"][parent_id]'/>"
							+"<div class='four columns'>"
								+"<lable>{title_text}</lable>"
								+"<input type='text' value='' class='full-width'"
									+"name='links["+lastId+"][title]'/>"
							+"</div>"
							+"<div class='four columns half-col-margin'>"
								+"<lable>{link_text}</lable>"
								+"<input type='text' value='' class='full-width lang-en'"
									+"name='links["+lastId+"][link]' />"
							+"</div>"
							+"<div class='two columns anti-float'>"
								+"<lable>&nbsp;</lable>"
								+"<input type='button' class='full-width button button-primary sub-primary button-type2' value='{delete_text}'"
									+"onclick='$(this).parent().parent().remove();'/>"
							+"</div>"
						+"</div>";
					$(el).before(html);
					lastId++;
				}
			</script>
		</div>
	</div>
</div>
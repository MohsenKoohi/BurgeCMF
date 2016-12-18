<div class="main">
	<div class="container">
		<h1>{category_details_text} {category_id}
			<?php 
			if($info && $info[$selected_lang]['cd_name']) 
				echo $comma_text." ".$info[$selected_lang]['cd_name']
			?>
		</h1>		
		<?php 
			if(!$info) {
		?>
			<h4>{not_found_text}</h4>
		<?php 
			}else{ 
		?>
			<script src="{scripts_url}/tinymce/tinymce.min.js"></script>
			<div class="container">
				<div class="row general-buttons">
					<div class="two columns button sub-primary button-primary"
						onclick="window.open('<?php echo get_customer_category_details_link($category_id,$info[$selected_lang]['category_hash'],'');?>','_blank');"
					>
						{customer_page_text}
					</div>
				</div>
				<div class="row general-buttons">
					<div class="two columns button sub-primary button-type2" onclick="deleteCategory()">
						{delete_category_text}
					</div>
				</div>
				<div class="row general-buttons">
					<div class="two columns button sub-primary button-type1" onclick="addSubCategory()">
						{add_sub_category_text}
					</div>
				</div>
				<br><br>
				<?php echo form_open(get_admin_category_details_link($category_id),array("onsubmit"=>"return formSubmit();")); ?>
					<input type="hidden" name="post_type" value="edit_category" />
					
					<div class="tab-container">
						<ul class="tabs">
							<?php foreach($info as $inf) { ?>
								<li>
									<a href="#cd_<?php echo $inf['cd_lang_id'];?>">
										<?php echo $inf['lang']?>
									</a>
								</li>
							<?php } ?>
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
						<?php foreach($info as $lang=>$cd) {?>
							<div class="tab" id="cd_<?php echo $cd['cd_lang_id'];?>">
								<div class="container">
									<div class="row even-odd-bg" >
										<div class="three columns">
											<span>{name_text}</span>
										</div>
										<div class="nine columns">
											<input type="text" class="full-width" 
												name="<?php echo $lang;?>[cd_name]" 
												value="<?php echo $cd['cd_name']; ?>"
												onkeyup="setUrl(this,'<?php echo $lang;?>');"
											/>
										</div>
									</div>
									<div class="row even-odd-bg" >
										<div class="three columns">
											<span>{url_text}</span>
										</div>
										<div class="nine columns eng ltr">
											<div class="six columns eng" style="float:left">
												{category_url_first_part}
											</div>
											<input type="text" class="six columns eng" 
												name="<?php echo $lang;?>[cd_url]" 
												value="<?php echo $cd['cd_url']; ?>"
											/>
										</div>
									</div>
									<div class="row even-odd-bg" >
										<div class="three columns">
											<span>{description_text}</span>
										</div>
										<div class="nine columns ">
											<textarea class="full-width" rows="10"
												name="<?php echo $lang;?>[cd_description]"
											><?php echo $cd['cd_description']; ?></textarea>
										</div>
									</div>
									<div class="row even-odd-bg" >
										<div class="three columns">
											<span>{image_text}</span>
										</div>
										<div class="nine columns ">
											<div class="two columns ">
												<span>{delete_image_text}</span>
											</div>
											<div class="three columns ">
												<input id="del-img-<?php echo $lang; ?>" type="checkbox" class="graphical" onclick="deleteImage('<?php echo $lang;?>');"/>
											</div>
											<br><br>
											<div class="tweleve columns">
												<input type="hidden" name="<?php echo $lang;?>[cd_image]"
												value="<?php echo $cd['cd_image']; ?>" />
												<?php
													$image=$no_image_url;
													if($cd['cd_image'])
														$image=$cd['cd_image'];
												?>
												<img 
													id="img-<?php echo $lang; ?>"
													src="<?php echo $image; ?>"  
													style="cursor:pointer;max-height:200px;background-color:white"
													onclick="selectImage('<?php echo $lang; ?>');"
												/>
											</div>
										</div>
									</div>
									<div class="row even-odd-bg" >
										<div class="three columns">
											<span>{meta_keywords_text}</span>
										</div>
										<div class="nine columns">
											<input type="text" class="full-width" 
												name="<?php echo $lang;?>[cd_meta_keywords]" 
												value="<?php echo $cd['cd_meta_keywords']; ?>"
											/>
										</div>
									</div>
									<div class="row even-odd-bg" >
										<div class="three columns">
											<span>{meta_description_text}</span>
										</div>
										<div class="nine columns">
											<input type="text" class="full-width" 
												name="<?php echo $lang;?>[cd_meta_description]" 
												value="<?php echo $cd['cd_meta_description']; ?>"
											/>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<br>
					<div class="row even-odd-bg">
						<div class="three columns">
							<span>{parent_text}</span>
						</div>
						<div id="parent-category" class="nine columns category-div">
							<input type="hidden" name="category_parent_id" />
							<?php echo $categories; ?>
						</div>
						<script type="text/javascript">
							$("#parent-category span").click(
								function()
								{
									var id=$(this).data("id");
									$("#parent-category input[value="+id+"]").prop("checked",true);

								}
							);
						</script>
					</div>
					<div class="row even-odd-bg">
						<div class="three columns">
							<span>{hash_value_text}</span>
						</div>
						<div class="nine columns">
							<input type="text" class="ltr en full-width" name="category_hash" 
								value="<?php echo $info[$selected_lang]['category_hash']?>"
							/>
						</div>
					</div>
					<div class="row even-odd-bg">
						<div class="three columns">
							<span>{show_in_list_text}</span>
						</div>
						<div class="nine columns">
							<input type="checkbox" class="graphical" name="category_show_in_list" 
								<?php if($info[$selected_lang]['category_show_in_list']) echo 'checked';?>
							/>
						</div>
					</div>
					<div class="row even-odd-bg">
						<div class="three columns">
							<span>{hidden_text}</span>
						</div>
						<div class="nine columns">
							<input type="checkbox" class="graphical" name="category_is_hidden" 
								<?php if($info[$selected_lang]['category_is_hidden']) echo 'checked';?>
							/>
						</div>
					</div>
					<script type="text/javascript">
						var activeLang;

						function selectImage(lang)
						{
							var fileMan=$(".burgeFileMan");
							if(!fileMan.length)
								createFileMan();

							fileMan.css("display","block");
							setTimeout(function()
							{
								$(".burgeFileMan iframe")[0].focus();
							},1000);

							activeLang=lang;
						}

						function createFileMan()
						{
							var src="<?php echo get_link('admin_file_inline');?>";
							src+="?parent_function=fileSelected";
							$(document.body).append(
								"<div class='burgeFileMan' onkeypress='checkExit(event);' tabindex='1' >"
									+"<div class='bmain'>"
									+	"<div class='bheader'>File Manager"
									+		"<button class='close' onclick='closeFileMan()'>×</button>"
									+ "</div>"
									+	"<iframe src='"+src+"'></iframe>"
									+"</div>"
								+"</div>"
							);
						}

						function checkExit(event)
						{
							if(event.keyCode == 27)
								closeFileMan();	
						}

						function closeFileMan()
						{
							var fileMan=$(".burgeFileMan");
							
							fileMan.css("display","none");//.remove();	
						}

						function fileSelected(path)
						{
							$("#img-"+activeLang).prop("src",path);
							$("input[name='"+activeLang+"[cd_image]']").val(path);
							$("#del-img-"+activeLang).prop("checked",false);
							lastImages[activeLang]="";
							closeFileMan();
						}

						var lastImages=[];

						function deleteImage(lang)
						{
							if(typeof(lastImages[lang])==="undefined" || lastImages[lang]=="")
							{
								lastImages[lang]=$("#img-"+lang).prop("src");
								$("input[name='"+lang+"[cd_image]']").val("");
								$("#img-"+lang).prop("src","{no_image_url}");
							}
							else
							{
								$("input[name='"+lang+"[cd_image]']").val(lastImages[lang]);
								$("#img-"+lang).prop("src",lastImages[lang]);	
								lastImages[lang]="";
							}
						}
					</script>
					<br><br>
					<div class="row">
							<div class="four columns">&nbsp;</div>
							<input type="submit" class=" button-primary four columns" value="{submit_text}"/>
					</div>
					<script type="text/javascript">
						function setUrl(el,lang)
						{
							var val=$(el).val();
							val=val.replace(/[\s!#@\$%\^&\*><.;'"?\/\]\[\(\)\\]+/g," ").trim().replace(/\s+/g,"-");
							$("input[name='"+lang+"[cd_url]']").val(val);
						}
					</script>
				</form>

				<div style="display:none">
					<?php echo form_open(get_admin_category_details_link($category_id),array("id"=>"delete")); ?>
						<input type="hidden" name="post_type" value="delete_category"/>
						<input type="hidden" name="post_id" value="{category_id}"/>
					</form>

					<script type="text/javascript">
						$(function(){
							var parId="<?php echo $info[$selected_lang]['category_parent_id'];?>";
							$("#parent-category input[value="+parId+"]").prop("checked","checked");

							$("#parent-category input[name=category]").change(function()
							{
								$("input[name=category_parent_id]").val($(this).val());
							});

							$("#parent-category input[name=category][value="+parId+"]").trigger("change");
						});

						function formSubmit()
						{
							if(!confirm("{are_you_sure_to_submit_text}"))
								return false;

							return true;
						}

	              	function deleteCategory()
						{
							if(!confirm("{are_you_sure_to_delete_this_category_text}"))
								return;

							$("form#delete").submit();
						}

						function addSubCategory()
						{
							$("form#delete input[name=post_type]").val("add_sub_category");
							$("form#delete").submit();
						}

						$(window).load(initializeTextAreas);
						var tmTextAreas=[];
						<?php
							foreach($all_langs as $lang => $value)
								echo "\n".'tmTextAreas.push("textarea[name=\''.$lang.'[cd_description]\']");';
						?>
						var tineMCEFontFamilies=
							"Mitra= b mitra, mitra;Yagut= b yagut, yagut; Titr= b titr, titr; Zar= b zar, zar; Koodak= b koodak, koodak;"+
							+"Andale Mono=andale mono,times;"
							+"Arial=arial,helvetica,sans-serif;"
							+"Arial Black=arial black,avant garde;"
							+"Book Antiqua=book antiqua,palatino;"
							+"Comic Sans MS=comic sans ms,sans-serif;"
							+"Courier New=courier new,courier;"
							+"Georgia=georgia,palatino;"
							+"Helvetica=helvetica;"
							+"Impact=impact,chicago;"
							+"Symbol=symbol;"
							+"Tahoma=tahoma,arial,helvetica,sans-serif;"
							+"Terminal=terminal,monaco;"
							+"Times New Roman=times new roman,times;"
							+"Trebuchet MS=trebuchet ms,geneva;"
							+"Verdana=verdana,geneva;"
							+"Webdings=webdings;"
							+"Wingdings=wingdings,zapf dingbats";
						var tinyMCEPlugins="directionality textcolor link image hr emoticons2 lineheight colorpicker media table code";
						var tinyMCEToolbar=[
						   "link image media hr bold italic underline strikethrough alignleft aligncenter alignright alignjustify styleselect formatselect fontselect fontsizeselect  emoticons2",
						   "cut copy paste bullist numlist outdent indent forecolor backcolor removeformat  ltr rtl lineheightselect table code"
						];

						
						function RoxyFileBrowser(field_name, url, type, win)
						{
							var roxyFileman ="<?php echo get_link('admin_file_inline');?>";

							if (roxyFileman.indexOf("?") < 0) {     
							 roxyFileman += "?type=" + type;   
							}
							else {
							 roxyFileman += "&type=" + type;
							}
							roxyFileman += '&input=' + field_name + '&value=' + win.document.getElementById(field_name).value;
							if(tinyMCE.activeEditor.settings.language){
							 roxyFileman += '&langCode=' + tinyMCE.activeEditor.settings.language;
							}
							tinyMCE.activeEditor.windowManager.open({
							  file: roxyFileman,
							  title: 'Roxy Fileman',
							  width: 850, 
							  height: 650,
							  resizable: "yes",
							  plugins: "media",
							  inline: "yes",
							  close_previous: "no"  
							}, {     window: win,     input: field_name    });
						
							return false; 
						}

						function initializeTextAreas()
						{
							for(i in tmTextAreas)
			               tinymce.init({
									selector: tmTextAreas[i]
									,plugins: tinyMCEPlugins
									,file_browser_callback: RoxyFileBrowser
									//,width:"600"
									,height:"600"
									,convert_urls:false
									,toolbar: tinyMCEToolbar
									,font_formats:tineMCEFontFamilies
									,media_live_embeds: true
		               	});
	              	}

					</script>
				</div>
			</div>
		<?php 
			}
		?>
		
		



	</div>
</div>
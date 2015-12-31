<div class="main">
	<div class="container">
		<h1>{constants_text}</h1>

		<div class="container separated">
			<h2>{constants_list_text}</h2>		
			<?php echo form_open(get_link("admin_constant"),array()); ?>
				<input type="hidden" name="post_type" value="constants_list" />
				<?php foreach($constants as $cons) {?>
					<div class="row even-odd-bg" >
						<div class="three columns">
							<label>{name_text}</label>
							<?php echo $cons['constant_key'];?>
						</div>
						<div class="six columns">
							<label>{value_text}</label>
							<input name="value_<?php echo $cons['constant_key']?>" 
								value="<?php echo $cons['constant_value']?>" type="text" class="full-width"
								onkeypress="valueChanged(this);"
							/>
							<input name="changed_<?php echo $cons['constant_key']?>" type="checkbox" style="display:none"/>
						</div>
						<div class="three columns">
							<label>{delete_text}</label>
							<input name="delete_<?php echo $cons['constant_key']?>" type="checkbox" class="graphical" />
						</div>
					</div>
				<?php } ?>
				<br><br>
				<div class="row">
						<div class="four columns">&nbsp;</div>
						<input type="submit" class=" button-primary four columns" value="{submit_text}"/>
				</div>				
			</form>
			<script type="text/javascript">
				function valueChanged(el)
				{
					var el_cb=$("input[type='checkbox']",$(el).parent());
					el_cb.prop("checked","checked");
				}
			</script>
		</div>

		<div class="container separated">
			<h2>{add_constant_text}</h2>	
			<?php echo form_open(get_link("admin_constant"),array()); ?>
				<input type="hidden" name="post_type" value="add_constant" />	
				<div class="row even-odd-bg" >
					<div class="three columns">
						<label>{name_text}</label>
						<input type="text" name="key" class="ltr eng full-width" />
					</div>
					<div class="six columns">
						<label>{value_text}</label>
						<input type="text" name="value" class="full-width"/>
				</div>
				<div class="row"></div>
				<div class="row">
						<div class="four columns">&nbsp;</div>
						<input type="submit" class=" button-primary four columns" value="{add_text}"/>
				</div>				
			</form>
		</div>

	</div>
</div>
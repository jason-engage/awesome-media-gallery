<!-- Section Starts -->
<section class="content gallery pure-u-1">  
	<div class="upload-fields">
		<!-- Responsive Row Starts -->
		<div class="pure-g-r"> 
			<!-- Content Starts -->
			<div id="content">  
				<!-- Pure Row Starts --> 
				<div class="pure-g-r"> 
					<!-- Category Selector Starts -->
					<div class="pure-u-1"> 
						<div class="category-wrapper">
							<div class="action meta-gallery pure-u-1-4">
								<label for="gallery-all"><?php echo $langscape["Apply a gallery to all items"];?></label>
								<select class="data input-select hidden js-gallery-all media-select selectize" id="gallery" name="gallery-all"><?php
								foreach ($gallery_options as $key => $value) { ?>
		                            <option value="<?php echo $key ?>"><?php echo $value ?></option><?php
								} ?>
								
								</select>
							</div>
							
							<div class="action meta-title pure-u-1-4">
								<label for="gallery-all"><?php echo $langscape["Titles"];?></label>
								<div class="input pure-form">
									<button class="pure-button pure-button-primary" id="js-clear-titles"><?php echo $langscape["CLEAR"];?></button>
								</div>
							</div>

							<div class="action meta-desc pure-u-1-4">
								<label for="gallery-all">Apply descriptions</label>
								<div class="input pure-form">
									<input placeholder="<?php echo $langscape["Description"];?>" type="text" name="desc" id="js-upload-desc-text" value="">
									<button class="pure-button" id="js-upload-desc-button">
                                    	<i class="checkmark icon"></i> 	    
                                    </button>
								</div>
							</div>
							
							<div class="action meta-tags pure-u-1-4">
								<label for="gallery-all"><?php echo $langscape["Apply tags"];?></label>
								<div class="input pure-form">
									<input placeholder="<?php echo $langscape["Tags"];?>" data-value="" class="js-upload-all-tags" type="text" name="tags" id="tags" value="">
									<button class="pure-button" id="js-tags-button">
                                    	<i class="checkmark icon"></i> 	    
                                    </button>
								</div>
							</div>

						</div>
					</div><!-- Category Selector Ends -->

					<ul class="awesome-gallery index <?php echo $type_gallery_name; ?>"><!-- Image Loop Starts -->
					<?php 
					$extra = array(
					'before_HTML' => '',
					'uploaded_image' => '',
					'after_HTML' => ''
					);
					echo $add_image_form->render($extra); ?>
					</ul><!-- Image Loop Ends -->
					
					
				</div> <!-- Responsive Row Ends -->
			</div> <!-- Content Ends --> 

		</div>  <!-- Pure Row Ends --> 
	
	</div> 

</section> <!-- Section Ends --> 
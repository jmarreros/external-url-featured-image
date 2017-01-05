
<?php if ( ! $hasdata ): ?>


	<input id="dcmsUrl" type="url" name="dcmsUrl" placeholder="URL" value="" />
	<a id="dcmsPreview" class="button">Preview</a>

	<input id="dcmsAlt" type="text" name="dcmsAlt" placeholder="Alt text" value="" style="width:100%;display:none;">
	
	<div style="width:100%;border:1px dotted #d1d1d1;min-height: 20px;margin-top:8px;text-align: center;color:#d1d1d1;">
		<span id="dcmsNoImage">No Image</span>
		<img id="dcmsImg" src="" style="max-width: 100%;height: auto;" />
	</div>

	<a id="dcmsRemove" class="button" style="display:none;margin-top:4px;">Remove Image</a>
	
<?php else: ?>

	<input id="dcmsUrl" type="url" name="dcmsUrl" placeholder="URL" value="<?= $img ?>" style="display:none;" />
	<a id="dcmsPreview" class="button" style="display:none;">Preview</a>

	<input id="dcmsAlt" type="text" name="dcmsAlt" placeholder="Alt text" value="<?= $alt ?>" style="width:100%;">

	<div  style="width:100%;border:1px dotted #d1d1d1;min-height: 20px;margin-top:8px;text-align: center;color:#d1d1d1;">
		<span id="dcmsNoImage" style="display:none;">No Image</span>
		<img  id="dcmsImg" src="<?= $img ?>" style="max-width: 100%;height: auto;" />
	</div>

	<a id="dcmsRemove" class="button" style="margin-top:4px;">Remove Image</a>

<?php endif ?>

<script>

	jQuery(document).ready(function($){

		// Preview
		$('#dcmsPreview').click(function(e){

			e.preventDefault();
			imgUrl = $('#dcmsUrl').val();

			if ( imgUrl != '' ){

				$("<img>", {
				    src: imgUrl,
				    error: function() {alert('Error URL Image')},
				    load: function() {
				    	$('#dcmsImg').attr('src',imgUrl);
				    	$('#dcmsNoImage').hide();
				    	$('#dcmsAlt').show();
				    	$('#dcmsRemove').show();
				    	$('#dcmsUrl').hide();
				    	$('#dcmsPreview').hide();
				    }
				});

			} //-- if ''
		}); //-- click Preview


		// Remove
		$('#dcmsRemove').click(function(e){

			e.preventDefault();

			$('#dcmsImg').attr('src','');
			$('#dcmsNoImage').show();
	    	$('#dcmsAlt').hide().val('');
	    	$('#dcmsRemove').hide();
	    	$('#dcmsUrl').show().val('');
	    	$('#dcmsPreview').show();

		}); //-- click Remove


	});

</script>
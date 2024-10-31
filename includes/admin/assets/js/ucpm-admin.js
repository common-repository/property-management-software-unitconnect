/*
 * Property Management Software by UnitConnect
 * https://wordpress.com/plugins/ucpm/
 */
(function($){
	
	/*
	 * Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
	 * Further modified from PippinsPlugins https://gist.github.com/pippinsplugins/29bebb740e09e395dc06
	 */
	jQuery(document).ready(function($) {
		// Uploading files
		var file_frame;

		jQuery('.ucpm_wpmu_button').on('click', function(event) {

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if (file_frame) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: jQuery(this).data('uploader_title'),
				button: {
					text: jQuery(this).data('uploader_button_text'),
				},
				multiple: false // Set to true to allow multiple files to be selected
			});

			// When an image is selected, run a callback.
			file_frame.on('select', function() {
				// We set multiple to false so only get one image from the uploader
				attachment = file_frame.state().get('selection').first().toJSON();

				// Do something with attachment.id and/or attachment.url here
				// write the selected image url to the value of the #ucpm_wp_meta text field
				jQuery('#ucpm_meta').val('');
				jQuery('#ucpm_upload_meta').val(attachment.url);
				jQuery('#ucpm_upload_edit_meta').val('/wp-admin/post.php?post=' + attachment.id + '&action=edit&image-editor');
				jQuery('.ucpm-current-img').attr('src', attachment.url).removeClass('placeholder');
			});

			// Finally, open the modal
			file_frame.open();
		});

		// Toggle Image Type
		jQuery('input[name=img_option]').on('click', function(event) {
			
			var imgOption = jQuery(this).val();
			if (imgOption == 'external') {
				jQuery('#ucpm_upload').hide();
				jQuery('#ucpm_external').show();
			} else if (imgOption == 'upload') {
				jQuery('#ucpm_external').hide();
				jQuery('#ucpm_upload').show();
			}

		});

		if ('' !== jQuery('#ucpm_meta').val()) {
			jQuery('#external_option').attr('checked', 'checked');
			jQuery('#ucpm_external').show();
			jQuery('#ucpm_upload').hide();
		} else {
			jQuery('#upload_option').attr('checked', 'checked');
		}

		// Update hidden field meta when external option url is entered
		jQuery('#ucpm_meta').blur(function(event) {
			if ('' !== $(this).val()) {
				jQuery('#ucpm_upload_meta').val('');
				jQuery('.ucpm-current-img').attr('src', $(this).val()).removeClass('placeholder');
			}
		});

		jQuery('.remove_img').on('click', function(event) {
			var placeholder = jQuery('#ucpm_placeholder_meta').val();
			jQuery(this).parent().fadeOut('fast', function() {
				jQuery(this).remove();
				jQuery('.ucpm-current-img').addClass('placeholder').attr('src', placeholder);
			});
			jQuery('#ucpm_upload_meta, #ucpm_upload_edit_meta, #ucpm_meta').val('');
		});

	});

	jQuery(document).on('click', '.mts-realestate-notice-dismiss', function(e){
		e.preventDefault();
		jQuery(this).parent().remove();
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: 'mts_dismiss_realestate_notice',
				dismiss: jQuery(this).data('ignore')
			}
		});
		return false;
	});


	// import csv 
	$('body').on('click', '#ucpm-import-btn', function(){
		$('#ucpm-import input').trigger('click');
	});

	$('#ucpm-import input').on('change', function(){
		var $input = $(this),
			val = $input.val().toLowerCase();
			
        if ( val.length ) {
	        var regex = new RegExp("(.*?)\.(csv)$"),
	            $btn = $('#ucpm-import-btn'),
	            success_msg = '<p class="ucpm-finished">Import successfully finished, please refresh the page.</p>', 
	            loader = ' <img src="/wp-admin/images/wpspin_light.gif" class="ucpm-loader">';

	        $('body').find('.ucpm-finished').remove();
	        
	        if (!(regex.test(val))) {
	            $(this).val('');
	            alert('Please select correct file format');
	        }

	        var fd = new FormData();
	            var files = $input[0].files[0];
	        
	        fd.append('file', files);

	        $btn.after(loader);

	        $.ajax({
	            url: ajaxurl + '?action=ucpm_import_csv',
	            type: 'post',
	            data: fd,
	            contentType: false,
	            processData: false,
	            success: function(response){
	            	console.log(response);
	            	$('body').find('.ucpm-loader').remove();
	            	$btn.after(success_msg);
	            	$input.val('');
	            },
	        });
        }
	});

})(jQuery);
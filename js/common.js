$(document).ready(function() {

	// set colorpicker
	$('#cp_text, #cp_bg').colorpicker();

	// set default variable
	var btn_text_normal = 'Generate!';
	var btn_text_processing = 'Generating...';
	var btn_text = $('#btn_status');
	var btn_loading = $('#btn_loading');
	var btn_generate = $('#bc_generate');

	// set default value(s)
	btn_text.text(btn_text_normal);

	// ajax configuration
	$.ajaxSetup({
		url: 'ajax.php',
		type: 'POST',
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		beforeSend: function(xhr) {
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
	});

	$('#bc_copyright_color_switch').change( function() {
		if( $(this).is(':checked') )
		{
			$('#bc_colorpicker_box').slideUp();
			$('#bc_copyright_color').val('0');

		}
		else
		{
			$('#bc_colorpicker_box').slideDown();
			$('#bc_copyright_color').val('1');
		}
	});

	$('#bc_serial, #bc_bulk_data_rows').blur(function() {
		if ( $(this).val() !== '' && $(this).hasClass('is-invalid') )
			$(this).removeClass('is-invalid');
	});


	$('#bc_generate').click(function() {

		$('#bc_serial').attr('required', true);
		$('#bc_bulk_data').attr('required', true);

		if ( $('a#nav-bulk-tab').hasClass('active') )
		{
			$('#bc_serial').attr('required', false);
			if ( $('#bc_bulk_data').val() !== '' )
			{
				$('#bc_generator_form').submit();
			}
			else
			{
				// e.preventDefault();
				$('#bc_bulk_data').focus();
			}
		}
		else
		{
			$('#bc_bulk_data').attr('required', false);
			if ( $('#bc_serial').val() == '' )
			{
				$('#bc_serial').focus();
			}
			else
			{
				// change ui elements
				btn_generate.attr('disabled', true);
				btn_text.text(btn_text_processing);
				btn_loading.show();

				// prepare data
				var formData = $(this).closest('form').serializeArray();
				formData.push({ name: this.name, value: this.value });

				// send ajax request
				$.ajax({
					data: formData,
					success: function(response) {
						if ( response.error == false )
							$('img#bc_image_result').attr('src', response.result);
						else
							alert(response.result);
					},
					complete: function() {
						// reset ui elements
						btn_generate.prop('disabled', false);
						btn_text.text(btn_text_normal);
						btn_loading.hide();
					}
				});
			}
		}

	});

});
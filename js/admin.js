jQuery(document).ready(function($){
		$(".salavat-codes input[type='text']").on("click", function () {
			$(this).select();
		});

		$('#salavat_view_method').on('change', function(event) {
			/* Act on the event */
			$(this).siblings('.salavatcounter-hide-me').hide();
			
			if ( $(this).val() == 'widget'){
                $('#image_show').parent('span').eq(0).slideDown();
                $('#image_url').parent('span').eq(0).slideDown();
			}

			if ( $(this).val() == 'corner'){
				$('#salavat_position').slideDown();
			}
		});


    $('#salavatcounter-load-wp-uploader').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#image_url').val(image_url);
        });
    });

    $('#new-salavat-submit').on('click' , function(e){
    	e.preventDefault();
    	var salavat_title = $('#salavat_title');
    	if($.trim(salavat_title.val()) == ''){

			salavat_title.animate({width:'toggle'},80);
    		salavat_title.slideUp('fast', function() {
    			$(this).slideDown('fast');
    		});
    		return false;
    	}else{
    		$('#new-salavat-submit-form').submit();
    	}
    });

    $('.salavatcounter-view-on-my-site').on('change' , function(){
    	var _this_ajx_ldr = $(this).siblings('.salavat-ajax-loader').eq(0);
    	_this_ajx_ldr.fadeIn();
    	var chkd = 0;
    	if($(this).is(":checked")){
    		chkd = 1;
    	}

    	$.ajax({
    		url: ajaxurl,
    		type: 'POST',
    		dataType: 'json',
    		data: {action: 'salavatcounter_admin_on_corner' , id:$(this).data('id') , chkd:chkd},
    	})
    	.done(function() {
    		console.log("success");
    		_this_ajx_ldr.fadeOut();
    	})
    	.fail(function(data) {
    		alert(data.msg);
    		console.log("error");
    		_this_ajx_ldr.fadeOut();
    	})
    	.always(function() {
    		console.log("complete");
    		_this_ajx_ldr.fadeOut();
    	});
    	

    });

    $('.confirmdelete').on('click' , function(e){
    	e.preventDefault();
    	if(confirm("Delete?")){
    		window.location.href=$(this).attr('href');
    	}
    });
});
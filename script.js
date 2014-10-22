jQuery(function($){
	var scss = $('.sc-say-salavat'),
	sc_padding_count = $('.sc-padding-count');
	scss.click(function(){
		var __this= $(this);
		__this.find('img').css('display' , 'inline');
		$.ajax({
				url: sc_ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {action:'say_salavat'},
				success: function (data) {
					__this.find('img').css('display' , 'none');
					if(data.ok == 'OK'){
						sc_padding_count.hide(105).text(parseInt(data.counter)).show(110);
					}else{
						alert(data.msg);
					}
				}
			});
	});
})
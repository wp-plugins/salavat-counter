jQuery(document).ready(function($){
	$('body').on('click', '.sc-say-salavat', function () {
		var __this = $(this);
		var sc_padding_count = __this.parents('div.salavat-counter').eq(0).find('.sc-padding-count');
		__this.find('img').css('display', 'inline');
		__this.attr('disabled', 'disabled');
		$.ajax({
			url : salavatcounter.ajaxurl,
			type : 'get',
			dataType : 'jsonp',
			data : {action : 'salavatcounter' , id:$(this).data('id')},
			success : function (data) {
				__this.find('img').css('display', 'none');
				__this.removeAttr('disabled');
				if (data.ok == 'OK') {
					sc_padding_count.hide(105).text(parseInt(data.counter)).show(110);
				} else {
					alert(data.msg);
				}
			},
			fail : function () {
				__this.find('img').css('display', 'none');
				__this.removeAttr('disabled');
				alert('Ajax error.')
			}
		});
	});
});
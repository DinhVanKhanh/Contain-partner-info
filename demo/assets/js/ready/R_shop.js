$(document).ready(function() {
	bindInputCodeData('txtStCode');
	$('#mnShops').css('color', '#cc3300');

	//load list shop type
	loadShopList();

	$('#submit_del').click(function() {
		deleteShop();
	});
	
	$('#submit_del_ok').click(function () {
		$.fancybox.close();
		setTimeout(() => {
			$('.message').html('販売店を削除してよろしいでしょうか？');
			$('#submit_del_ok').hide();
			$('#submit_del').show();
			$('.btnClose').show();
		}, 500);
		loadShopList();
	});
});
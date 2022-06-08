$(document).ready(function() {
	$('#mnBanner').css('color', '#cc3300');

	// Load list banners
	loadBannerList();

	// Delete list banners
	$('#submit_del').click(function() {
		deleteBanner();
	});
	
	$('#submit_del_ok').click(function () {
		$.fancybox.close();
		setTimeout(() => {
			$('.message').html('このバナーを削除してよろしいでしょうか？');
			$('#submit_del_ok').hide();
			$('#submit_del').show();
			$('.btnClose').show();
		}, 500);
		loadBannerList();
	});
});
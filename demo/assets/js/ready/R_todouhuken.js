$(document).ready(function() {
	bindInputCodeData('txtStCode');
	$('#mnTodouhuken').css('color', '#cc3300');

	// Load list todouhuken
	loadTodouhukenList();

	$('#submit_del').click(function() {
		deleteTodouhuken();
	});
	
	$('#submit_del_ok').click(function () {
		$.fancybox.close();
		setTimeout(() => {
			$('.message').html('都市コードを削除してよろしいでしょうか？');
			$('#submit_del_ok').hide();
			$('#submit_del').show();
			$('.btnClose').show();
		}, 500);
		loadTodouhukenList();
	});
});
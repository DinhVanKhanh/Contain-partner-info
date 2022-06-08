$(document).ready(function() {
	$('#mnItems').css('color', '#cc3300');
	bindInputCodeData('ItemCode');

	// Load list Item
	loadItemsList();
	
	$('#submit_del').click(function() {
		deleteItem();
	});
	
	$('#submit_del_ok').click(function () {
		$.fancybox.close();
		setTimeout(() => {
			$('.message').html('この種類を削除してもよろしいですか？');
			$('#submit_del_ok').hide();
			$('#submit_del').show();
			$('.btnClose').show();
		}, 500);
		loadItemsList();
	});
});
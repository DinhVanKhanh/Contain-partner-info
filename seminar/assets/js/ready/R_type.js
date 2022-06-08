$(document).ready(function() {
	$('#mnTypes').css('color', '#cc3300');
	bindInputCodeData('TypesName');

    // Load list Types
    loadTypesList();

    $('#submit_del').click(function() {
		deleteTypes();
	});
	
	$('#submit_del_ok').click(function () {
		$.fancybox.close();
		setTimeout(() => {
			$('.message').html('このユーザー管理を削除してもよろしいですか？');
			$('#submit_del_ok').hide();
			$('#submit_del').show();
			$('.btnClose').show();
		}, 500);
		loadTypesList();
	});
});
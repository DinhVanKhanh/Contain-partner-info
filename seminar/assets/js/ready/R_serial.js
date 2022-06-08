$(document).ready(function () {
    $('#mnSerials').css('color', '#cc3300');
    bindInputCodeData('SeminarName');
    bindInputCodeData('CompanyName');
    bindInputCodeData('VenueName');
    bindInputContact("SerialNumber", contact);
    $('.fancyboxSC').fancybox({
        width: 900,
        height: 460
    });

    // Load list Serial
    loadSeminarSeriallist();

    $('#submit_del').click(function() {
		deleteSeminarSerial();
	});
	
	$('#submit_del_ok').click(function () {
		$.fancybox.close();
		setTimeout(() => {
			$('.message').html('セミナーを削除してよろしいでしょうか？');
			$('#submit_del_ok').hide();
			$('#submit_del').show();
			$('.btnClose').show();
		}, 500);
		loadSeminarSeriallist();
	});
});
$(document).ready(function() {
	bindInputCodeData('txtMtCode');
	
	$('#mnMeetingPlace').css('color','#cc3300');

	$("#txtMtTel").keydown(function(e) {
		// Allow: backspace, delete, tab, escape, enter, ctrl+A and .
		let ctrlKey = 17, vKey = 86, cKey = 67;
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173,109]) !== -1
			// Allow: Ctrl+A
		|| (e.keyCode == 65 && e.ctrlKey === true)
			// Allow: home, end, left, right
		|| (e.keyCode >= 35 && e.keyCode <= 39)
		// Allow: number 0-9: numpad
		|| (e.keyCode >= 96 && e.keyCode <= 105)
		// Allow: ctrl c, ctrlv, ctrl a
		|| ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true))
		// Allow: number 0-9 main key
		|| (e.keyCode >=48 && e.keyCode <= 57)) {
			//do nothing
		}
		else{
			e.preventDefault();	
		}
	});

	$("#txtMtPos").keydown(function(e) {
		// Allow: backspace, delete, tab, escape, enter, ctrl+A and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173,109]) !== -1
			// Allow: Ctrl+A
		|| (e.keyCode == 65 && e.ctrlKey === true)
			// Allow: home, end, left, right
		|| (e.keyCode >= 35 && e.keyCode <= 39)
		// Allow: number 0-9: numpad
		|| (e.keyCode >= 96 && e.keyCode <= 105)
		// Allow: ctrl c, ctrlv, ctrl a
		|| ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true))
		// Allow: number 0-9 main key
		|| (e.keyCode >=48 && e.keyCode <= 57)) {
			//do nothing
		}
		else{
			e.preventDefault();	
		}
	});
	
	$("#txtMtFax").keydown(function(e) {
		// Allow: backspace, delete, tab, escape, enter, ctrl+A and .
		let ctrlKey = 17, vKey = 86, cKey = 67;
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173,109]) !== -1
			// Allow: Ctrl+A
		|| (e.keyCode == 65 && e.ctrlKey === true)
			// Allow: home, end, left, right
		|| (e.keyCode >= 35 && e.keyCode <= 39)
		// Allow: number 0-9: numpad
		|| (e.keyCode >= 96 && e.keyCode <= 105)
		// Allow: ctrl c, ctrlv, ctrl a
		|| ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true))
		// Allow: number 0-9 main key
		|| (e.keyCode >=48 && e.keyCode <= 57)) {
			//do nothing
		}
		else{
			e.preventDefault();	
		}
	});
	
	// Load list meeting places
	loadMeetingPlacesList();

	$('#submit_del').click(function () {
		deleteMeetingPlaces();
	});
	
	$('#submit_del_ok').click(function () {
		$.fancybox.close();
		setTimeout(() => {
			$('.message').html('会場を削除してよろしいでしょうか？');
			$('#submit_del_ok').hide();
			$('#submit_del').show();
			$('.btnClose').show();
		}, 500);
		//loadMeetingPlacesList();
		filterMeetingPlaceByArea();
	});
});
$(document).ready(function() {
	$('#mnLogin').css('color', '#cc3300');
	showList();

	$(".num_alphabet").on("keydown", function(event) {
		// Ignore controls such as backspace
		let arr = [8, 16, 17, 20, 35, 36, 37, 38, 39, 40, 45, 46];
		// Allow letters
		for (let i = 65; i <= 90; i++) {
			arr.push(i);
		}
		for (let i = 96; i <= 105; i++) {
			arr.push(i);
		}
		for (let i = 48; i <= 57; i++) {
			arr.push(i);
		}

		if (jQuery.inArray(event.which, arr) === -1) {
			event.preventDefault();
		}
	});

	$(".num_alphabet").on("input", function() {
		let regexp = /[^a-zA-Z0-9_]/g;
		if ($(this).val().match(regexp)) {
			$(this).val($(this).val().replace(regexp, ''));
		}
	});

	$('#submit_del').click(function() {
		deleteUser();
	});

	$('#submit_del_ok').click(function () {
		$.fancybox.close();
		setTimeout(() => {
			$('.message').html('ユーザーを削除してよろしいでしょうか？');
			$('#submit_del_ok').hide();
			$('#submit_del').show();
			$('.btnClose').show();
		}, 500);
		showList();
	});

	$("#username").keydown(function(e) {
		// Allow: backspace, delete, tab, escape, enter, ctrl+A and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173, 109]) !== -1
		// Allow: Ctrl+A
		|| (e.keyCode == 65 && e.ctrlKey === true)
		// Allow: home, end, left, right
		|| (e.keyCode >= 35 && e.keyCode <= 39)
		// Allow: number 0-9
		|| (e.keyCode >= 96 && e.keyCode <= 105)

		|| (e.keyCode >= 48 && e.keyCode <= 57)

		|| (e.keyCode >= 65 && e.keyCode <= 90)) {

		}
		else {
			e.preventDefault();
			$('#username').val() == '';
		}
	});

	$('#cPass1').click(function () {
		$(this).hide();
		$('#cPass2').parent().show();
		$('#change').val(1);
	});

	$('#cPass2').click(function () {
		$(this).parent().hide();
		$('#cPass1').show();
		$('#change').val(0);
	});

	function showList() {
		if ( $('#confirmBox').length ) {
			loadUserList();
		}
		else {
			loadUserPersonal();
		}
	}
});
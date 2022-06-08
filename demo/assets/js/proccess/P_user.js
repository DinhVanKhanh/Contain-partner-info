const _url_init = 'src/index.php';
const _controller = 'user';

// Load list user
function loadUserList() {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'loadList',
		},
		beforeSend: function () {
			$('#scLoading').show();
		},
		success: function (data) {
			$('#tableContent').html(data.view);
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('#scLoading').hide();
		}
	});
}

// New user
function addUser( code, name, password ) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'add',
			code: code,
			name: name,
			password: encrypt( password )
		},
		beforeSend: function () {
			$('.dialogLoading').show();
		},
		success: function (data) {
			if (typeof data.errMsg != "undefined") {
				$('.error_inline0').html(data.errMsg);
				return;
			}

			$.fancybox.close();
			loadUserList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Edit user
function editUser ( code, name, password, id, isPwChange ) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'edit',
			code: code,
			name: name,
			password: encrypt( password ),
			isPwChange: isPwChange,
			id: Number( id )
		},
		beforeSend: function () {
			$('.dialogLoading').show();
		},
		success: function (data) {
			if (typeof data.errMsg != "undefined") {
				$('.error_inline0').html(data.errMsg);
				return;
			}

			$.fancybox.close();
			loadUserList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Save User
function saveUser() {
	let partern = /^(\s+|)$/;
	if ( partern.test( $('#username').val() ) ) {
		$('.error_inline0').html('ユーザーIDは未入力です。');
		$('#username').focus();
		return;
	}

	if ( partern.test( $('#fullname').val() ) ) {
		$('.error_inline0').html('ユーザー名は未入力です。');
		$('#fullname').focus();
		return;
	}

	let mode = Number( $('#isEdit').val() );
	let change = Number( $('#change').val() );
	if ( mode == 0 || (mode == 1 && change == 1) ) {
		if ( partern.test( $('#pwd1').val() ) ) {
			$('.error_inline0').html('パスワードは未入力です。');
			$('#pwd1').focus();
			return;
		}

		if ( $('#pwd1').val().length > 12 ) {
			if (change == 1 || mode == 0) {
				$('.error_inline0').html('パスワードを12文字以内で入力してください。');
				$('#pwd1').focus();
				return;
			}
		}

		if ( partern.test( $('#pwd2').val() ) ) {
			$('.error_inline0').html('パスワード確認は未入力です。');
			$('#pwd2').focus();
			return;
		}

		if ($('#pwd2').val().length > 12) {
			$('.error_inline0').html('パスワード確認を12文字以内で入力してください。');
			$('#pwd2').focus();
			return;
		}

		if ( $('#pwd1').val() != $('#pwd2').val() ) {
			$('.error_inline0').html('パスワードとパスワード確認が違います。');
			$('#pwd1').focus();
			return;
		}

		if ( $('#username').val().length > 12 ) {
			$('.error_inline0').html('ユーザーIDを６文字以内で入力してください。');
			$('#username').focus();
			return;
		}

		if ( $('#fullname').val().length > 12 ) {
			$('.error_inline0').html('ユーザー名を12文字以内で入力してください。');
			$('#fullname').focus();
			return;
		}
	}

	if ( mode == 1 ) {
		editUser(
			$('#username').val(),
			$('#fullname').val(),
			$('#pwd1').val(),
			$('#userId').val(),
			change
		);
	}
	else {
		addUser(
			$('#username').val(),
			$('#fullname').val(),
			$('#pwd1').val()
		);
	}
}

// Open dialog
function openDialog(userId, isEdit) {
	$('#username').val('');
	$('#fullname').val('');
	$('#pwd1').val('');
	$('#pwd2').val('');
	$('.error_inline0').html('');
	$('.dialogLoading').hide();

	if ( Number( isEdit ) == 1 ) {
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: _url_init,
			data: {
				controller: _controller,
				action: 'loadById',
				userId: Number( userId )
			},
			success: function (data) {
				$('#username').val(data.UserCd);
				$('#fullname').val(data.UserName);
				$('#hidePw1').val(data.Password);
			},
			error: function (xhr, textStatus, errorThrown) {
				$('.error_inline0').html('サーバーへの接続のエラーであります。');
			}
		});

		$('#username').attr('readonly', true);
		$('#username').css('color', '#676767');
		$('#username').css('background', '#F2F2F2');
		$('#isEdit').val(1);
		$('#change').val(1);
		$('#userId').val(userId);
	}
	else {
		$('#username').attr('readonly', false);
		$('#username').css('color', '#000');
		$('#username').css('background', 'rgba(0, 0, 0, 0) -moz-linear-gradient(center top , #fff 30%, #efefef 70%) repeat scroll 0 0');
		$('#isEdit').val(0);
		$('#change').val(0);
		$('#userId').val('');
	}
}

// Delete user
function deleteUser() {
	jQuery.ajax({
		type: 'POST',
		dataType: 'text',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'delete',
			userId: Number( $('#userId').val() )
		},
		success: function (data) {
			$('#submit_del_ok').show();
			$('#submit_del').hide();
			$('.btnClose').hide();
			$('.message').html(data);
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		}
	});
}

// Export User to csv
function exportCSV() {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'exportCSV'
		},
		beforeSend: function () {
			$('#scLoading').show();
		},
		success: function (data) {
			if ( typeof data.empty != "undefined" ) {
				$('.error_inline0').html(data.empty);
				return;
			}
			location.href = data.fileUrl;
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('#scLoading').hide();
		}
	});
}
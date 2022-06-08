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

// Load info personnal
function loadUserPersonal() {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'loadInfoPersonal',
		},
		beforeSend: function () {
			$('#scLoading').show();
		},
		success: function (data) {
			let role = ['admin', 'user'];
			let content = '<table id="tblResult_u" class="show_ban">' +
						'<tr>' +
							'<th class="w5">No.</th>' +
							'<th class="w14">ユーザーID</th>' +
							'<th>ユーザー名</th>' +
							'<th class="w_td_btn">操作</th>' +
						'</tr>' +
						'<tr>' +
							'<td class="center areaIndex">1</td>' +
							'<td class="center areaCode">' + data.UserCd + '</td>' +
							'<td class="areaName">' + data.UserName + '</td>' +
							'<td class="center areaBtn padd_10">' +
								'<button name="btnEdit" onclick=openDialog2(this.id) class="btnEdit fancybox4" id="' + data.UserId + '" value="' + data.UserId + '" href="#inline0">修正</button>' +
							'</td>' +
						'</tr>' +
						'</table>';
			$('#tableContent').html(content);
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
function addUser( code, name, password, role) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'add',
			code: code,
			name: name,
			password: encrypt( password ),
			role: role
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
function editUser( name, password, id, isPwChange, role ) {
	let form = new Object();
	form.controller = _controller;
	form.action = 'edit';
	form.name = name;
	form.password = encrypt( password );
	form.isPwChange = isPwChange;
	form.id = Number(id);
	if ( role != null ) {
		form.role = Number(role);
	}

	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: form,
		beforeSend: function () {
			$('.dialogLoading').show();
		},
		success: function (data) {
			if (typeof data.errMsg != "undefined") {
				$('.error_inline0').html(data.errMsg);
				return;
			}

			$.fancybox.close();
			if ( role != null ) {
				loadUserList();
			}
			else {
				loadUserPersonal();
			}
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
	let partern = /^\s*$/;
	if ( partern.test( $('#username').val() ) ) {
		$('.error_inline0').html('ユーザーIDは未入力です。');
		$('#username').focus();
		return;
	}

	if ( $('#username').val().length > 12 ) {
		$('.error_inline0').html('ユーザーIDを６文字以内で入力してください。');
		$('#username').focus();
		return;
	}

	if ( partern.test( $('#fullname').val() ) ) {
		$('.error_inline0').html('ユーザー名は未入力です。');
		$('#fullname').focus();
		return;
	}

	if ( $('#fullname').val().length > 12 ) {
		$('.error_inline0').html('ユーザー名を12文字以内で入力してください。');
		$('#fullname').focus();
		return;
	}

	let mode = Number( $('#isEdit').val() );
	let change = Number( $('#change').val() );
	let pwd1 = $('.pass-add input[name="pwd1"]').val();
	let pwd2 = $('.pass-edit input[name="pwd1"]').val();
	if ( mode == 0 ) {
		if ( partern.test( pwd1 ) ) {
			$('.error_inline0').html('パスワードは未入力です。');
			$('.pass-add input[name="pwd1"]').focus();
			return;
		}

		if ( pwd1.length > 12 ) {
			$('.error_inline0').html('パスワードを12文字以内で入力してください。');
			$('.pass-add input[name="pwd1"]').focus();
			return;
		}

		addUser(
			$('#username').val(),
			$('#fullname').val(),
			pwd1,
			$('#role').val()
		);
	}
	if ( mode == 1 ) {
		if ( change == 1 ) {
			if ( partern.test( pwd2 ) ) {
				$('.error_inline0').html('パスワードは未入力です。');
				$('.pass-edit input[name="pwd1"]').focus();
				return;
			}

			if ( pwd2.length > 12 ) {
				$('.error_inline0').html('パスワードを12文字以内で入力してください。');
				$('.pass-edit input[name="pwd1"]').focus();
				return;
			}
		}

		editUser(
			$('#fullname').val(),
			pwd2,
			$('#userId').val(),
			change,
			$('#role').val() == undefined ? null : $('#role').val(),
		);
	}
}

// Open dialog
function openDialog(userId, isEdit) {
	$('#username').val('');
	$('#fullname').val('');
	$('.pass-add input[name="pwd1"]').val('');
	$('.pass-edit input[name="pwd1"]').val('');
	$('.error_inline0').html('');
	$('.dialogLoading').hide();
	$('#change').val(0);
	$('#cPass2').parent().hide();
	$('#cPass1').show();
	$('#role').val(0);
	$('.pass-edit, .pass-add').hide();

	if ( Number( isEdit ) == 1 ) {
		$('.pass-edit').show();
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
				$('#role').val(data.KengenKbn);
			},
			error: function (xhr, textStatus, errorThrown) {
				$('.error_inline0').html('サーバーへの接続のエラーであります。');
			}
		});

		$('#username').attr('readonly', true);
		$('#username').css('color', '#676767');
		$('#username').css('background', '#F2F2F2');
		$('#isEdit').val(1);
		$('#userId').val(userId);
	}
	else {
		$('.pass-add').show();
		$('#username').attr('readonly', false);
		$('#username').css('color', '#000');
		$('#username').css('background', '#ffffff');
		$('#isEdit').val(0);
		$('#userId').val('');
	}
}

// Open dialog for personal
function openDialog2(userId) {
	$('#username').val('');
	$('#fullname').val('');
	$('.pass-add input[name="pwd1"]').val('');
	$('.pass-edit input[name="pwd1"]').val('');
	$('.error_inline0').html('');
	$('.dialogLoading').hide();
	$('#change').val(0);
	$('#cPass2').parent().hide();
	$('#cPass1').show();
	$('.pass-edit, .pass-add').hide();
	$('.show_parent table tr:first-child').hide();
	$('.pass-edit').show();
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
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		}
	});

	$('#username').attr('readonly', true);
	$('#username').css('color', '#676767');
	$('#username').css('background', '#F2F2F2');
	$('#isEdit').val(1);
	$('#userId').val(userId);
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
const _url_init = 'src/index.php';
const _controller = 'shop';

// Load list shop
function loadShopList() {
	jQuery.ajax({
		url: _url_init,
		type: 'POST',
		dataType: 'json',
		data: {
			controller: _controller,
			action: 'loadList'
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

// New shop
function addShop( code, name, IsSpecial, Descript ) {
	jQuery.ajax({
		url: _url_init,
		type: 'POST',
		dataType: 'json',
		data: {
			controller: _controller,
			action: 'add',
			code: code,
			name: name,
			IsSpecial: IsSpecial,
			Descript: Descript
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
			loadShopList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Edit shop
function editShop( code, name, IsSpecial, Descript, id ) {
	jQuery.ajax({
		url: _url_init,
		type: 'POST',
		dataType: 'json',
		data: {
			controller: _controller,
			action: 'edit',
			code: code,
			name: name,
			IsSpecial: IsSpecial,
			Descript: Descript,
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
			loadShopList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Save shop
function saveShop() {
	$('.error_inline0').html('');

	let partern = /^(\s+|)$/;
	if ( partern.test( $('#txtStCode').val() ) ) {
		$('.error_inline0').html('販売店コードは未入力です。');
		$('#txtStCode').focus();
		return;
	}
	else if ( $('#txtStCode').val().length > 30 ) {
		$('.error_inline0').html('販売店コードを30文字以内で入力してください。');
		$('#txtStCode').focus();
		return;
	}
	else if ( partern.test( $('#txtStName').val() ) ) {
		$('.error_inline0').html('販売店名は未入力です。');
		$('#txtStName').focus();
		return;
	}
	else if ( $('#txtStName').val().length > 50 ) {
		$('.error_inline0').html('販売店名を50文字以内で入力してください。');
		$('#txtStName').focus();
		return;
	}

	if ( Number( $('#isEdit').val() ) == 1 ) {
		editShop( 
			$('#txtStCode').val(),
			$('#txtStName').val(),
			$('#ckSpecial').is(":checked") ? 1 : 0,
			$('#txtStDescript').val(),
			Number( $('#shopTypeId').val() )
		);
	}
	else {
		addShop( 
			$('#txtStCode').val(),
			$('#txtStName').val(),
			$('#ckSpecial').is(":checked") ? 1 : 0,
			$('#txtStDescript').val(),
		);
	}
}

// Open dialog
function openDialog(shopId, isEdit) {
	$("#ckSpecial").prop('checked', true);
	$('#txtStCode').val('');
	$('#txtStName').val('');
	$('#txtStDescript').val('');
	$('.error_inline0').html('');
	$('.dialogLoading').hide();

	if ( Number( isEdit ) == 1 ) {
		if ( /^(\s+|\D)$/.test(shopId) ) {
			$('.error_inline0').html('Error id');
			return;
		}

		jQuery.ajax({
			url: _url_init,
			type: 'POST',
			dataType: 'json',
			data: {
				controller: _controller,
				action: 'loadById',
				shopId: Number( shopId )
			},
			success: function (data) {
				// Error
				if (typeof data.error != "undefined") {
					$('.error_inline0').html('サーバーへの接続のエラーであります。');
					return;
				}

				$('#txtStCode').val( data.Code );
				$('#txtStName').val( data.Name );
				$('#txtStDescript').val( data.Description );
				$("#ckSpecial").prop('checked', Number( data.IsSpecial ) );
			},
			error: function (xhr, textStatus, errorThrown) {
				$('.error_inline0').html('サーバーへの接続のエラーであります。');
			}
		});

		$('#isEdit').val(1);
		$('#shopTypeId').val(shopId);
	}
	else {
		$('#isEdit').val(0);
		$('#shopTypeId').val('');
	}
}

// Delete shop
function deleteShop() {
	jQuery.ajax({
		url: _url_init,
		type: 'POST',
		dataType: 'text',
		data: {
			controller: _controller,
			action: 'delete',
			shopId: Number( $('#shopTypeId').val() )
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

// Export Shop to csv
function exportCSV() {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'exportCSV',
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
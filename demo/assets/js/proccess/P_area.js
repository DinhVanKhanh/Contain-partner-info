const _url_init = 'src/index.php';
const _controller = 'area';

// Load list Area
function loadAreaList() {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'loadList'
		},
		beforeSend: function () {
			$('#scLoading').show();
		},
		success: function (data) {
			$('#tableContent').html(data.view);

			if ( !isNaN( $('#checkRowId').val() ) ) {
				$('#' + $('#checkRowId').val() ).prop('checked', true);
			}
			else {
				$('#checkRowId').val('');
				$('#' + $('#checkRowId').val() ).prop('checked', false);
			}

			$('input[type="checkbox"]').on('change', function () {
				$('input[type="checkbox"]').not(this).prop('checked', false);
				if ($('#' + this.id).is(':checked')) {
					$('#checkRowId').val(this.id);
				}
				else {
					$('#checkRowId').val('');
				}
			});
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('#scLoading').hide();
		}
	});
}

// New area
function addArea(code, name) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'add',
			code: code,
			name: name
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
			loadAreaList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Edit area
function editArea(code, name, id) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'edit',
			code: code,
			name: name,
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
			loadAreaList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Save Area
function saveAreas() {
	let partern = /^(\s+|)$/;
	if ( partern.test( $('#txtAcode').val() ) ) {
		$('.error_inline0').html('地区コードは未入力です。');
		$('#txtAcode').focus();
		return;
	}
	else if ( $('#txtAcode').val().length > 12 ) {
		$('.error_inline0').html('地区コードを12文字以内で入力してください。');
		$('#txtAcode').focus();
		return;
	}
	else if ( partern.test( $('#txtAname').val() ) ) {
		$('.error_inline0').html('地区名は未入力です。');
		$('#txtAname').focus();
		return;
	}
	else if ( $('#txtAname').val().length > 12 ) {
		$('.error_inline0').html('地区名を12文字以内で入力してください。');
		$('#txtAname').focus();
		return;
	}

	if ( Number( $('#isEdit').val() ) == 1 ) {
		editArea( 
			$('#txtAcode').val(),
			$('#txtAname').val(),
			Number( $('#areaId').val() )
		);
	}
	else {
		addArea( 
			$('#txtAcode').val(),
			$('#txtAname').val()
		);
	}
}

// Open dialog
function openDialog(areaId, isEdit) {
	$('#txtAcode').val('');
	$('#txtAname').val('');
	$('.error_inline0').html('');
	$('.dialogLoading').hide();

	if ( Number( isEdit ) == 1 ) {
		if ( /^(\s+|\D)$/.test(areaId) ) {
			$('.error_inline0').html('Error id');
			return;
		}

		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: _url_init,
			data: {
				controller: _controller,
				action: 'loadById',
				areaId: Number( areaId )
			},
			success: function (data) {
				$('#txtAcode').val(data.AreaCode);
				$('#txtAname').val(data.AreaName);
			},
			error: function (xhr, textStatus, errorThrown) {
				$('.error_inline0').html('サーバーへの接続のエラーであります。');
			}
		});
		$('#isEdit').val(1);
		$('#areaId').val(areaId);
	}
	else {
		$('#isEdit').val(0);
		$('#areaId').val('');
	}
}

// Delete Area
function deleteArea() {
	if ( /^(\s|\s+|\D)$/.test($('#areaId').val()) ) {
		$('.error_inline0').html('Error id');
		return;
	}

	jQuery.ajax({
		type: 'POST',
		dataType: 'text',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'delete',
			areaId: Number( $('#areaId').val() )
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

// Export areas to csv
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

// Change order row
function changeAreaRow(params) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data : {
			controller: _controller,
			action : 'changeOrderRow',
			curId: Number( params.curId ),
			curIdx: Number( params.curIdx ),
			upIdx: Number( params.upIdx ),
			upId: Number( params.upId ),
		}
	}).done(function () {
		loadAreaList();
	});
}
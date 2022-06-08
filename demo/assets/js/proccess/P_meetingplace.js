const _url_init = 'src/index.php';
const _controller = 'meetingplace';

// Load list MeetingPlaces
function loadMeetingPlacesList() {
	$('#scLoading').show();
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
			$('.error_inline0').html('サーバーへの接続のエラーであります');
		},
		complete: function () {
			$('#scLoading').hide();
		}
	});
}

// New MeetingPlaces
function addMeetingPlace(formData) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: formData,
		processData: false,
		contentType: false,
		beforeSend: function () {
			$('.dialogLoading').show();
		},
		success: function (data) {
			if (typeof data.errMsg != "undefined") {
				$('.error_inline0').html(data.errMsg);
				return;
			}

			$.fancybox.close();
			loadMeetingPlacesList()
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Edit MeetingPlaces
function editMeetingPlaces(formData) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: formData,
		processData: false,
		contentType: false,
		beforeSend: function () {
			$('.dialogLoading').show();
		},
		success: function (data) {
			if (typeof data.errMsg != "undefined") {
				$('.error_inline0').html(data.errMsg);
				return;
			}

			$.fancybox.close();
			loadMeetingPlacesList()
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Save MeetingPlaces
function saveMeetingPlaces() {
	$('.error_inline0').html('');
	let partern = /^(\s+|)$/;

	if ( partern.test( $('#txtMtCode').val() ) ) {
		$('.error_inline0').html('会場コードは未入力です。');
		$('#txtMtCode').focus();
		return;
	}
	else if ( $('#txtMtCode').val().length > 30 ) {
		$('.error_inline0').html('会場コードを30文字以内で入力してください。');
		$('#txtMtCode').focus();
		return;
	}
	else if ( partern.test( $('#txtMtShopName1').val() ) ) {
		$('.error_inline0').html('店名1は未入力です。');
		$('#txtMtShopName1').focus();
		return;
	}
	else if ( $('#txtMtShopName1').val().length > 500 ) {
		$('.error_inline0').html('店名1を500文字以内で入力してください。');
		$('#txtMtShopName1').focus();
		return;
	}
	else if ( $('#txtMtAddress').val().length > 500 ) {
		$('.error_inline0').html('会場名を500文字以内で入力してください。');
		$('#txtMtAddress').focus();
		return;
	}
	else if ( $('#txtMtAddress1').val().length > 500 ) {
		$('.error_inline0').html('会場名を500文字以内で入力してください。');
		$('#txtMtAddress1').focus();
		return;
	}
	else if ( !partern.test( $('#txtMtTel').val() )
	&& !(/\d{2,3}-\d{3,4}-\d{4,5}/.test( $('#txtMtTel').val() ) ) ) {
		$('.error_inline0').html('電話のフォーマットが無効です');
		$('#txtMtTel').focus();
		return;
	}
	else if ( !partern.test( $('#txtMtFax').val() )
	&& !(/\d{3}-\d{3}-\d{4}/.test( $('#txtMtFax').val() ) ) ) {
		$('.error_inline0').html('Faxのフォーマットが無効です');
		$('#txtMtFax').focus();
		return;
	}

	let formData = new FormData();
	formData.append('controller', _controller);
	formData.append('code', $('#txtMtCode').val());
	formData.append('address_1', $('#txtMtAddress').val());
	formData.append('address_2', $('#txtMtAddress1').val());
	formData.append('storeName1', $('#txtMtShopName1').val());
	formData.append('storeName2', $('#txtMtShopName2').val());
	formData.append('todouId', $('#todouhuken').val());
	formData.append('tel', $('#txtMtTel').val());
	formData.append('fax', $('#txtMtFax').val());
	formData.append('map', $('#txtMtMap').val());
	formData.append('posCode', $('#txtMtPos').val());

	if ( Number( $('#isEdit').val() ) == 1 ) {
		formData.append('action', 'edit');
		formData.append('id', $('#MtId').val());
		editMeetingPlaces(formData);
	}
	else {
		formData.append('action', 'add');
		addMeetingPlace(formData);
	}
}

// Open dialog
function openDialog(mtId, isEdit) {
	$('#txtMtCode').val('');
	$('#txtMtAddress').val('');
	$('#txtMtAddress1').val('');
	$('#txtMtShopName1').val('');
	$('#txtMtShopName2').val('');
	$('#txtMtTel').val('');
	$('#txtMtFax').val('');
	$('#txtMtMap').val('');
	$('#txtMtPos').val('');
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
				mtId: mtId
			},
			success: function (data) {
				$('#txtMtCode').val(data.Code);
				$('#txtMtAddress').val(unescapeHtml(data.Address_1));
				$('#txtMtAddress1').val(unescapeHtml(data.Address_2));
				$('#txtMtShopName1').val(unescapeHtml(data.storeName1));
				$('#txtMtShopName2').val(unescapeHtml(data.storeName2));
				$('#txtMtPos').val(unescapeHtml(data.postalCode));
				$('#txtMtTel').val(data.Tel);
				$('#txtMtFax').val(data.Fax);
				$('#txtMtMap').val(unescapeHtml(data.Map));
				$('#todouhuken').val(data.TodouhukenId);
			},
			error: function (xhr, textStatus, errorThrown) {
				$('.error_inline0').html('サーバーへの接続のエラーであります');
			}
		});

		$('#isEdit').val(1);
		$('#MtId').val(mtId);
	}
	else {
		$('#isEdit').val(0);
		$('#MtId').val('');
	}
}

// Delete MeetingPlaces
function deleteMeetingPlaces() {
	$('.error_inline0').html('');
	jQuery.ajax({
		type: 'POST',
		dataType: 'text',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'delete',
			mtId: Number( $('#MtId').val() )
		},
		success: function (data) {
			$('#submit_del_ok').show();
			$('#submit_del').hide();
			$('.btnClose').hide();
			$('.message').html(data);
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります');
		}
	});
}

// Filter meetingplace by area
function filterMeetingPlaceByArea() {
	let areaId = Number( $('#area').val() );
	if ( areaId >= 0 ) {
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: _url_init,
			data: {
				controller: _controller,
				action: 'filterByArea',
				areaId: areaId
			},
			beforeSend: function () {
				$('#scLoading').show();
			},
			success: function (data) {
				$('#tableContent').html(data.view);
			},
			error: function (xhr, textStatus, errorThrown) {
				$('.error_inline0').html('サーバーへの接続のエラーであります');
			},
			complete: function () {
				$('#scLoading').hide();
			}
		});
	}
	else {
		loadMeetingPlacesList();
	}
}

// Export meeting place to csv
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
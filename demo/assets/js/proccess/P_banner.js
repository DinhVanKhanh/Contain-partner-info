const _url_init = 'src/index.php';
const _controller = 'banner';

// Load list banner
function loadBannerList() {
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
			console.log(textStatus);
		}
		,
		complete: function () {
			$('#scLoading').hide();
		}
	});
}

// New banner
function addBanner( formData ) {
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
			// console.log(data);
			// console.log('abc8');
			$.fancybox.close();
			loadBannerList();
			$('#ipImg3').val('');
			$('#ipImg2').val('');
			$('#ipImg1').val('');
			$('#ipImg').val('');
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Edit banner
function editBanner( formData ) {
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
			loadBannerList();
			$('#ipImg3').val('');
			$('#ipImg2').val('');
			$('#ipImg1').val('');
			$('#ipImg').val('');
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Save Banner
function saveBanner() {
	let partern = /^(\s+|)$/;
	if (partern.test($('#inputImage1').val()) &&
		partern.test($('#inputImage2').val()) &&
		partern.test($('#inputImage3').val())) {
		$('.error_inline0').html('情報を入力して下さい。');
		$('#inputImage1').focus();
		return false;
	}

	let formData = new FormData();
	formData.append( 'controller', _controller );

	// Banner1
	if (typeof $('#ipImg1')[0].files[0] != "undefined") {
		formData.append('Banner1', $('#ipImg1')[0].files[0]);
	}
	formData.append('IsShow1', ($('#chkTop').prop("checked") == true) ? 0 : 1);

	// Banner2
	if (typeof $('#ipImg2')[0].files[0] != "undefined") {
		formData.append('Banner2', $('#ipImg2')[0].files[0]);
	}
	formData.append('IsShow2', ($('#chkLeft').prop("checked") == true) ? 0 : 1);

	// Banner3
	if (typeof $('#ipImg3')[0].files[0] != "undefined") {
		formData.append('Banner3', $('#ipImg3')[0].files[0]);
	}
	formData.append('IsShow3', ($('#chkRight').prop("checked") == true) ? 0 : 1);

	let ck = Number( $("input[name='IsShop']:checked").val() );
	if ( ck == 1 ) {
		formData.append('IsShop', 1);
		formData.append('ParentId', $('#parent_shop').val());
	}
	else {
		formData.append('IsShop', 0);
		formData.append('ParentId', $('#parent_area').val());
	}
	formData.append('Description', $('#description').val());

	if ( Number( $('#isEdit').val() ) == 1 ) {
        formData.append('oldBanner1', $('#img1').val());
        formData.append('oldBanner2', $('#img2').val());
        formData.append('oldBanner3', $('#img3').val());
		formData.append('action', 'edit');
		formData.append('id', $('#bannerId').val());
		editBanner( formData );
	}
	else {
		formData.append('action', 'add');
		addBanner( formData );
	}
}

// Open dialog Edit
function openDialog(bannerId, isEdit) {
	$('#parent_area option:first').prop('selected', true);
	$('#parent_shop option:first').prop('selected', true);
	$('#btnDelBn1').hide();
	$('#btnDelBn2').hide();
	$('#btnDelBn3').hide();
	$('#inputImage1').val('');
	$('#inputImage2').val('');
	$('#inputImage3').val('');
	$('#img1').val('');
	$('#img2').val('');
	$('#img3').val('');
	$('#description').val('');

	$('#chkTop').prop("checked", false);
	$('#chkLeft').prop("checked", false);
	$('#chkRight').prop("checked", false);
	$('#IsArea').prop("checked", true);
	$('.error_inline0').text('');
	$('.dialogLoading').hide();

	if ( Number( isEdit ) == 1 ) {
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: _url_init,
			data: {
				controller: _controller,
				action: 'loadById',
				bannerId: bannerId
			},
			success: function (data) {
				// Error
				if (typeof data.error != "undefined") {
					$('.error_inline0').html('サーバーへの接続のエラーであります。');
					return;
				}

				// Success
				switch ( Number( data.IsShop ) ) {
					case 0:
						$('#IsArea').prop("checked", true);
						$('#parent_area').val(data.ParentId);
						break;

					case 1:
						$('#IsShop').prop("checked", true);
						$('#parent_shop').val(data.ParentId);
						break;
				}
				let partern = /^\s*$/;

				// Banner 1
				let banner1 = data.Banner1 == "NULL" ? '' : data.Banner1;
				$('#inputImage1').val(banner1);
				if (!partern.test(banner1)) {
					$('#btnDelBn1').show();
				}
				$('#img1').val(banner1);

				if ( Number( data.IsShow1 ) == 0 ) {
					$('#chkTop').prop("checked", true);
				}

				// Banner 2
				let banner2 = data.Banner2 == "NULL" ? '' : data.Banner2;
				$('#inputImage2').val(banner2);
				if (!partern.test(banner2)) {
					$('#btnDelBn2').show();
				}
				$('#img2').val(banner2);

				if ( Number( data.IsShow2 ) == 0 ) {
					$('#chkLeft').prop("checked", true);
				}

				// Banner 3
				let banner3 = data.Banner3 == "NULL" ? '' : data.Banner3;
				$('#inputImage3').val(banner3);
				if (!partern.test(banner3)) {
					$('#btnDelBn3').show();
				}
				$('#img3').val(banner3);

				if ( Number( data.IsShow3 ) == 0 ) {
					$('#chkRight').prop("checked", true);
				}
				$('#description').val(data.Description);
			},
			error: function (xhr, textStatus, errorThrown) {
				$('.error_inline0').html('サーバーへの接続のエラーであります。');
			}
		});

		$('#isEdit').val(1);
		$('#bannerId').val(bannerId);
	}
	else {
		$('#isEdit').val(0);
		$('#bannerId').val('');
	}
}

// Delete Banner
function deleteBanner() {
	jQuery.ajax({
		type: 'POST',
		dataType: 'text',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'delete',
			bannerId: Number( $('#bannerId').val() ),
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

// Export Banner to csv
function exportCSV() {
	jQuery.ajax({
		url: _url_init,
		type: 'POST',
		dataType: 'json',
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

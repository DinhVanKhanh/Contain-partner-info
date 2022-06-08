const _url_init = 'src/index.php';
const _controller = 'todouhuken';

// Load list todouhuken
function loadTodouhukenList() {
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

// New todouhuken
function addTodoukuhen( code, name, display, areaId ) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'add',
			code: code,
            name: name,
            display: display,
			areaId: areaId
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
			loadTodouhukenList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Edit todouhuken
function editTodouhuken( code, name, display, areaId, id ) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
			action: 'edit',
			code: code,
            name: name,
            display: display,
			areaId: areaId,
			id: id
		},
		beforeSend: function () {
			$('.dialogLoading').show();
		},
		success: function (data) {
			if (typeof data.errMsg != "undefined") {
				$('.error_inline0').html(data.errMsg);
				console.log( data );
				return;
			}

			$.fancybox.close();
			loadTodouhukenList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Save Todouhuken
function saveTodouhuken() {
    let partern = /^(\s+|)$/;
	if ( partern.test( $('#txtStCode').val() ) ) {
		$('.error_inline0').html('都市コードは未入力です。');
		$('#txtStCode').focus();
		return;
	}
	else if ( $('#txtStCode').val().length > 30 ) {
		$('.error_inline0').html('都市コードを30文字以内で入力してください。');
		$('#txtStCode').focus();
		return;
	}
	else if ( partern.test( $('#txtStName').val() ) ) {
		$('.error_inline0').html('都市名は未入力です。');
		$('#txtStName').focus();
		return;
	}
	else if ( $('#txtStName').val().length > 50 ) {
		$('.error_inline0').html('都市名を50文字以内で入力してください。');
		$('#txtStName').focus();
		return;
    }
    else if ( partern.test( $('#txtStDisplay').val() ) ) {
		$('.error_inline0').html('都市名（表示）は未入力です。');
		$('#txtStName').focus();
		return;
	}
	else if ( $('#txtStDisplay').val().length > 50 ) {
		$('.error_inline0').html('都市名（表示）を50文字以内で入力してください。');
		$('#txtStName').focus();
		return;
	}

    $('.dialogLoading').show();
    if ( Number( $('#isEdit').val() ) == 1 ) {
		editTodouhuken(
			Number( $('#txtStCode').val() ),
            $('#txtStName').val(),
            $('#txtStDisplay').val(),
            Number( $('#area').val() ),
			Number( $('#todouId').val() )
		);
	}
	else {
		addTodoukuhen(
			Number( $('#txtStCode').val() ),
            $('#txtStName').val(),
            $('#txtStDisplay').val(),
            Number( $('#area').val() )
		);
	}
}

// Open dialog
function openDialog(todouId, isEdit) {
    $('#txtStCode').val('');
    $('#txtStName').val('');
	$('#txtStDisplay').val('');
	$('#area option:first').prop('selected', true);
    $('.error_inline0').html('');
    $('.dialogLoading').hide();

    if (Number(isEdit) == 1) {
        if (/^(\s+|\D)$/.test(todouId)) {
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
                todouId: Number(todouId)
            },
            success: function (data) {
                $('#area').val(data.AreaId);
                $('#txtStCode').val(data.TodouhukenCode);
                $('#txtStName').val(data.TodouhukenName);
                $('#txtStDisplay').val(data.TodouhukenDisplay);
            },
            error: function (xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバーへの接続のエラーであります。');
            }
        });
        $('#isEdit').val(1);
        $('#todouId').val(todouId);
    }
    else {
        $('#isEdit').val(0);
        $('#todouId').val('');
    }
}

function deleteTodouhuken() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'text',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'delete',
            todouId: Number( $('#todouId').val() )
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

// Export todouhuken
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
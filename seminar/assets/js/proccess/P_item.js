const _url_init = 'src/index.php';
const _controller = 'item';

// Load list Item
function loadItemsList() {
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
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        },
		complete: function () {
			$('#scLoading').hide();
		}
    });
}

// New item
function addItem(type, code, name) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
            action: 'add',
            type: type,
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
			loadItemsList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Edit item
function editItem(type, code, name, id) {
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
            action: 'edit',
            type: type,
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
			loadItemsList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Save item
function saveItems() {
    let partern = /^(\s+|)$/;
	if ( partern.test( $('#ItemCode').val() ) ) {
		$('.error_inline0').html('種類コードは未入力です。');
		$('#ItemCode').focus();
		return;
	}
	else if ( $('#ItemCode').val().length > 30 ) {
		$('.error_inline0').html('種類コードを30文字以内で入力してください。');
		$('#ItemCode').focus();
		return;
	}
	else if ( partern.test( $('#ItemName').val() ) ) {
		$('.error_inline0').html('種類名は未入力です。');
		$('#ItemName').focus();
		return;
	}
	else if ( $('#ItemName').val().length > 30 ) {
		$('.error_inline0').html('種類名を30文字以内で入力してください。');
		$('#ItemName').focus();
		return;
    }

	if ( Number( $('#isEdit').val() ) == 1 ) {
		editItem( 
			$('#Type').val(),
            $('#ItemCode').val(),
            $('#ItemName').val(),
			Number( $('#ItemId').val() )
		);
	}
	else {
		addItem( 
			$('#Type').val(),
            $('#ItemCode').val(),
            $('#ItemName').val()
		);
	}
}

// Open dialog
function openDialog(itemId, isEdit) {
    $('#Type option').attr('selected', false);
    $('#ItemCode').val('');
    $('#ItemName').val('');
    $('.error_inline0').html('');
    $('.dialogLoading').hide();

    if (Number(isEdit) == 1) {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: _url_init,
            data: {
                controller: _controller,
                action: 'loadById',
                itemId: Number(itemId)
            },
            success: function (data) {
                $('#Type').val(data.Type);
                $('#ItemCode').val(data.ItemCode);
                $('#ItemName').val(data.ItemName);
            },
            error: function (xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバへの接続に失敗しました。');
            }
        });
        $('#isEdit').val(1);
        $('#ItemId').val(itemId);
    }
    else {
        $('.dialogLoading').hide();
        $('#isEdit').val(0);
        $('#ItemId').val('');
    }
}

// Delete Item
function deleteItem() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'text',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'delete',
            itemId: Number( $('#ItemId').val() )
        },
        success: function (data) {
            $('#submit_del_ok').show();
			$('#submit_del').hide();
			$('.btnClose').hide();
			$('.message').html(data);
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        }
    });
}
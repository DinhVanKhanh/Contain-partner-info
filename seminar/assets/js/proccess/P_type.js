const _url_init = 'src/index.php';
const _controller = 'type';

function loadTypesList() {
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
            $('#tblTypes tr.kaisha:first-child').find('td');
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        },
		complete: function () {
			$('#scLoading').hide();
		}
    });
}

// New Type
function addType( name, description ) {
    jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
            action: 'add',
            typesName: name,
            Description: description
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
			loadTypesList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Edit Type
function editType( name, description, id ) {
    jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: _url_init,
		data: {
			controller: _controller,
            action: 'edit',
            typesName: name,
            Description: description,
            typesId: Number( id )
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
			loadTypesList();
		},
		error: function (xhr, textStatus, errorThrown) {
			$('.error_inline0').html('サーバーへの接続のエラーであります。');
		},
		complete: function () {
			$('.dialogLoading').hide();
		}
	});
}

// Save Types
function saveTypes() {
    if ( /^\s*$/.test( $('#TypesName').val() ) ) {
        $('.error_inline0').html('種類名は未入力です。'); 
        $('#TypesName').focus();
        return false;
    }
    else if ($('#TypesName').val().length > 12) {
        $('.error_inline0').html('種類名を12文字以内で入力してください。');   
        $('#TypesName').focus();
        return false;
    }
    $('.error_inline0').html('');

    if ( Number( $('#isEdit').val() ) == 1 ) {
        editType(
            $('#TypesName').val(),
            $('#Description').val(),
            $('#typesId').val()
        );
    }
    else {
        addType(
            $('#TypesName').val(),
            $('#Description').val()
        );
    }
}

// Open dialog
function openDialog(typesId, isEdit) {
    $('.error_inline0').html('');

    if (Number(isEdit) == 1) {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: _url_init,
            data: {
                controller: _controller,
                action: 'loadById',
                typesId : Number(typesId)
            },
            success: function(data) {
                $('#showCode').show();
                $('#TypesId').text(data.TypesId);
                $('#TypesName').val(data.TypesName);
                $('#Description').val(data.Description);
            },
            error: function(xhr, textStatus, errorThrown) {
               $('.error_inline0').html('サーバへの接続に失敗しました。');    
            }
        });
        $('#isEdit').val(1);
        $('#typesId').val(typesId);
    }
    else {
        $('#showCode').hide();
        $('#isEdit').val(0);
        $('#typesId').val('');
        $('#TypesName').val('');
        $('#Description').val('');
    }    
}

// Delete Types
function deleteTypes() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'text',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'delete',
            typesId : Number( $('#typesId').val() )
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
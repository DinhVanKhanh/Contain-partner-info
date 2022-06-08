const _url_init = 'src/index.php';
const _controller = 'serial';

function loadSeminarSeriallist() {
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
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        },
		complete: function () {
			$('#scLoading').hide();
		}
    });
}

// New Serial
function addSerial( number, sampleId, note ) {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'add',
            SampleId: Number( sampleId ),
            SerialNumber: number,
            Note: note
        },
        beforeSend: function () {
			$('.dialogLoading').show();
		},
        success: function(data) {
            if (typeof data.errMsg != "undefined") {
				$('.error_inline0').html(data.errMsg);
				return;
			}

            $.fancybox.close();
            loadSeminarSeriallist();
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続のエラーであります。');
        },
        complete: function () {
			$('.dialogLoading').hide();
		}
    });
}

// Edit Serial
function editSerial( number, sampleId, note, id ) {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'edit',
            SampleId: Number( sampleId ),
            SerialId: Number( id ),
            SerialNumber: number,
            Note: note
        },
        beforeSend: function () {
			$('.dialogLoading').show();
		},
        success: function(data) {
            if (typeof data.errMsg != "undefined") {
				$('.error_inline0').html(data.errMsg);
				return;
			}

            $.fancybox.close();
            loadSeminarSeriallist();
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続のエラーであります。');
        },
        complete: function () {
			$('.dialogLoading').hide();
		}
    });
}

function saveSeminarSerial() {
    let partern = /^\s*$/;
    if ( partern.test( $('#SampleId').val() ) ) {
        $('.error_inline0').html('セミナー名を入力して下さい。');
        $('#SampleId').focus();
        return false;
    }

    if ( partern.test( $('#SerialNumber').val() ) ) {
        $('.error_inline0').html('所有製品のシリアルNoを入力して下さい。');
        $('#SerialNumber').focus();
        return false;
    }
    $('.error_inline0').html('');

    if ( Number( $('#isEdit').val() ) == 1 ) {
        editSerial(
            $('#SerialNumber').val(),
            $('#SampleId').val(),
            $('#Note').val(),
            $('#SerialId').val()
        );
    }
    else {
        addSerial(
            $('#SerialNumber').val(),
            $('#SampleId').val(),
            $('#Note').val()
        );
    }
}

function openDialog(SerialId, isEdit) {
    $('#SampleId option:first').prop('selected', true);
    $('#SeminarName').val('');
    $('#SerialNumber').val('');
    $('#Note').val('');
    $('.error_inline0').html('');
    $('.dialogLoading').css('display', 'none');

    if ( Number( isEdit ) == 1 ) {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: _url_init,
            data: {
                controller: _controller,
                action: 'loadById',
                SerialId: Number( SerialId )
            },
            success: function(data) {
                $('#SerialId').val(data.SerialId);
                $('#SampleId').val(data.SampleId);
                $('#SerialNumber').val(data.SerialNumber);
                $('#Note').val(data.Note);
            },
            error: function(xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバへの接続に失敗しました。');
            }
        });
        $('#isEdit').val(1);
        $('#SerialId').val(SerialId);
    }
    else {
        $('#isEdit').val(0);
        $('#SerialId').val('');
    }
}

function deleteSeminarSerial() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'text',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'delete',
            SerialId: Number( $('#SerialId').val() )
        },
        success: function(data) {
            $('#submit_del_ok').show();
			$('#submit_del').hide();
			$('.btnClose').hide();
			$('.message').html(data);
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        }
    });
}
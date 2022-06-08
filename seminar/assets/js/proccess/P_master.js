const _url_init = 'src/index.php';
const _controller = 'master';

function loadMasterList() {
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

function saveMaster() {
    let partern = /^\s*$/;
    // Name
    if ( partern.test( $('#SampleName').val() ) ) {
        $('.error_inline0').html('セミナー名を入力して下さい。');    
        $('#SampleName').focus();
        return false;
    }
    if ( $('#SampleName').val().length > 50 ) {
        $('.error_inline0').html('セミナー名を50文字以内で入力してください。');    
        $('#SampleName').focus();
        return false;
    }

    // DeadLine
    if ( partern.test( $('#SampleDeadline').val() ) ) {
        $('.error_inline0').html('標準申込期限を入力して下さい。');    
        $('#SampleDeadline').focus();
        return false;
    }
    if ( isNaN( $('#SampleDeadline').val() ) ) {
        $('.error_inline0').html('標準申込期限が有効ではない。');    
        $('#SampleDeadline').focus();
        return false;
    }
    if ( $('#SampleDeadline').val().length > 5 ) {
        $('.error_inline0').html('標準申込期限を5文字以内で入力してください。');    
        $('#SampleDeadline').focus();
        return false;
    }
    if ( $('#SampleDeadline').val() < 0 ) {
        $('.error_inline0').html('標準申込期限が有効ではない。');    
        $('#SampleDeadline').focus();
        return false;
    }

    // FeesChk
    if ( $('input[name="SampleFeesChk"]:checked').val() > 5 ) {
        $('.error_inline0').html('受講料を20文字以内で入力してください。');    
        $('input[name="SampleFeesChk"]').focus();
        return false;
    }

    // Fees
    if ( isNaN($('#SampleFees').val()) || $('#SampleFees').val() < 0 ) {
        $('.error_inline0').html('受講料が有効ではない。');    
        $('#SampleFees').focus();
        return false;
    }
    
    // Month
    let AppMonth = '';
    for (let i = 1; i <= 12; i++) {
        if ( $('#SampleAppMonth'+i).is(':checked') ) {
            AppMonth += ',' + i;
        }
    }
    AppMonth = AppMonth.substr(1);
    if ( partern.test( AppMonth ) ) {
        $('.error_inline0').html('必ず開催時期のいずれかを選んでください。');    
        $('#SampleAppMonth1').focus();
        return false;
    }

    // Always
    let SampleAlways = 0;
    if ( $('#ChkSampleAlways').is(':checked') ) {
        SampleAlways = $('#SampleAlways').val();
    }

    // Email
    let Email = $('#SampleEmail').val();
    if ( !partern.test( Email ) ) {
        Email = Email.replace(" ", "");
        if ( Email.indexOf(',') != -1 ) {
            Email = Email.split(',');
            let len = Email.length;
            for ( let i = 0; i < len; i++ ) {
                if ( /^[a-z][a-z0-9_\.]{5,32}@[a-z0-9]{2,}(\.[a-z0-9]{2,4}){1,2}$/.test( Email[i] ) == false ) {
                    $('.error_inline0').html('担当者のメールアドレスが有効ではない。');    
                    $('#SampleEmail').focus();
                    return false;
                }
            }
        }
    }

    // ========= Case : input checked not enought 12 month =========
    let countChk = $('.tableMonth input:checked').size();
    if (countChk < 1) {
        $('.error_inline0').html('必ず開催時期のいずれかを選んでください。');
        return false;
    }

    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'save',
            SampleId: $('#SampleId').val(),
            SampleName: $('#SampleName').val(),
            SampleDeadline: $('#SampleDeadline').val(),
            SampleFeesChk: $('input[name="SampleFeesChk"]:checked').val(),
            SampleFees: $('#SampleFees').val(),
            SampleTaxChk: $('input[name="SampleTaxChk"]:checked').val(),
            SampleAppMonth: AppMonth,
            SampleAlways: SampleAlways,
            SampleEmail: $('#SampleEmail').val()
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
            loadMasterList();
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        },
        complete: function () {
			$('.dialogLoading').hide();
		}
    });
}

function openDialog(SampleId) {
    $('#SampleName').val('');
    $('#SampleDeadline').val('');
    $('#SampleFees').val('');
    $('#raNoFee').prop('checked', false);
    $('#raHaveFee:selected').prop('checked', false);
    $('#raHaveTax:selected').prop('checked', false);
    $('#raNoTax:selected').prop('checked', false);
    $('.error_inline0').html('');

    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'loadById',
            SampleId: Number( SampleId )
        },
        beforeSend: function () {
			$('.dialogLoading').show();
		},
        success: function (data) {
            $('#SampleName').val(data.SampleName);
            $('#SampleDeadline').val(data.SampleDeadline);
            $('#SampleFees').val(data.SampleFees);
            $('#SampleEmail').val(data.SampleEmail);

            if (data.SampleFeesChk == 1) {
                $('#raHaveFee').prop('checked', true);
                $('#SampleFees').attr('disabled', false).removeClass('disable');
            }
            else {
                $('#raNoFee').prop('checked', true);
                $('#SampleFees').attr('disabled', true).addClass('disable');
            }

            if (data.SampleTaxChk == 1) {
                $('#raHaveTax').prop('checked', true);
            }
            else {
                $('#raNoTax').prop('checked', true);
            }
            $('.tableMonth input').prop('checked', false);

            let arrMonth = data.SampleAppMonth.split(',');
            if (arrMonth.length > 0) {
                for (let i = 0; i < arrMonth.length; i++) {
                    $('#SampleAppMonth' + arrMonth[i]).prop('checked', true);
                }
            }
            if (data.SampleAlways == 1) {
                $('#ChkSampleAlways').prop('checked', true);
            }
            else {
                $('#ChkSampleAlways').prop('checked', false);
            }
            $('#SampleId').val(data.SampleId);
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        },
        complete: function () {
			$('.dialogLoading').hide();
		}
    });
}
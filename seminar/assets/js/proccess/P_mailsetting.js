const _url_init = 'src/index.php';
const _controller = 'mail';

// Load list mail
function loadSeminarMailList() {
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

// Test mail
function testSendMail() {
    $('.error_inline0').html("");
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'testMail',
            FromMail: $('#FromMail').val(),
            FromName: $('#FromName').val(),
            MailTest: $('#MailTest').val(),
            EncriptionType: $("input[type='radio'][name='EncriptionType']:checked").val(),
            Host: $('#Host').val().trim(),
            Port: $('#Port').val().trim() == '' ? 0 : $('#Port').val(),
            Username: $('#Username').val().trim(),
            Password: $('#Password').val().trim(),
            checkSmtp: ($("#checkSmtp").is(':checked')) ? 1 : 0
        },
        beforeSend: function () {
			$('#scLoading').show();
		},
        success: function(data) {
            if (data.errMsg != true) {
                $('.error_inline0').html("テストメールの送信に失敗しました。" + "<br/>エラー内容：" + data.errMsg);
            } 
            else {
                $('.error_inline0').html('テストメールの送信にできました。');
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html(xhr.responseText);
        },
		complete: function () {
			$('#scLoading').hide();
		}
    });
}

// Save mail
function saveSeminarMail() {
    let email = $('#FromMail');
    let email2 = $('#MailTest');

    let filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if ( $('#FromMail').val() == '' ) {
        $('.error_inline0').html('差出人は未入力です。');
        $('#FromMail').focus();
        return false;
    }
    else if ( $('#FromName').val() == '' ) {
        $('.error_inline0').html('差出名は未入力です。');
        $('#FromName').focus();
        return false;
    }
    else if ( $('#Host').val() == '' ) {
        $('.error_inline0').html('SMTPサーバーは未入力です。');
        $('#Host').focus();
        return false;
    }
    else if ( $('input[type=radio][name=EncriptionType]').val() != "0" && $('#Port').val()=='' ) {
        $('.error_inline0').html('SMTPポートは未入力です。');
        $('#Port').focus();
        return false;
    }
    else if ( $('#FromMail').val() && !filter.test(email.val()) ) {
        $('.error_inline0').html('差出人のメールアドレスをご入力ください。\nexample@gmail.com');
        $('#FromMail').focus();
        return false;
    }
    else if ( $('#MailTest').val() && !filter.test(email2.val()) ) {
        $('.error_inline0').html('テスト用の宛先をご入力ください。\nexample@gmail.com');
        $('#MailTest').focus();
        return false;
    }
    else if ( $('#Username').val().length > 50 ) {
        $('.error_inline0').html('SMTPユーザ名を30文字以内で入力してください。');
        $('#Username').focus();
        return false;
    }
    else if ( $('#Host').val().length > 30 ) {
        $('.error_inline0').html('SMTPサーバーを30文字以内で入力してください。');
        $('#Host').focus();
        return false;
    }
    else if ( $('#FromName').val().length > 100 ) {
        $('.error_inline0').html('差出名を100文字以内で入力してください。');
        $('#FromName').focus();
        return false;
    }  
    else if ( $('#Password').val().length > 15 ) {
        $('.error_inline0').html('パスワードを15文字以内で入力してください。');
        $('#Password').focus();
        return false;
    }

    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'edit',
            FromMail: $('#FromMail').val(),
            FromName: $('#FromName').val(),
            MailTest: $('#MailTest').val(),
            EncriptionType: $("input[type='radio'][name='EncriptionType']:checked").val(),
            Host: $('#Host').val().trim(),
            Port: $('#Port').val().trim() == '' ? 0 : $('#Port').val(),
            Username: $('#Username').val().trim(),
            Password: $('#Password').val().trim(),
            id: $('#EmailId').val()
        },
        success: function(isSuccess) {
            if (!isSuccess) {
                $('.error_inline0').html("サーバへの接続に失敗しました。");
                return;
            }
            $.fancybox.close();
            loadSeminarMailList();
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html("サーバへの接続に失敗しました。");
        }
    });
}

function openDialog(emailId, isEdit) {
    $('#Password').val('');
    $('.error_inline0').html('');

    if (Number(isEdit) == 1) {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: _url_init,
            data: {
                controller: _controller,
                action: 'loadById',
                EmailId: emailId
            },
            beforeSend: function () {
                $('#scLoading').show();
            },
            success: function(data) {
                $('#Host').val(data.Host);
                $('#Port').val(data.Port);
                if (data.Username != '') {
                    $('#Username').val(data.Username);
                    $('#Password').val(data.Password);
                    $('#checkSmtp').prop('checked', true);
                    $('#Username').removeAttr('disabled').removeClass('disable');
                    $('#Password').removeAttr('disabled').removeClass('disable');
                }
                else {
                    $('#checkSmtp').prop('checked', false);
                    $('#Username').attr({
                        'disabled': 'true'
                    }).addClass('disable').val('');
                    $('#Password').attr({
                        'disabled': 'true'
                    }).addClass('disable').val('');
                }
                $('#FromMail').val(data.FromEmail);
                $('#FromName').val(data.FromName);
                $('#MailTest').val(data.MailTest);
                switch (data.EncriptionType) {
                    case "0":
                        $('#raNone').prop('checked', true);
                        $('#Port').val('');
                        break;
                    case "1":
                        $('#raSSL').prop('checked', true);
                        break;
                    case "2":
                        $('#raTLS').prop('checked', true);
                        break;
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                $('.error_inline0').html("サーバへの接続に失敗しました。");
            },
            complete: function () {
                $('#scLoading').hide();
            }
        });
        $('#isEdit').val(1);
        $('#EmailId').val(emailId);
    }
    else {
        $('#isEdit').val(0);
        $('#EmailId').val('');
    }
}
const _url_init = 'src/index.php';
const _controller = 'seminara';

Date.prototype.yyyymmdd = function() {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString();
    var dd = this.getDate().toString();
    return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]);
};

// Load Seminar A List
function loadSeminarList() {
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
        success: function(data) {
            $('#tableContent').html(data.view);
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続エラーが発生しました。');
        },
        complete: function () {
            $('#scLoading').hide();
        }
    });
}

// Save Seminar A
function saveSeminarA() {
    let partern = /^\s*$/;
    // Seminar Name
    if ( partern.test( $('#SeminarName').val() ) ) {
        $('.error_inline0').html('セミナー名を入力して下さい。');
        $('#SeminarName').focus();
        return;
    }

    if ( $('#SeminarName').length > 50 ) {
        $('.error_inline0').html('セミナー名を50文字以内で入力してください。');
        $('#SeminarName').focus();
        return;
    }

    // Area ID
    if ( partern.test($('#seArea').val()) ) {
        $('.error_inline0').html('地域コードを入力して下さい。');
        $('#seArea').focus();
        return;
    }

    if ( $('#seArea').val().length > 30 ) {
        $('.error_inline0').html('地域コードを30文字以内で入力してください。');
        $('#seArea').focus();
        return;
    }

    // Venue Name
    if ( partern.test( $('#VenueName').val() ) ) {
        $('.error_inline0').html('開催会場名を入力して下さい。');
        $('#VenueName').focus();
        return;
    }

    if ( $('#VenueName').length > 1000 ) {
        $('.error_inline0').html('開催会場名を1000文字以内で入力してください。');
        $('#VenueName').focus();
        return;
    }

    // Venue Address
    if ( partern.test( $('#VenueAddress').val() ) ) {
        $('.error_inline0').html('開催会場住所を入力して下さい。');
        $('#VenueAddress').focus();
        return;
    }

    if ( $('#VenueAddress').length > 1000 ) {
        $('.error_inline0').html('開催会場住所を1000文字以内で入力してください。');
        $('#VenueAddress').focus();
        return;
    }

    // Venue Map
    if ( $('#VenueMap').length > 1000 ) {
        $('.error_inline0').html('地図(URL)を5000文字以内で入力してください。');
        $('#VenueMap').focus();
        return;
    }

    // Venue Station
    if ( partern.test( $('#VenueStation').val() ) ) {
        $('.error_inline0').html('最寄駅を入力して下さい。');    
        $('#VenueStation').focus();
        return false;
    }

    if ( $('#VenueStation').length > 500 ) {
        $('.error_inline0').html('最寄駅を500文字以内で入力してください。');
        $('#VenueStation').focus();
        return;
    }

    // Time
    if ( partern.test( $('#TimeStart').val() ) ) {
        $('.error_inline0').html('開始時間を入力して下さい。');
        $('#TimeStart').focus();
        return;
    }

    if ( partern.test( $('#TimeEnd').val() ) ) {
        $('.error_inline0').html('終了時刻を入力して下さい。');
        $('#TimeEnd').focus();
        return;
    }

    // Validate time
    let arrFT = $('#TimeStart').val().split(':');
    let arrTT = $('#TimeEnd').val().split(':');

    if ( (arrFT[0] > arrTT[0])
    || ( arrFT[0] == arrTT[0] && arrFT[1] == arrTT[1] )
    || ( arrFT[0] == arrTT[0] && arrFT[1] > arrTT[1] )) {
        $('.error_inline0').html('終了時間が有効ではない。');
        $('#TimeEnd').focus();
        return;
    }

    // Date
    if ( partern.test( $('#scDate').val() ) ) {
        $('.error_inline0').html('開催日が未入力です。');
        $('#scDate').focus();
        return;
    }

    if ( !/^\d{4}-\d{1,2}-\d{1,2}$/.test( $('#scDate').val() ) ) {
        $('.error_inline0').html('日付が有効ではありません。');
        $('#scDate').focus();
        return;
    }

    // Person
    if ( partern.test( $('#CountPerson').val() ) ) {
        $('.error_inline0').html('席数を入力して下さい。');
        $('#CountPerson').focus();
        return;
    }

    if ( Number($('#CountPerson').val()) < 0 ) {
        $('.error_inline0').html('席数を入力して下さい。');
        $('#CountPerson').focus('席数が有効ではない。');
        return;
    }

    // Note
    if ( $('#note').length > 500 ) {
        $('.error_inline0').html('開催会場住所を500文字以内で入力してください。');
        $('#note').focus();
        return;
    }

    // Tel
    let contact = [
        /^\d{2}\-\d{3}\-\d{5}$/,
        /^\d{2}\-\d{4}\-\d{4}$/,
        /^\d{3}\-\d{3}\-\d{4}$/,
        /^\d{3}\-\d{2}\-\d{5}$/
    ];
    if ( !partern.test( $('#ContactTel').val() ) ) {
        if ( !contact[0].test( $('#ContactTel').val() )
        && !contact[1].test( $('#ContactTel').val() )
        && !contact[2].test( $('#ContactTel').val() )
        && !contact[3].test( $('#ContactTel').val() ) ) {
            $('.error_inline0').html('電話番号が有効ではない。');
            $('#ContactTel').focus();
            return;
        }
    }

    // Fax
    if ( !partern.test( $('#ContactFax').val() ) ) {
        if ( !contact[0].test( $('#ContactFax').val() )
        && !contact[1].test( $('#ContactFax').val() )
        && !contact[2].test( $('#ContactFax').val() )
        && !contact[3].test( $('#ContactFax').val() ) ) {
            $('.error_inline0').html('ファクス番号が有効ではない。');
            $('#ContactFax').focus();
            return;
        }
    }

    // Seminar Fee
    if ( partern.test( $('#SeminarFees').val() ) ) {
        $('.error_inline0').html('セミナー受講料を入力して下さい。');    
        $('#SeminarFees').focus();
        return false;
    }

    // Product
    if ( partern.test( $('#seProduct').val() ) ) {
        $('.error_inline0').html('対象製品を入力して下さい。');    
        $('#seProduct').focus();
        return false;
    }
    $('.error_inline0').html('');

    let formData = new FormData();
    formData.append('controller', _controller);
    formData.append('SeminarName', $('#SeminarName').val());
    formData.append('AreaId', $('#seArea').val());
    formData.append('VenueName', $('#VenueName').val());
    formData.append('VenueAddress', $('#VenueAddress').val());
    formData.append('VenueMap', $('#VenueMap').val());
    formData.append('VenueStation', $('#VenueStation').val());
    formData.append('TimeStart', $('#TimeStart').val());
    formData.append('TimeEnd', $('#TimeEnd').val());
    formData.append('scDate', $('#scDate').val());
    formData.append('CountPerson', $('#CountPerson').val());
    formData.append('ContactTel', $('#ContactTel').val());
    formData.append('ContactFax', $('#ContactFax').val());
    formData.append('Note', $('#note').val());
    formData.append('Course', $('#seCouse').val());
    formData.append('Company', $('#seCompany').val());
    formData.append('Product', $('#seProduct').val());
    formData.append('SeminarFees', $('#SeminarFees').val().replaceAll(',', ''));

    if ( Number($('#isEdit').val()) == 1 ) {
        formData.append('action', 'edit');
        formData.append('id', $('#SeminarId').val());
    }
    else {
        formData.append('action', 'add');
    }

    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#scLoading').show();
            $('.dialogLoading').show();
        },
        success: function(data) {
            if (typeof data.errMsg != "undefined") {
                $('.error_inline0').html(data.errMsg);
                return;
            }

            $.fancybox.close();
            loadSeminarList();
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続に失敗しました。');
        },
        complete: function () {
            $('.dialogLoading').hide();
            $('#scLoading').hide();
        }
    });
}

// Open dialog
function openDialog(seminarId, isEdit) {
    $('#seArea option:first').prop('selected', true);
    $('#seCouse option:first').prop('selected', true);
    $('#seCompany option:first').prop('selected', true);
    $('#seProduct option:first').prop('selected', true);
    $('#SeminarName').val('');
    $('#VenueName').val('');
    $('#VenueAddress').val('');
    $('#VenueMap').val('');
    $('#TimeStart').val('');
    $('#TimeEnd').val('');
    $('#scDate').val('');
    $('#SeminarFees').val('');
    $('#CountPerson').val('');
    $('#ContactTel').val('');
    $('#ContactFax').val('');
    $('#VenueStation').val('');
    $('#note').val('');
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
                seminarId: Number( seminarId )
            },
            beforeSend: function () {
                $('.dialogLoading').show();
            },
            success: function(data) {
                $('#SeminarId').val(data.SeminarId);
                $('#SeminarName').val(data.SeminarName);
                $('#seArea').val(data.AreaId);
                $('#VenueName').val(data.VenueName);
                $('#VenueAddress').val(data.VenueAddress);
                $('#VenueMap').val(data.VenueMap);
                $('#TimeStart').val(data.TimeStart);
                $('#TimeEnd').val(data.TimeEnd);
                $('#scDate').val(data.Date);
                let monney = numeral(data.SeminarFees).format('0,0');
                $('#SeminarFees').val(monney);
                $('#CountPerson').val(data.CountPerson);
                $('#ContactTel').val(data.ContactTel);
                $('#ContactFax').val(data.ContactFax);
                $('#VenueStation').val(data.VenueStation);
                $('#seCompany').val(data.SeminarClass1);
                $('#seProduct').val(data.SeminarClass2);
                $('#seCouse').val(data.SeminarClass3);
                $('#note').val(data.Note);
                if (data.SampleTaxChk == 1) {
                    $('.searchTax').text('税込');
                }
                else {
                    $('.searchTax').text('税抜き');
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバーへの接続に失敗しました。');
            },
            complete: function () {
                $('.dialogLoading').hide();
                $('#isEdit').val(1);
                $('#SeminarId').val(seminarId);
            }
        });
    }
    else {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: _url_init,
            data: {
                controller: _controller,
                action: 'getSample'
            },
            beforeSend: function () {
                $('.dialogLoading').show();
            },
            success: function(data) {
                $('#SeminarName').val(data.SampleName);
                $('#SeminarFees').val(data.SampleFees);
                if (data.SampleTaxChk == 1) {
                    $('.searchTax').text('税込');
                }
                else {
                    $('.searchTax').text('税抜き');
                }
                let today = new Date();
                $('#scDate').val(today.yyyymmdd());
            },
            error: function(xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバーへの接続に失敗しました。');
            },
            complete: function () {
                $('.dialogLoading').hide();
                $('#isEdit').val(0);
                $('#SeminarId').val('');
            }
        });
    }
}

// Delete seminar
function deleteSeminar() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'text',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'delete',
            seminarId: Number( $('#SeminarId').val() )
        },
        success: function(data) {
            $('.message').html(data);
            afterDelete(1);
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続に失敗しました。');
        }
    });
}

function checkExistAllSeminarA() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'checkExistAll'
        },
        success: function(data) {
            $('.message').html(data.msg);
            $('.btnCenter').html(data.btn);
            $.fancybox({
                'width': 350,
                'height': 70,
                'href': '#confirmBox',
                'closeBtn': false,
                'onCleanup': function() {
                    $("#confirmBox").unwrap();
                }
            });
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続に失敗しました。');
        }
    });
}

function deleteAllSeminarA() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'deleteAll'
        },
        success: function(data) {
            $('.message').html(data.msg);
            if (data.success) {
                $.fancybox.close();
                loadSeminarList();
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        }
    });
}

function uploadSeminarA() {
    let ipEx = $('#ipExcel').val();
    if ( /^\s*$/.test(ipEx) || ipEx == "undefined") {
        $('.message').html("取り込むファイルを選択して下さい。");
        $('#ipExcel').val('');
        $('#inputExcel').html('');
        afterDelete(1);
        $.fancybox({
            'width': 300,
            'height': 70,
            'href': '#confirmBox',
            'closeBtn': false,
            'onCleanup': function() {
                $("#confirmBox").unwrap();
            }
        });
    }
    else {
        let formData = new FormData();
        formData.append('file', $('#ipExcel')[0].files[0]);
        formData.append('action', 'uploadData');
        formData.append('controller', _controller);
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: _url_init,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#scLoading').show();
            },
            success: function(data) {
                let msg;
                if ( typeof data.errMsg != "undefined" ) {
                    msg = data.errMsg;
                }
                else {
                    msg = data.numSuccess + " 行の取り込みに成功しました。  " + data.numFailRows + " 行の取り込みに失敗しました。 </br>";
                    if ( data.numFailRows > 0 ) {
                        msg += "<a href='" + data.errFile + "' target='_blank'>ファイルをダウンロードして詳細な内容をご覧下さい</a>";
                    }
                }
                $('.message').html(msg);
                $('#ipExcel').val('');
                $('#inputExcel').html('');
                afterDelete(1);
                $.fancybox({
                    'width': 370,
                    'height': 140,
                    'closeBtn': false,
                    'href': '#confirmBox',
                    'onCleanup': function() {
                        $("#confirmBox").unwrap();
                    }
                });
                loadSeminarList();
            },
            error: function(xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバーへの接続エラーが発生しました。');
            },
            complete: function() {
                $('#scLoading').hide();
            }
        });
    }
}

function FullSeminarAId(id) {
    $('#SeminarId').val(id);
    jQuery.ajax({
        type: 'POST',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'FullSeminarAId',
            seminarId: id
        },
        beforeSend: function () {
            $('#scLoading').show();
        },
        success: function (data) {
            $.fancybox.close();
            loadSeminarList();
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        },
        complete: function() {
            $('#scLoading').hide();
        }
    });
}
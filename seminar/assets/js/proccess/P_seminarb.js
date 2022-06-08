const _url_init = 'src/index.php';
const _controller = 'seminarb';

Date.prototype.yyyymmdd = function() {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString();
    var dd = this.getDate().toString();
    return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]);
};

// Load Seminar B List
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
            $('.error_inline0').html('サーバーへの接続のエラーであります。');
        },
        complete: function () {
            $('#scLoading').hide();
        }
    });
}

// Save Seminar B
function saveSeminarB() {
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
    if ( partern.test($('#areaId').val()) ) {
        $('.error_inline0').html('地域コードを入力して下さい。');
        $('#areaId').focus();
        return;
    }

    if ( $('#areaId').val().length > 30 ) {
        $('.error_inline0').html('地域コードを30文字以内で入力してください。');
        $('#areaId').focus();
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
        $('.error_inline0').html('開催日は未入力です。');
        $('#scDate').focus();
        return;
    }

    if ( !/^\d{4}-\d{1,2}-\d{1,2}$/.test( $('#scDate').val() ) ) {
        $('.error_inline0').html('日付が有効ではない。');
        $('#scDate').focus();
        return;
    }
    $('.error_inline0').html('');

    let formData = new FormData();
    formData.append('controller', _controller);
    formData.append('SeminarName', $('#SeminarName').val());
    formData.append('AreaId', $('#areaId').val());
    formData.append('VenueName', $('#VenueName').val());
    formData.append('VenueAddress', $('#VenueAddress').val());
    formData.append('VenueMap', $('#VenueMap').val());
    formData.append('TimeStart', $('#TimeStart').val());
    formData.append('TimeEnd', $('#TimeEnd').val());
    formData.append('scDate', $('#scDate').val());
    formData.append('AppDate', $('#year').val() + "-" + $('#month').val() + "-" + $('#day').val() );

    if ( Number($('#isEdit').val()) == 1 ) {
        formData.append('action', 'edit');
        formData.append('id', $('#seminarId').val());
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
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        },
        complete: function () {
            $('.dialogLoading').hide();
            $('#scLoading').hide();
        }
    });
}

// Open dialog
function openDialog(seminarId, isEdit) {
    $('#SeminarName').val('');
    $('#VenueName').val('');
    $('#VenueAddress').val('');
    $('#VenueMap').val('');
    $('#TimeStart').val('');
    $('#TimeEnd').val('');
    $('#scDate').val('');
    $('#areaId option:first').prop('selected', true);
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
                $('#seminarId').val(data.SeminarId);
                $('#SeminarName').val(data.SeminarName);
                $('#VenueName').val(data.VenueName);
                $('#VenueAddress').val(data.VenueAddress);
                $('#VenueMap').val(data.VenueMap);
                $('#TimeStart').val(data.TimeStart);
                $('#TimeEnd').val(data.TimeEnd);
                $('#scDate').val(data.Date);
                $('#areaId').val(data.AreaId);
                let d = new Date(data.AppDate);
                $('#year').val(d.getFullYear());
                $('#month').val(d.getMonth() + 1);
                $('#day').val(d.getDate());
            },
            error: function(xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバへの接続に失敗しました。');
            },
            complete: function () {
                $('.dialogLoading').hide();
                $('#isEdit').val(1);
                $('#SeminarId').val(seminarId);
                $('#seminarId').val(seminarId);
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
                $('#scDate').val(data.today);
                $('#year').val(data.year);
                $('#month').val(data.month);
                $('#day').val(data.day);
            },
            error: function(xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバへの接続に失敗しました。');
            },
            complete: function () {
                $('.dialogLoading').hide();
                $('#isEdit').val(0);
                $('#SeminarId').val('');
                $('#seminarId').val('');
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
            seminarId: Number( $('#seminarId').val() )
        },
        success: function(data) {
            $('.message').html(data);
            afterDelete(1);
        },
        error: function(xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        }
    });
}

function checkExistAllSeminarB() {
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
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        }
    });
}

function deleteAllSeminarB() {
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

function uploadSeminarB() {
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
                $('.error_inline0').html('サーバーへの接続のエラーであります。');
            },
            complete: function() {
                $('#scLoading').hide();
            }
        });
    }
}

function FullSeminarBId(id) {
    $('#SeminarId').val(id);
    $('#seminarId').val(id);
    jQuery.ajax({
        type: 'POST',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'FullSeminarBId',
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
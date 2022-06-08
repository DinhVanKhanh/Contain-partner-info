const _url_init = 'src/index.php';
const _controller = 'seminard';

Date.prototype.yyyymmdd = function() {
    let yyyy = this.getFullYear().toString();
    let mm = (this.getMonth() + 1).toString();
    let dd = this.getDate().toString();
    return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]);
};

// Load Seminar D List
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

// Save Seminar D
function saveSeminarD() {
    let partern = /^\s*$/;
    let partern_checkURL = /((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/;

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

    // AppDate
    if ( partern.test( $('#year').val() )
    || partern.test( $('#month').val() )
    || partern.test( $('#day').val() ) ) {
        $('.error_inline0').html('申込期限が有効ではない。');
        $('#year').focus();
        return;
    }

    if ( /^]d{4}-\d{1,2}\d{1,2}$/.test( $('#year').val() + "-" + $('#month').val() + "-" + $('#day').val() ) ) {
        $('.error_inline0').html('申込期限が有効ではない。');
        $('#year').focus();
        return;
    }

    let now = new Date();
    if ( Number( $('#year').val() ) < (now.getFullYear() - 10) ) {
        $('.error_inline0').html('申込期限が有効ではない。');
        $('#year').focus();
        return;
    }

    if ( $('#month').val().length > 3
    || Number( $('#month').val() ) < 0
    || Number( $('#month').val() ) > 12 ) {
        $('.error_inline0').html('申込期限が有効ではない。');
        $('#month').focus();
        return;
    }

    if ( $('#day').val().length > 3
    || Number( $('#day').val() ) < 0
    || Number( $('#day').val() ) > 31 ) {
        $('.error_inline0').html('申込期限が有効ではない。');
        $('#day').focus();
        return;
    }

    // Compare Date & AppDate
    let da = new Date( $('#scDate').val() );
    let appda = new Date( $('#year').val() + '-' + $('#month').val() + '-' + $('#day').val() );
    if ( da.valueOf() < appda.valueOf() ) {
        $('.error_inline0').html('申込期限は開催日の前の日です。');
        $('#scDate').focus();
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
//　↓↓　＜2020/11/04＞　＜VinhDao＞　＜修正＞
    // let arrFT = $('#TimeStart').val().split(':');
    // let arrTT = $('#TimeEnd').val().split(':');

    // if ( ( arrFT[0] > arrTT[0])
    // || ( arrFT[0] == arrTT[0] && arrFT[1] >= arrTT[1] ) ) {

    if ( compareStartEnd($('#TimeStart').val(), $('#TimeEnd').val()) ) {
//　↑↑　＜2020/11/04＞　＜VinhDao＞　＜修正＞
        $('.error_inline0').html('終了時間が有効ではない。');
        $('#TimeEnd').focus();
        return;
    }

    // Company
    if ( partern.test( $('#CompanyName').val() ) ) {
        $('.error_inline0').html('スクールを入力して下さい。');
        $('#CompanyName').focus();
        return;
    }

    if ( $('#CompanyName').length > 50 ) {
        $('.error_inline0').html('スクールを50文字以内で入力してください。');
        $('#CompanyName').focus();
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

    //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
    // SeminarFees
    // if (partern.test($('#SeminarFees').val())) {
    //     $('.error_inline0').html('価格1を入力して下さい。');
    //     $('#SeminarFees').focus();
    //     return;
    // }
    // // 主催者URL
    // if (partern.test($('#OrganizerURL').val())) {
    //     $('.error_inline0').html('主催者URLを入力して下さい。');
    //     $('#OrganizerURL').focus();
    //     return;
    // }

    // if (!partern_checkURL.test($('#OrganizerURL').val())) {
    //     $('.error_inline0').html('主催者URLを入力して下さい。');
    //     $('#OrganizerURL').focus();
    //     return;
    // }
    //↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>


    // Note
    if ( $('#note').length > 500 ) {
        $('.error_inline0').html('開催会場住所を500文字以内で入力してください。');
        $('#note').focus();
        return;
    }

//　↓↓　＜2020/09/22＞　＜VinhDao＞　＜No.4のHP合同PRJ-info_seminar-Check_200922＞
    // let contact = [
    //     /^\d{2}\-\d{3}\-\d{5}$/,
    //     /^\d{2}\-\d{4}\-\d{4}$/,
    //     /^\d{3}\-\d{3}\-\d{4}$/,
    //     /^\d{3}\-\d{2}\-\d{5}$/
    // ];

    // Tel
    // if ( !partern.test( $('#ContactTel').val() ) ) {
    //     if ( !contact[0].test( $('#ContactTel').val() )
    //     && !contact[1].test( $('#ContactTel').val() )
    //     && !contact[2].test( $('#ContactTel').val() )
    //     && !contact[3].test( $('#ContactTel').val() ) ) {
    //         $('.error_inline0').html('電話番号が有効ではない。');
    //         $('#ContactTel').focus();
    //         return;
    //     }
    // }

    // Fax
    // if ( !partern.test( $('#ContactFax').val() ) ) {
    //     if ( !contact[0].test( $('#ContactFax').val() )
    //     && !contact[1].test( $('#ContactFax').val() )
    //     && !contact[2].test( $('#ContactFax').val() )
    //     && !contact[3].test( $('#ContactFax').val() ) ) {
    //         $('.error_inline0').html('ファクス番号が有効ではない。');
    //         $('#ContactFax').focus();
    //         return;
    //     }
    // }

    // Tel
    if ( !partern.test( $('#ContactTel').val() ) ) {
        let tel = $('#ContactTel').val();
        tel = tel.replace(/-+/g, '');
        if ( tel.length < 10 || tel.length > 11 ) {
            $('.error_inline0').html('電話番号が有効ではない。');
            $('#ContactTel').focus();
            return;
        }
    }

    // Fax
    if ( !partern.test( $('#ContactFax').val() ) ) {
        let fax = $('#ContactFax').val();
        fax = fax.replace(/-+/g, '');
        if ( fax.length < 10 || fax.length > 11 ) {
            $('.error_inline0').html('ファクス番号が有効ではない。');
            $('#ContactFax').focus();
            return;
        }
    }
//　↑↑　＜2020/09/22＞　＜VinhDao＞　＜No.4のHP合同PRJ-info_seminar-Check_200922＞

    // PDF
    if ( typeof $('#ipPdf')[0].files[0] != "undefined" ) {
        let fileName = $('#ipPdf')[0].files[0].name;
        let ext = fileName.substr( fileName.lastIndexOf('.') + 1 );
        if ( ext.toLowerCase() != 'pdf' ) {
            $('.error_inline0').html('PDFファイルが有効ではない。');
            $('#inputPdf').focus();
            return;
        }

        if ( $('#ipPdf')[0].files[0].size > 2000000 ) {
            $('.error_inline0').html('ファイルサイズの制限を超えました。');
            $('#inputPdf').focus();
            return;
        }
    }
    $('.error_inline0').html('');

    let formData = new FormData();
    formData.append('controller', _controller);
    formData.append('file', $('#ipPdf')[0].files[0]);
    formData.append('SeminarName', $('#SeminarName').val());
    formData.append('Todouhuken', $('#seArea').val());
    formData.append('CompanyName', $('#CompanyName').val());
    formData.append('VenueName', $('#VenueName').val());
    formData.append('VenueAddress', $('#VenueAddress').val());
    formData.append('TimeStart', $('#TimeStart').val());
    formData.append('TimeEnd', $('#TimeEnd').val());
    formData.append('scDate', $('#scDate').val());
    formData.append('CountPerson', $('#CountPerson').val());
    //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
    formData.append('SeminarFees', $('#SeminarFees').val());
    formData.append('SeminarFees2Member', $('#SeminarFees2Member').val());
    formData.append('SeminarType', $('input[name="SeminarType"]:checked').val());
    formData.append('OrganizerURL', $('#OrganizerURL').val());
    //↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
    formData.append('ContactTel', $('#ContactTel').val());
    formData.append('ContactFax', $('#ContactFax').val());
    formData.append('curPdf', $('#inputPdf').val());
    formData.append('Note', $('#note').val());

    let day = Number( $('#day').val() ) < 10 ? '0' + Number( $('#day').val() ) : Number( $('#day').val() );
    let month = Number( $('#month').val() ) < 10 ? '0' + Number( $('#month').val() ) : Number( $('#month').val() );
    formData.append('AppDate', $('#year').val() + "-" + month + "-" + day);
    if ( Number($('#isEdit').val()) == 1 ) {
        formData.append('action', 'edit');
        formData.append('oldPdf', $('#oldPdf').val());
        formData.append('deletePdf', $('#deletePdf').val());
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
        success: function (data) {
            if (typeof data.errMsg != "undefined") {
                $('.error_inline0').html(data.errMsg);
                return;
            }
            $.fancybox.close();
            loadSeminarList();
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続のエラーであります。');
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
    $('#seArea option:selected').removeAttr('selected');
    $('#CompanyName').val('');
    $('#VenueName').val('');
    $('#VenueAddress').val('');
    $('#TimeStart').val('');
    $('#TimeEnd').val('');
    $('#scDate').val('');
    $('#CountPerson').val('');
    //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
    $('#SeminarFees').val('');
    $('#SeminarFees2Member').val('');
    $('#OrganizerURL').val('');
    //↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
    $('#ContactTel').val('');
    $('#ContactFax').val('');
    $('#inputPdf').val('');
    $('#ipPdf').val('');
    $('#note').val('');
    $('#year').val('');
    $('#month').val('');
    $('#day').val('');
    $('#deletePdf').val(0);
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
                    $('#seArea').val(data.TodouhukenId);
                    $('#CompanyName').val(data.CompanyName);
                    $('#VenueName').val(data.VenueName);
                    $('#VenueAddress').val(data.VenueAddress);
                    $('#TimeStart').val(data.TimeStart);
                    $('#TimeEnd').val(data.TimeEnd);
                    $('#CountPerson').val(data.CountPerson);
                    //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
                    $('#SeminarFees').val(data.SeminarFees);
                    $('#SeminarFees2Member').val(data.SeminarFees2Member);
                    let id_seminar_type = (data.SeminarType == "オンライン") ? "SeminarTypeOnline" : "SeminarTypeOffline";
                    $('#' + id_seminar_type).prop('checked', true);
                    $('#OrganizerURL').val(data.OrganizerURL);
                    //↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
                    $('#ContactTel').val(data.ContactTel);
                    $('#ContactFax').val(data.ContactFax);
                    $('#note').val(data.Note);
                    $('#scDate').val(data.Date);

                    let date1 = data.AppDate.split('-');
                    $('#year').val(date1[0]);
                    $('#month').val(date1[1]);
                    $('#day').val(date1[2]);

                    if ( data.PDF != "" && data.PDF != "undefined" ) {
                        $('#dlgPdf').css('display', 'inline-block');
                        $('#inputPdf').val(data.PDF);
                        $('#oldPdf').val(data.PDF);
                    }
                    else {
                        $('#dlgPdf').hide();
                        $('#pdfUrl').attr('href', '');
                        $('#oldPdf').val('');
                        $('#inputPdf').val('');
                    }
            },
            error: function(xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバへの接続に失敗しました。');
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
                $('#SeminarName').val(data.sampleName);
                $('#seArea').html(data.todouhukenOption);
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
                $('#dlgPdf').hide();
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
            $('.error_inline0').html('サーバへの接続に失敗しました。');
        }
    });
}

function uploadSeminarD() {
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
            'onCleanup': function () {
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
                        'onCleanup': function () {
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

function checkExistAllSeminarD() {
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


function deleteAllSeminarD() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'deleteAll'
        },
        success: function(data) {
            console.log(data);
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

function FullSeminarDId(id) {
    $('#SeminarId').val(id);
    jQuery.ajax({
        type: 'POST',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'FullSeminarDId',
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


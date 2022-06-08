const _url_init = 'src/index.php';
const _controller = 'schedule';
const _prefix = 'DEMO_';

/**
 * Load list schedules
**/
var scheduleIdMap = new HashMap();

function loadScheduleList() {
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

// Checkbox for grid
function checkBoxPromp(ckId) {
    if ($('#' + ckId).is(':checked')) {
        scheduleIdMap.put(ckId, ckId);
    } else {
        scheduleIdMap.remove(ckId);
    }
}

// Upload schedules
function uploadSchedules() {
    let ipEx = $('#ipExcel').val();
    if (/^\s*$/.test(ipEx) || ipEx == "undefined") {
        $('#submit_del').hide();
        $('#btnCloseFc').hide();
        $('#submit_del_ok').show();
        $('.message').html("取り込むファイルを選択して下さい。");
        $('#ipExcel').val('');
        $('#inputExcel').html('');
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
        formData.append('controller', _controller);
        formData.append('action', 'uploadData');
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
            success: function (data) {
                let msg;
                if ( typeof data.errMsg != "undefined" ) {
                    msg = data.errMsg;
                }
                else {
                    $('#area').val(-1);
                    msg = data.numSuccess + " 行の取り込みに成功しました。  " + data.numFailRows + " 行の取り込みに失敗しました。 </br>";
                    if (data.numFailRows > 0) {
                        msg += "<a href='" + data.errFile + "' target='_blank'>ファイルをダウンロードして詳細な内容をご覧下さい</a>";
                    }
                }

                $('#submit_del').hide();
                $('#btnCloseFc').hide();
                $('#submit_del_ok').show();
                $('.message').html(msg);
                $('#ipExcel').val('');
                $('#inputExcel').html('');
                $.fancybox({
                    'width': 370,
                    'height': 140,
                    'closeBtn': false,
                    'href': '#confirmBox',
                    'onCleanup': function () {
                        $("#confirmBox").unwrap();
                    }
                });
            },
            error: function (xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバーへの接続のエラーであります。');
            },
            complete: function () {
                $('#scLoading').hide();
            }
        });
    }
}

// Add schedule
function addSchedule( formData ) {
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
            if ( typeof data.errMsg != "undefined" ) {
                $('.error_inline0').html( data.errMsg );
                return;
            }

            $.fancybox.close();
            filterScheduleByArea();
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続のエラーであります');
        },
		complete: function () {
			$('.dialogLoading').hide();
		}
    });
}

// Edit schedule
function editSchedule( formData ) {
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
            if ( typeof data.errMsg != "undefined" ) {
                $('.error_inline0').html( data.errMsg );
                return;
            }
            $.fancybox.close();
            filterScheduleByArea();
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続のエラーであります');
        },
		complete: function () {
			$('.dialogLoading').hide();
		}
    });
}

// Save Schedule
function saveSchedule() {
    $('.error_inline0').html('');
    let scDate   = $('#scDate').val();
    let fromTime = $('#scFromTime').val();
    let toTime   = $('#scToTime').val();

    let error = function ( element, message ) {
        $(element).focus();
        $('.error_inline0').html( message );
    }

    let partern = /^(\s|\D)+$/;

    if ( partern.test( $('#shopId').val() ) ) {
        error( '#shopId', '販売店を入力して下さい。' );
        return;
    }

    if ( partern.test( scDate ) ) {
        error( '#scDate', '日付を入力して下さい。' );
        return;
    }
    
    if ( partern.test( fromTime ) ) {
        error( '#scFromTime', '開始時間を入力して下さい。' );
        return;
    }

    if ( partern.test( toTime ) ) {
        error( '#scToTime', '終了時間を入力して下さい。' );
        return;
    }

    // Validate time
    let arrFT = fromTime.split(':');
    let arrTT = toTime.split(':');

    if ( ( arrFT[0] == arrTT[0] && arrFT[1] == arrTT[1] ) || (arrFT[0] > arrTT[0]) ) {
        error( '#scToTime', '終了時間が有効ではない。' );
        return;
    }

    if ( !( /\d{4}-\d{2}-\d{2}/.test( scDate ) ) ) {
        error( '#scDate', '日付を入力して下さい。' );
        return;
    }

    let bits = new Date( scDate );
    if ( ( bits[1] < 1 && bits[1] > 12 )
    || ( bits[2] < 1 && bits[2] > 31 ) ) {
        error( '#scDate', '無効な日付' );
        return;
    }

    if ( $('#scDescript').val().length > 500 ) {
        error( '#scDescript', '開催会場住所を500文字以内で入力してください。' );
        return;
    }

    let formData = new FormData();
    formData.append('controller', _controller);
    formData.append('shopId', Number( $('#shopId').val() ));
    formData.append('mtId', Number( $('#mtPlace').val() ));
    formData.append('scDate', scDate);
    formData.append('scFTime', fromTime);
    formData.append('scTTime', toTime);
    formData.append('scDescript', $('#scDescript').val());
    formData.append('isActive', $('#scIsActive').is(':checked') ? 1 : 0);
    formData.append('isHighLight', $('#scIsHighLight').is(':checked') ? 1 : 0);
    formData.append('oldPdf', $('#oldPdf').val());

    // console.log(formData.entries());


    if ( typeof $('#ipPdf')[0].files[0] != "undefined" ) {
        formData.append('file', $('#ipPdf')[0].files[0]);
        formData.append('curPdf', $('#ipPdf')[0].files[0].name);
    }

    if ( Number($('#isEdit').val()) == 1 ) {
        formData.append('action', 'edit');
        formData.append('id', Number( $('#scId').val() ));
        editSchedule(formData);
    }
    else {
        formData.append('action', 'add');
        addSchedule(formData);
    }
}

// Open schedule dialog
function openDialog( schId, isEdit ) {
    resetDataDialog();
    $('.dialogLoading').hide();

    if ( Number( isEdit ) == 1 ) {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: _url_init,
            data: {
                controller: _controller,
                action: 'loadById',
                schId: schId
            },
            success: function (data) {
                let schedule = data.schedule;
                if ( data.isSpecialShop == 0 ) {
                    getListNormalShop( schedule.ShopId );
                    $('#ckSpecial').prop( 'checked', false );
                }
                else {
                    getListSpecialShop( schedule.ShopId );
                    $('#ckSpecial').prop( 'checked', true );
                }
                $('#mtPlace').val( schedule.MeetingPlaceId );

                $('#scDate').val( schedule.Date );
                $('#scFromTime').val( schedule.TimeFrom );
                $('#scToTime').val( schedule.TimeTo );
                $('#scDescript').val( schedule.Description );

                $('#scIsActive').prop( 'checked', schedule.IsActive == 1 ? true : false );
                $('#scIsHighLight').prop( 'checked', schedule.IsHighlight == 1 ? true : false );

                if ( typeof schedule.Pdf == "string" && !/^\s*$/.test(schedule.Pdf) ) {
                    $('#dlgPdf').show();
                    $('#pdfUrl').attr( 'href', data.servUrl + _prefix + schedule.Pdf );
                    $('#inputPdf').val( schedule.Pdf );
                    $('#oldPdf').val( schedule.Pdf );
                }
                else {
                    $('#dlgPdf').hide();
                    $('#pdfUrl').attr('href', '');
                    $('#oldPdf').val('');
                    $('#inputPdf').val('');
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバーへの接続のエラーであります。');
            }
        });

        $('#isEdit').val(1);
        $('#scId').val(schId);
    }
    else {
        getListNormalShop(null);
        $('#ckSpecial').prop('checked', false);
        $('#dlgPdf').hide();
        $('#isEdit').val(0);
        $('#scId').val('');
    }
}

// Delete schedule
function deleteSchedule() {
    let map = scheduleIdMap.valArray.toString();
    if ( /^\s+$/.test( map ) ) {
        $('.error_inline0').html('削除するデータを選択します。');
        return;
    }

    jQuery.ajax({
        type: 'POST',
        dataType: 'text',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'delete',
            idList: scheduleIdMap.valArray.toString()
        },
        success: function (data) {
            $('#submit_del_ok').show();
			$('#submit_del').hide();
			$('.btnClose').hide();
			$('.message').html(data);
            scheduleIdMap.valArray.length = 0;
            scheduleIdMap.keyArray.length = 0;
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続のエラーであります。');
        }
    });
}

// Filter schedule by area
function filterScheduleByArea() {
    let areaId = $('#area').val();
    if (areaId >= 0) {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: _url_init,
            data: {
                controller: _controller,
                action: 'filterByArea',
                areaId: areaId
            },
            beforeSend: function () {
                $('#scLoading').show();
            },
            success: function (data) {
                if ( typeof data.errMsg != "undefined" ) {
                    $('.error_inline0').html(data.errMsg);
                    return;
                }
                $('#tableContent').html( data.view );
            },
            error: function (xhr, textStatus, errorThrown) {
                $('.error_inline0').html('サーバーへの接続のエラーであります');
            },
            complete: function () {
                $('#scLoading').hide();
            }
        });
    }
    else {
        loadScheduleList();
    }
}

/**
 * Get list special shops and set select by id
 * @return Combobox
 */
function getListSpecialShop( activeId ) {
    $('#shopId option').remove();
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'getListSpecialShop',
        },
        success: function (data) {
                let str = "";
                data.forEach( function ( item ) {
                    str += "<option value='" + item.ShopId + "'>" + item.Name + "</option>";
                });

                $('#shopId').html( str );

                if ( activeId == null ) {
                    $('#shopId').val($("#shopId option:first").val());
                }
                else {
                    $('#shopId').val(activeId);
                }
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_load_to').html('サーバーへの接続のエラーであります');
            $('.loading').addClass('hide');
        }
    });
}

/**
 * Get list normal shops and set select by id
 * @return Combobox
 **/
function getListNormalShop( activeId ) {
    $('#shopId option').remove();
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'getListNormalShop',
        },
        success: function (data) {
            let str = "";
            data.forEach( function ( item ) {
                str += "<option value='" + item.ShopId + "'>" + item.Name + "</option>";
            });
            $('#shopId').html( str );

            if ( activeId == null ) {
                $('#shopId').val( $("#shopId option:first").val() );
            }
            else {
                $('#shopId').val( activeId );
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_load_to').html('サーバーへの接続のエラーであります');
            $('.loading').addClass('hide');
        }
    });
}

function resetDataDialog() {
    $("#mtPlace option:first").prop('selected',true);
    $('#scDate').val('');
    $('#scFromTime').val('');
    $('#scToTime').val('');
    $('#scDescript').val('');
    $('#inputPdf').val('');
    $('#ipPdf').val('');
    $('.error_inline0').html('');
    $('#scIsActive').prop('checked', false);
    $('#scIsHighLight').prop('checked', false);
    $('#dlgPdf').hide();
}
$(document).ready(function() {
    $("#scDate").keydown(function(e) {
        // Allow: backspace, delete, tab, escape, enter, ctrl+A and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173, 109]) !== -1 ||
            // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39) ||
            // Allow: number 0-9
            (e.keyCode >= 96 && e.keyCode <= 105) ||

            (e.keyCode >= 48 && e.keyCode <= 57)) {

        } else {
            e.preventDefault();
        }
    });

    $('#scDate').mask('9999-99-99');

    $('#mnSchedule').css('color', '#cc3300');

    $('#scFromTime').mask('99:99');
    $('#scToTime').mask('99:99');

    $('#scFromTime').timepicker();
    $('#scToTime').timepicker();
    $('#datepair .time').timepicker({
        'showDuration': true,
        'timeFormat': 'H:i'
    });

    var basicExampleEl = document.getElementById('datepair');
    var datepair = new Datepair(basicExampleEl);

    $('#scDate').datepicker();
    $("#scDate").datepicker("option", "dateFormat", 'yy-mm-dd');

    $('.fancyboxSC').fancybox({
        width: 900,
        height: 650
    });

    getListNormalShop(null);

    //load list schedule
    loadScheduleList();

    $('#submit_ud').click(function() {
        saveSchedule();
    });

    $('#btnDel').click(function() {
        if (scheduleIdMap.valArray.length == 0) {
            $('#submit_del').hide();
            $('#submit_del_ok').show();
            $('#btnCloseFc').hide();
            $('.message').html('削除するデータを選択してください');
            $.fancybox({
                'width': 300,
                'height': 70,
                'href': '#confirmBox',
                'closeBtn': false,
                'onCleanup': function() {
                    $("#confirmBox").unwrap();
                }
            }); //fancybox

        }
        else {
            $('#submit_del').show();
            $('#submit_del_ok').hide();
            $('#btnCloseFc').show();
            $('.message').html('店頭デモを削除してよろしいでしょうか？');
            $.fancybox({
                'width': 300,
                'height': 100,
                'href': '#confirmBox',
                'closeBtn': false,
                'onCleanup': function() {
                    $("#confirmBox").unwrap();
                }
            }); //fancybox
        }
    });

    $('#submit_del').click(function() {
        deleteSchedule();
    });

    $('#submit_del_ok').click(function () {
        $.fancybox.close();
        setTimeout(() => {
            $('.message').html('地区を削除してよろしいでしょうか？');
            $('#submit_del_ok').hide();
            $('#submit_del').show();
            $('.btnClose').show();
        }, 500);
        filterScheduleByArea();
    });

    $('#btnImport').click(function() {
        uploadSchedules();
    });

    //ckSpecial
    $("#ckSpecial").change(function() {
        if (this.checked) {
            getListSpecialShop(null);
        }
        else {
            getListNormalShop(null);
        }
    });

    $('#btnPdfDel').click(function () {
        $('#inputPdf').val('');
        $('#ipPdf')[0].files[0] = undefined;
        $('#pdfUrl').attr('href','#');
    });
});

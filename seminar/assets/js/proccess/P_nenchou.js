const _url_init = '../src/index.php';
const _controller = 'seminard';

function loadSemianrDClientList() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'loadClientList',
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

//↓↓　<2021/08/31> <VanKhanh> <show list area>
function loadSemianrDAreaList() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'loadAreaList',
        },
        beforeSend: function () {
            $('#scLoading').show();
        },
        success: function (data) {
            $('#tableAra').html(data);

        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続のエラーであります。');
        },
        complete: function () {
            $('#scLoading').hide();
        }
    });
}

let getArea = function (AreaCode,obj) {
    // alert("getArea");
    $(obj).siblings().children().removeClass('active');
    $(obj).children().addClass('active');
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'loadArea',
            AreaCode: AreaCode,
        },
        beforeSend: function () {
            $('#scLoading').show();
        },
        success: function (data) {
            $('#tableContent').html(data.view);
            // console.log(data.view);
        },
        error: function (xhr, textStatus, errorThrown) {
            $('.error_inline0').html('サーバーへの接続のエラーであります。');
        },
        complete: function () {
            $('#scLoading').hide();
        }
    });
}
//↑↑　<2021/08/31> <VanKhanh> <show list area>


function openpopup(url, w, h) {
    let dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    let dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
    let width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    let height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
    let left = ((width / 2) - (w / 2)) + dualScreenLeft;
    let top = ((height / 2) - (h / 2)) + dualScreenTop;
    let newWindow = window.open(url, null, "scrollbars=yes,width=" + w + ",height=" + h + ",status=yes,toolbar=no,menubar=no,location=no,left=" + left + ",top=" + top);
    if (window.focus) {
        newWindow.focus();
    }
}

function showAttendSeminarD(SeminarId) {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'showAttendSeminarD',
            SeminarId: SeminarId
        },
        success: function(data) {
            $('.showSeminar').html(data.view);
        },
        error: function(xhr, textStatus, errorThrown) {
            // let err = eval("(" + xhr.responseText + ")");
            // alert(err.Message);
        }
    });
}

function sendClientMailD(SeminarId) {
    $('#scLoading').show();
    let user_company = $("input[name=user_company]").val();
    let user_name = $("input[name=user_name]").val();
    let user_email = $("input[name=user_email]").val();
    let user_postcode1 = $("input[name=user_postcode1]").val();
    let user_postcode2 = $("input[name=user_postcode2]").val();
    let user_address = $("input[name=user_address]").val();
    let user_tel = $("input[name=user_tel]").val();
    let user_fax = $("input[name=user_fax]").val();
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'sendClientMailD',
            SeminarId: SeminarId,
            user_company: user_company,
            user_name: user_name,
            user_email: user_email,
            user_postcode1: user_postcode1,
            user_postcode2: user_postcode2,
            user_address: user_address,
            user_tel: user_tel,
            user_fax: user_fax
        },
        beforeSend: function () {
            $('#scLoading').show();
        },
        success: function(data) {
            if (data.success == false) {
                alert("エラーが発生しました。");
            }
            else {
                document.inputform.submit();
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            // let err = eval("(" + xhr.responseText + ")");
            // alert(textStatus + " " + errorThrown);
        },
        complete: function () {
            $('#scLoading').hide();
        }
    });
}
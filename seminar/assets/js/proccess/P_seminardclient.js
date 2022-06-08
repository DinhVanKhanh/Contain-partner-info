const _url_init = 'src/index.php';
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

function openpopup(url, w, h) {
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, null, "scrollbars=yes,width=" + w + ",height=" + h + ",status=yes,toolbar=no,menubar=no,location=no,left=" + left + ",top=" + top);
    if (window.focus) {
        newWindow.focus();
    }
}
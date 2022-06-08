function bindInputCodeData(inputtag) {
    $("#" + inputtag).on("keydown", function (event) {
        let arr = [8, 9, 16, 17, 20, 35, 36, 37, 38, 39, 40, 45, 46, 109, 173];
        for (let i = 65; i <= 90; i++) {
            arr.push(i);
        }

        for (let i = 96; i <= 105; i++) {
            arr.push(i);
        }

        for (let i = 48; i <= 57; i++) {
            arr.push(i);
        }

        if (inputtag == 'SeminarName' || inputtag == 'CompanyName' || inputtag == 'SampleName' || inputtag == 'VenueName' || inputtag == 'VenueAddress' || inputtag == 'VenueStation') {
            arr.push(32);
        }
        arr.push(220);

        if (jQuery.inArray(event.which, arr) === -1) {
            event.preventDefault();
        }
    });

    let regexp = /[&^%$#@!]/g;
    $("#" + inputtag).on("input", function () {
        if ($(this).val().match(regexp)) {
            $(this).val($(this).val().replace(regexp, ''));
        }
    });

    $("#" + inputtag).on("blur", function () {
        $(this).val($(this).val().trim());
    });
}

function bindInputCodeDataClass(inputtag) {
    $("." + inputtag).on("keydown", function (event) {
        let arr = [8, 9, 16, 17, 20, 35, 36, 37, 38, 39, 40, 45, 46, 109, 173];
        for (let i = 65; i <= 90; i++) {
            arr.push(i);
        }
        for (let i = 96; i <= 105; i++) {
            arr.push(i);
        }
        for (let i = 48; i <= 57; i++) {
            arr.push(i);
        }
        if (inputtag == 'html_editor') {
            arr.push(32);
        }
        arr.push(220);
        if (jQuery.inArray(event.which, arr) === -1) {
            event.preventDefault();
        }
    });

    $("." + inputtag).on("input", function () {
        let regexp = /[&^_%$#@!]/g;
        if ($(this).val().match(regexp)) {
            $(this).val($(this).val().replace(regexp, ''));
        }
    });

    $(".editor").on("blur", function () {
        $(this).text($(this).text().trim());
    });
}

function encrypt(string) {
    let text = btoa('qweadszxc') + btoa(string);
    return text;
}

var contact = [8, 46, 9, 27, 109, 173, 13];
var number  = [46, 8, 9, 27, 13, 110, 190, 173, 109];

// Format contact
function bindInputContact(inputtag, keys) {
    $("#" + inputtag).keydown(function (e) {
        var ctrlKey = 17,
            vKey = 86,
            cKey = 67;
        // Allow: backspace, delete, tab, escape, enter, ctrl+A,left -, right -, and .
        if ($.inArray(e.keyCode, keys) !== -1 ||
            // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39) ||
            // Allow: number 0-9: numpad
            (e.keyCode >= 96 && e.keyCode <= 105) ||
            // Allow: ctrl c, ctrlv, ctrl a
            ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: number 0-9 main key
            (e.keyCode >= 48 && e.keyCode <= 57)) {
            //do nothing
        }
        else {
            e.preventDefault();
        }
    });
}

// Format date
function bindInputDate(inputtag) {
    $("#" + inputtag).keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173, 109]) !== -1 ||
            // Allow: Ctrl+A
	        (e.keyCode == 65 && e.ctrlKey === true) || 
	         // Allow: home, end, left, right
	        (e.keyCode >= 35 && e.keyCode <= 39) || 
	        // Allow: number 0-9
	        (e.keyCode >= 96 && e.keyCode <= 105) || 
			(e.keyCode >=48 && e.keyCode <= 57)) {
        }
        else {
	    	e.preventDefault();	
		}
    });
}

/**
    * @param task
            default warn
            1 ok
*/
function afterDelete( task ) {
    let content = '';
    switch (task) {
        case 1:
            content = '<a title="Close" id="btnCloseFc" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close(); loadSeminarList();">はい</a>';
            $('.btnCenter').html(content);
            break;

        default:
            $('.message').html('セミナーを削除してよろしいでしょうか？');
            content = '<button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>' +
                    '<a title="Close" id="btnCloseFc" class="btn btnClose" href="javascript:;" onclick=" $.fancybox.close();">いいえ</a> </p>';
            $('.btnCenter').html(content);
            $('#submit_del').click(function() {
                deleteSeminar();
            });     
            break;
    }
}

function appdate() {
    let now = new Date($('#scDate').val());
    let dline = $('#dateHide').val();
    let apdate = new Date(now.getFullYear(), now.getMonth(), now.getDate() - dline);
    $('#year').val(apdate.getFullYear());
    $('#month').val(apdate.getMonth() + 1);
    $('#day').val(apdate.getDate());
}

/**
 * Compare time start and time end
 * @param string time1
 * @param string time2
 * @return bool
 */
function compareStartEnd(start, end) {
    // Start
    let arrStart = start.split(':');
    for ( let i = 0; i < arrStart.length; i++ ) {
        arrStart[i] = parseInt(arrStart[i]);
    }

    // End
    let arrEnd = end.split(':');
    for ( let i = 0; i < arrEnd.length; i++ ) {
        arrEnd[i] = parseInt(arrEnd[i]);
    }

    // Compare
    if ( arrStart[0] > arrEnd[0] || (arrStart[0] == arrEnd[0] && arrStart[1] >= arrEnd[1]) ) {
        return true;
    }
    return false;
}
$(document).ready(function() {
    $('#mnSeminarMaster').css('color','#cc3300');
    bindInputCodeData('SampleName');

    // Load list master
    loadMasterList();

    ChkAlways();
    
    // just enter Fax number by number , not text
    $("#SampleDeadline").keydown(function(e) {
        isNumber(e);
    });

    $('.tableMonth input').on('change', function() {
        ChkAlways();
    });

    $('#ChkSampleAlways').on('change', function() {
        if ($(this).is(':checked')) {
            $('.tableMonth input').prop('checked',true);
        }
        else {
            $('.tableMonth input').prop('checked',false);
        }
    });

    $('input[name=SampleFeesChk]').change(function() {
        if ($(this).val() == 0) {
            $('#SampleFees').attr({'disabled':true}).val('').addClass('disable');
        }
        else {
            $('#SampleFees').attr('disabled',false).removeClass('disable');
        }
    });

    function ChkAlways() {
        let s = $('input[name="SampleAppMonth"]').size();
        let flag = 1;
        for (let i = 0; i < s; i++) {
            if (!$('input[name="SampleAppMonth"]').eq(i).is(':checked')) {
                flag = 0;
                $('#ChkSampleAlways').prop('checked', false);
                break;
            }
        }
    }
});